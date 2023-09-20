<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Cashbook;
use App\Models\Accounts\Account;
use App\Models\Yearly\Parameter;
use Illuminate\Support\Facades\DB;
use  App\Models\GstDocs\GstDocument;
use App\Models\Yearly\TransGstDetail;
use  App\Models\GstDocs\GstDocumentDetail;
use App\Models\Items\Item;
use App\Models\Masters\Branch;
use App\Models\Yearly\Transfer;
use App\Models\Yearly\TransferDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class TransferRequest extends FormRequest
{
    protected $items = [];
    protected $current_branch_id=0;
    protected $branch_acid = 0;
    protected $item_acids = [];
    protected $cash_books = [];
    protected $gen_msg = '';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules =[];
        $rules += [
            'iss_date'                    => 'required|date_format:d-m-Y',
            'branch_id_to'             => 'required|exists:branches,id',
            'status'                    => 'required|in:I,R',
            'vehical_no'      =>       'nullable|max:15',
            'details.*.item_id'          => 'required|exists:items,id',
            'details.*.qty'              => 'required|numeric',
            'details.*.rate'             => 'required|numeric',
        ];

        if($this->branch_id_to == getCurrentBranchId() && $this->status == 'I'){
            $rules += [
                'branch_id_to_msg'=>'required',
            ];
        }

        $tot_wt = 0;
        foreach ($this->details as $key => $value) {
            $tot_wt += ($this->status == 'I' ? $value['weight']:$value['rec_weight']);
        }
        if($this->status == 'I'){
            if(floatval($tot_wt) != floatval($this->net_wt)){
                $rules += [
                    'net_weight_msg'=>'required',
                ];
            }
        }
        else{
            if(floatval($tot_wt) != floatval($this->rec_net_wt)){
                $rules += [
                    'net_weight_msg'=>'required',
                ];
            }
        }

        if($this->status == 'I') {
            $this->branch_acid = ($t = Branch::find($this->branch_id_to)) ? intval($t->account_id):0;
        } else {
            $this->branch_acid = ($t = Branch::find($this->branch_id_from)) ? intval($t->account_id):0;
        }
        $gen_msg = '';
        if($this->branch_acid == 0) {
            $rules += [
                'gen_msg'=>'required',
            ];
            $gen_msg = "Check ! Account in Branch";
        }
        $this->item_acids = [];
        foreach ($this->details as $key => $det) {
            if($this->status == 'I') {
                $item_acid = ($t = Item::find($det['item_id'])) ? intval($t->tsf_sale_ledger_acid):0;
            } else {
                $item_acid = ($t = Item::find($det['item_id'])) ? intval($t->tsf_pur_ledger_acid):0;
            }
            if($item_acid == 0) {
                $rules += [
                    'gen_msg'=>'required',
                ];
                $gen_msg = addString($gen_msg,"Check ! Transfer Account in Item");
            } else {
                if(array_key_exists($item_acid,$this->item_acids)) {
                    $this->item_acids[$item_acid]['amount'] += $det['amount'];
                    $this->item_acids[$item_acid]['weight'] += $det['weight'];
                } else {
                    $this->item_acids[$item_acid] = ['weight' => $det['weight'],'amount' => $det['amount']];
                }
            }
        }
        $this->gen_msg = $gen_msg;
        return $rules;
    }

    public function messages()
    {
        $msgs = [
            'details.*.item_id.required'=>'Item Mandatory!',
            'branch_id_to_msg.required'=>'To branch can not be equal to active Branch',
            'net_weight_msg.required' => "Net weight is Mismatch!",
            'gen_msg.required' => $this->gen_msg,
        ];
        return $msgs;
    }

    public function save()
    {

        $detids = [];
        $cbids = [];
        $gst_details = [];
        $this->current_branch_id = getCurrentBranchId();
        $prefix = '';
        if($this->status == 'I'){
            $prefix = 'issue_transfer';
        }
        else if($this->status == 'R'){
            $prefix = 'receipt_transfer';
        }
        $branch_prefix = validatePrefix($prefix);
        if ($this->form_id > 0) {
            $transfer = Transfer::findOrFail($this->form_id);
            $detids = $transfer->details->pluck('id')->toArray();
            $cbids = $transfer->cash_books->pluck('id')->toArray();
        } else {
            $transfer = new Transfer();
        }
        $transfer->fill($this->all());
        if ($this->form_id == 0) {
            $transfer->vcode = '';
        }
        $s_no = 1;
        $transfer_details =  [];
        foreach ($this->details as $key => $det) {
            $arr = getResultModel($detids, TransferDetail::class,$det);
            $detids = $arr[1];
            $transfer_details[$key] = $arr[0];
            $s_no++;
        }
        $cbids = $this->GetCashBookDetails($cbids);

        DB::connection(getYearlyDbConn())->beginTransaction();
        if (intval($this->form_id) == 0) {
            if($this->status == 'I'){
                $transfer->iss_doc_no = getYearlyNextEntryNumberFront('IT-' . $branch_prefix);
                $transfer->iss_doc_no_print = $branch_prefix  . $transfer->iss_doc_no;
                $transfer->branch_id_from = $this->current_branch_id;
            }
        }
        else{
            if($this->status == 'R'){
                $transfer->rec_doc_no = getYearlyNextEntryNumberFront('RT-' .  $branch_prefix);
                $transfer->rec_doc_no_print = $branch_prefix  . $transfer->rec_doc_no;
            }
        }
        $transfer->save();
        $this->setPart($this->status == 'I' ? $transfer->iss_doc_no_print : $transfer->rec_doc_no_print);
        $transfer->vcode = $this->status.'T' . $transfer->id;
        $transfer->save();
        $transfer->details()->saveMany($transfer_details);
        $transfer->cash_books()->saveMany($this->cash_books);
        TransferDetail::whereIn('id', $detids)->delete();
        Cashbook::whereIn('id',$cbids)->delete();
        DB::connection(getYearlyDbConn())->commit();
        return reply('ok', [
            'success' => true,
            'transfer' => $transfer
        ]);
    }

    private function GetCashBookDetails($cbids)
    {
        $this->cash_books = [];
        $cash_cr = 'T';
        if($this->status == 'I') {
            $bill_no = $this->form_id > 0 ? $this->iss_doc_no_print:'@@@@@';
        } else {
            $bill_no = $this->form_id > 0 ? $this->rec_doc_no_print:'@@@@@';
        }
        $prt = 'Trf Doc No. ' .$bill_no;
        $prt1 = $prt.' '.($this->status == 'I' ? Branch::getBranchName($this->branch_id_to):Branch::getBranchName($this->branch_id_from));
        // vcode, trans_date, due_date, dr_cr, cash_cr, ac_id, branch_id, amount, weight, part
        // dd($this->acid_party);
        if($cash_cr == 'T') {
            $cashbooks[] = ['dr_cr' => 'D', 'ac_id' => $this->branch_acid, 'amount' => $this->amount, 'part' => $prt,'weight' => 0];
        }
        foreach($this->item_acids as $key => $val) {
            $cashbooks[] = ['dr_cr' => ($this->status == 'I') ? 'D':'C', 'ac_id' => $key, 'amount' => $val['amount'], 'part' => $prt1,'weight' => $val['weight']];
        }
        $fixvalues = ['cash_cr' => $cash_cr, 'trans_date' => $this->status == 'I' ? $this->iss_date:$this->rec_date,'branch_id' => getCurrentBranchId()];
        foreach ($cashbooks as $cb) {
            if (round($cb['amount'], 2) != 0) {
                if (round($cb['amount'], 2) < 0) {
                    $cb['amount'] = abs($cb['amount']);
                    $cb['dr_cr'] = ($cb['dr_cr'] == 'D') ? 'C' : 'D';
                }
                $arr = getResultModel($cbids, Cashbook::class, $cb + $fixvalues);
                $cbids = $arr[1];
                $this->cash_books[] = $arr[0];
            }
        }
        return $cbids;
    }

    private function setPart($bill_no) {
        foreach($this->cash_books as $val) {
            $val['vchr_no'] = $bill_no;
            $val['part'] = str_replace('@@@@@', $bill_no,$val['part']);
        }
    }


}
