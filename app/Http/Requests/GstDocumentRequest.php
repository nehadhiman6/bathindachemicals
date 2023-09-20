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
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GstDocumentRequest extends FormRequest
{
    protected $_acname = '';
    protected $items = [];
    protected $current_branch_id=0;
    protected $msg = '';
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
            'l_o_type'                    => 'required|in:L,C',
            'party_doc_date'             => 'nullable|date_format:d-m-Y|required_if:pur_sale_type,P',
            'party_doc_no' => 'nullable|unique:' . getYearlyDbConn() . '.gst_docs,party_doc_no,' .  $this->form_id . ',id,ac_id,' . $this->ac_id,
            'cash_cr'                    => 'required|in:C,R',
            'pur_sale_type'              => 'required|in:S,P',
            'rev_charges'                => 'required|in:Y,N',
            'doc_type'                   => 'required|in:CN,DN,EV',
            'ac_id'                       => 'required|exists:accounts,id',
            'ac_gst_type'                   => 'required',
            'uid' => 'nullable|unique:' . getYearlyDbConn() . '.gst_docs,uid,' . $this->form_id,
            'gst_amt'                    => 'numeric',
            'round_off'                  => 'nullable|numeric',
            'doc_amt'                    => 'required|numeric',
            'details.*.acid_doc'         => 'required|exists:accounts,id',
            'details.*.item_id'          => 'required|exists:items,id',
            'details.*.qty'              => 'required|numeric',
            'details.*.rate'             => 'required|numeric',
            'details.*.disc_prec'        => 'nullable|numeric',
            'details.*.disc_amt'         => 'nullable|numeric',
            'details.*.net_disc'         => 'nullable|numeric',
            'details.*.amount'           => 'required|numeric',
            'details.*.gst_adj_amt'      => 'nullable|numeric',
            'details.*.gst_id'           => 'required|exists:gst,id',
            'details.*.hsn_code'          => 'required',
        ];
        $field = 'doc_date';
        if($this->form_id > 0){
            $gst_doc = GstDocument::find($this->form_id);
            $old_date = $gst_doc->$field;
        }

        $rules += ['doc_date' => "required|date_format:d-m-Y|before_or_equal:" . getFyDate('E') . "|after_or_equal:" . getFyDate('S')];

        if ($this->doc_type != 'EV') {
            $rules += ['gst_reason_id' => 'required_unless:doc_type,EV|exists:gst_reasons,id'];
        }
        if ($this->ref_no) {
            $rules += ['ref_date' => 'required|date_format:d-m-Y'];
        }
        if ($this->tds_per > 0) {
            $rules += ['tds_account' => 'required|exists:accounts,id'];
        }

        if ($this->add_less_other1_amount != 0) {
            $rules += ['acid_add_less_other1' => 'required|exists:accounts,id'];
        }

        if ($this->add_less_other2_amount != 0) {
            $rules += ['acid_add_less_other2' => 'required|exists:accounts,id'];
        }

        if($this->pur_sale_type == 'S'){
            // dd($this->fyear);
            $rules += ['bill_no' => 'required|max:25'];
            $rules += ['fyear' => 'required|min:8|max:8'];
            if($this->fyear){
                $this->msg = checkBillNo($this->bill_no,$this->fyear,$this->ac_id);
                if($this->msg){
                    $rules += ['msg' => 'required'];
                }
            }


        }

        // foreach($this->details as $key => $det){
        //     $item = Item::find($det['item_id']);
        //     if($item->store_item_type  == 'N'){
        //         $rules += [
        //             'details.'.$key.'.weight' => 'required|numeric|min:0.01',
        //         ];

        //     }
        // }



        return $rules;
    }

    public function messages()
    {
        $msgs = [
            'details.*.hsn_code.required' => 'HSN Code field is required.',
            'party_doc_no.unique' => 'Party Document Number is already taken.',
            'msg_date.required' => "You can't modify this Entry",
            'ac_gst_type.required' => 'Please Check GST Type in Account Master !!',
            'pur_store_msg.required' => "Purchase Store id is required Against Capital Goods !!",
            'msg.required' => $this->msg,
        ];
        return $msgs;
    }

    public function save()
    {
        // if (checkParametersStatus() == 'Not OK') {
        //     throw ValidationException::withMessages(['gen_msg' => "Ist Complete Information in Modify Parameters"]);
        // }
        $detids = [];
        $cbids = [];
        $transdetids = [];
        $gst_details = [];
        $this->current_branch_id = getCurrentBranchId();
        $prefix = '';
        if($this->pur_sale_type == 'P' && $this->doc_type == 'DN'){
            $prefix = 'debit_note_purchase';
        }
        else if($this->pur_sale_type == 'P' && $this->doc_type == 'CN'){
            $prefix = 'credit_note_purchase';
        }
        else if($this->pur_sale_type == 'S' && $this->doc_type == 'DN'){
            $prefix = 'debit_note_sale';
        }
        else if($this->pur_sale_type == 'S' && $this->doc_type == 'CN'){
            $prefix = 'credit_note_sale';
        }
        $branch_prefix = validatePrefix($prefix);
        if ($this->form_id > 0) {
            $gst_document = GstDocument::findOrFail($this->form_id);
            $bill = Bill::firstOrNew(['vcode' => $gst_document->vcode]);
            $detids = $gst_document->details->pluck('id')->toArray();
            $cbids = Cashbook::where('vcode', $gst_document->vcode)->pluck('id')->toArray();
            $transdetids = $gst_document->trans_gst_details->pluck('id')->toArray();
        } else {
            $gst_document = new GstDocument();
            $bill = new Bill();
        }

        $gst_document->fill($this->all());
        if ($this->form_id == 0) {
            $gst_document->vcode = '';
        }
        $bill->fill(['trans_date' => $this->doc_date, 'ac_id' => $this->ac_id, 'dr_cr' => $this->doc_type == 'DN' ? 'D' : 'C', 'ref_date' => $this->doc_date, 'amount' => $this->doc_amt,'branch_id' => $this->current_branch_id]);
        $this->_acname = ($acc = Account::find($this->ac_id)) ? $acc->name : '';

        $s_no = 1;
        $gst_document_details =  [];
        foreach ($this->details as $key => $det) {
            $arr = getResultModel($detids, GstDocumentDetail::class, ['s_no' => $s_no] + $det);
            $detids = $arr[1];
            $gst_document_details[$key] = $arr[0];
            $gst_details[$key] = [];
            foreach ($det['trans_gst_details'] as $gstdet) {
                $arr = getResultModel($transdetids, TransGstDetail::class, $gstdet);
                $gst_details[$key][] = $arr[0];
                $transdetids = $arr[1];
            }
            $s_no++;
            // if ($this->stock_entry == 'Y') {
            //     $item = Item::find($det['item_id']);
            //     $this->items[$det['item_id']] = ['stockable' => $item->stockable, 'store_item_type' => $item->store_item_type,'item_id_stock' =>  ((getSoftwareType() == 'L' && intval($item->item_id_stock) != 0) ? intval($item->item_id_stock):$item->id)];
            // }
        }
        $arr = $this->GetCshBookDetails($cbids);
        $cbids = $arr[1];
        $cashbooks = $arr[0];

        DB::connection(getYearlyDbConn())->beginTransaction();
        if (intval($this->form_id) == 0) {
            $gst_document->doc_no = getYearlyNextEntryNumberFront('GD-'.$branch_prefix);
            $gst_document->doc_no_print = $branch_prefix  . $gst_document->doc_no;
            $cashbooks = $this->SetParticulars($cashbooks, ($this->doc_type == 'EV' || $this->pur_sale_type == 'P') ? '' : $gst_document->doc_no_print, $this->_acname);
            // $this->SetStockParticulars(($this->doc_type == 'EV' || $this->pur_sale_type == 'P') ? $gst_document->party_doc_no : $gst_document->doc_no, $this->_acname);
        }

        $gst_document->save();
        $gst_document->vcode = 'GD' . $gst_document->id;
        $gst_document->branch_id = $this->current_branch_id;
        $gst_document->save();
        $bill->fill(['vcode' => $gst_document->vcode, 'ref' => $gst_document->vcode]);
        $bill->ref_key = '';
        $bill->save();
        foreach ($gst_document_details as $key => $docdet) {
            $docdet->doc_id = $gst_document->id;
            $docdet->save();
            foreach ($gst_details[$key] as $gstdet) {
                $gstdet->fill(['vcode' => $gst_document->vcode, 'det_id' => $docdet->id,'branch_id' =>$this->current_branch_id]);
                $gstdet->save();
            }
        }
        $gst_document->cashbooks()->saveMany($cashbooks);
        Cashbook::whereIn('id', $cbids)->delete();
        TransGstDetail::whereIn('id', $transdetids)->delete();
        GstDocumentDetail::whereIn('id', $detids)->delete();
        DB::connection(getYearlyDbConn())->commit();
        return reply('ok', [
            'success' => true,
            'gstdocument' => $gst_document
        ]);
    }

    private function GetCshBookDetails($cbids)
    {
        $cashbooks = [];
        $cbooks = [];
        $part = $this->getFixPart($this->doc_no_print);
        if ($this->doc_type == 'DN') {
            $drcr = 'D';
            $drcr1 = 'C';
        } else {
            $drcr = 'C';
            $drcr1 = 'D';
        }
        // $part1 = $part . ' ' . $this->_acname;
        $part1 = $this->_acname. ' ' .$part;

        $cashcr = $this->cash_cr;
        // 'vcode','trans_date','dr_cr','cash_cr','acid','amount','part'
        if ($this->cash_cr == 'R') {
            $cashcr = 'T';
            $cashbooks[] = ['dr_cr' => $drcr, 'ac_id' => $this->ac_id,'branch_id' => $this->current_branch_id, 'amount' => $this->doc_amt, 'part' => trim($part . ' ' . $this->remarks)];
            $cashbooks[] = ['dr_cr' => $drcr1, 'ac_id' => $this->ac_id,'branch_id' => $this->current_branch_id, 'amount' => $this->tds_amount, 'part' => $part . ' Tds @' . $this->tds_per . '% on ' . $this->tds_on. ' ' . $this->remarks];
        }
        $det_amounts = [];
        $gst_amounts = [];
        foreach ($this->details as $det) {
            if (!array_key_exists($det['acid_doc'], $det_amounts)) {
                $det_amounts[$det['acid_doc']] = 0;
            }
            $det_amounts[$det['acid_doc']] += $det['net_amt'] - ($this->rev_charges == 'N' ? $det['gst_amt'] : 0);
            // $gst_amounts = setTransGstDet($det['trans_gst_details'], $gst_amounts);
        }
        foreach ($det_amounts as $key => $amt) {
            $cashbooks[] = ['dr_cr' => $drcr1, 'ac_id' => $key,'branch_id' => $this->current_branch_id, 'amount' => $amt, 'part' => $part1. ' ' . $this->remarks];
        }
        $cashbooks[] = ['dr_cr' => $drcr, 'ac_id' => $this->tds_account,'branch_id' => $this->current_branch_id, 'amount' => $this->tds_amount, 'part' => $part1. ' ' . $this->remarks];
        $cashbooks[] = ['dr_cr' => $drcr1,'branch_id' => $this->current_branch_id, 'ac_id' => Parameter::getParaValue('acid_round_off'), 'amount' => $this->round_off, 'part' => $part1. ' ' . $this->remarks];
        if ($this->rev_charges == 'N') {
            foreach ($gst_amounts as $key => $val) {
                $cashbooks[] = ['dr_cr' => $drcr1, 'ac_id' => $key,'branch_id' => $this->current_branch_id, 'amount' => $val, 'part' => $part1. ' ' . $this->remarks];
            }
        }
        $fixvalues = ['cash_cr' => $cashcr, 'trans_date' => $this->doc_date,'vchr_no' => $this->doc_no_print];
        foreach ($cashbooks as $cb) {
            if (floatval($cb['amount']) != 0) {
                if (floatval($cb['amount']) < 0) {
                    $cb['amount'] = abs($cb['amount']);
                    $cb['dr_cr'] = ($cb['dr_cr'] == 'D') ? 'C' : 'D';
                }
                $arr = getResultModel($cbids, Cashbook::class, $cb + $fixvalues);
                $cbids = $arr[1];
                $cbooks[] = $arr[0];
            }
        }
        return [$cbooks, $cbids];
    }

    private function SetParticulars($data, $bill_no, $party)
    {
        $part = $this->getFixPart($bill_no);
        $part .= $bill_no;
        $part1 = $part . ' ' . $party;
        foreach ($data as $val) {
            $val['vchr_no'] = $bill_no;
            if ($val['ac_id'] == $this->ac_id) {
                if (($this->doc_type == 'DN' && $val['dr_cr'] == 'D') || ($this->doc_type == 'CN' && $val['dr_cr'] == 'C')) {
                    $val['part'] = $part . ' ' . $this->remarks;
                } else {
                    $val['part'] .=  ' Tds @' . $this->tds_per . '% on ' . $this->tds_on;
                }
            } else {
                $val['part'] = $part1. ' ' . $this->remarks;
            }
        }
        return $data;
    }

    private function getFixPart($bill_no)
    {
        if ($this->pur_sale_type == 'P' || $this->doc_type == 'EV') {
            $part = "Party Doc No." . $this->party_doc_no;
        } elseif ($this->doc_type == 'DN') {
            $part = "Debit Note No." . $bill_no;
        } elseif ($this->doc_type == 'CN') {
            $part = "Credit Note No." . $bill_no;
        }
        return $part;
    }
}
