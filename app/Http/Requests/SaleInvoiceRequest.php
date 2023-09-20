<?php

namespace App\Http\Requests;

use App\Models\Accounts\Account;
use App\Models\Accounts\AccountDetail;
use App\Models\Items\Gst;
use App\Models\Items\Item;
use App\Models\Yearly\Bill;
use App\Models\Yearly\BlDetail;
use App\Models\Yearly\Cashbook;
use App\Models\Yearly\GetParameters;
use App\Models\Yearly\Invoice;
use App\Models\Yearly\InvoiceDetail;
use App\Models\Yearly\SaleOrderDetail;
use App\Models\Yearly\TransGstDetail;
use App\Models\Yearly\TransVatDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PDO;
use stdClass;

class SaleInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    protected $item_type = '';
    protected $cash_books = [];
    protected $_party = null;
     protected $current_branch_id=0;
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {




        return [
            'invoice_date'=>'required|date_format:d-m-Y',
            'invoice_type'=>'required|in:gst_invoice,vat_invoice,retail_invoice,high_seas_sale,sale_against_bond',
            'client_id'=>'required|exists:accounts,id',
            'bill_party_id'=>'required|exists:accounts,id',
            'ship_party_id'=>'required|exists:accounts,id',
            'transport_type'=>'required',
            'invoice_details.*.item_id' =>'required',
            'invoice_details.*.hsn_code' =>'required',
            'invoice_details.*.unit_id' =>'required',
            'invoice_details.*.rate_on' =>'required',
            'invoice_details.*.rate' =>'required',
            'delivery_terms'=>'required|in:F,M,K',
            'reverse_charge_applicable'=>'required|in:Y,N',
            'supply_type'=>'required|in:B2B,B2C,SEZWP,SEZWOP,EXPWP,EXPWOP,DEXP'
        ];
    }


    private function otherValidations(){
        $this->current_branch_id = getCurrentBranchId();
        validateBranch();


        // Invoice Date CANNOT BE FUTURE DATE AND CANNOT BE DATE PRIOR TO LAST INVOICE DATE
        $last_invoice = Invoice::where('branch_id',$this->current_branch_id)->orderBy('invoice_date','desc');
        if($this->form_id >= 0) {
            $last_invoice = $last_invoice->where('id','<',$this->form_id);
        }
        $last_invoice = $last_invoice->first();
        $today_date = getToday();
        $valid = isBetween($this->invoice_date,'d-m-Y',$last_invoice['invoice_date'],$today_date,'d-m-Y');
        if(!$valid){
            throw ValidationException::withMessages(['gen_msg' => "Check ! Date should be between $last_invoice[invoice_date] and $today_date"]);
        }
        foreach($this->invoice_details as $key=>$inv_det){
            $item = Item::findOrFail($inv_det['item_id']);

            if($item && $item['is_liqour'] == 'Y'){
                $secondary_unit = $item->secondary_unit;
                if(count($secondary_unit) == 0){
                    throw ValidationException::withMessages(['gen_msg' => 'Please add secondary Unit(PL) in liqour Items']);
                }
            }
            if($item && !$item['sale_ledger_acid'] > 0){
                throw ValidationException::withMessages(['gen_msg' => 'Please add Sale Account in Item']);
            }
        }

        if($this->client_id  > 0){
            if(!checkActiveAccount($this->client_id)){
                throw ValidationException::withMessages(['gen_msg' => 'The Selected Client Account is Inactive']);
            }
        }
        if($this->bill_party_id  > 0){
               if(!checkActiveAccount($this->bill_party_id)){
                throw ValidationException::withMessages(['gen_msg' => 'The Selected  Party Bill Account is Inactive']);
            }
        }
        if($this->ship_party_id  > 0){
            if(!checkActiveAccount($this->ship_party_id)){
                throw ValidationException::withMessages(['gen_msg' => 'The Selected Ship Account is Inactive']);
            }
        }

    }


    public function save(){
        $this->otherValidations();

        $branch_prefix = validatePrefix($this->invoice_type);
        $invoice = Invoice::findOrNew($this->form_id);
        $pay_term = $this->getPayTerm();
        $invoice->fill($this->all());
        $due_date = null;
        if($pay_term) {
            $invoice->pay_term_id = $pay_term->id;
            $due_date = getDateAdd($this->invoice_date,$pay_term->days_1);
        }
        $invoice->due_date = $due_date;
        $gst_vat = 'G';
        $vc = '';
        if($this->invoice_type == 'vat_invoice' || $this->invoice_type == 'retail_invoice') {
            $gst_vat = 'V';
        }
        if($this->form_id > 0) {
            $vc = $this->vcode;
        }
        if($gst_vat == 'V') {
            $vat_cst_details = TransVatDetail::firstOrNew(['vcode' => $vc]);
            $vat_cst_details->fill($this->vat_cst_details);
        }
        $transdetids = $invoice->trans_gst_details()->pluck('id')->toArray();
        $cash_book_ids = $invoice->cash_books()->pluck('id')->toArray();
        $old_bl_ids = $invoice->bl_details()->pluck('id')->toArray();
        $bill_ids = $invoice->bills()->pluck('id')->toArray();
        $gst_details = [];
        $old_ids = $invoice->invoice_details()->pluck('id')->toArray();
        $invoice_details = new Collection();
        $gst_amounts = [];
        $sale_acc_data = [];
        $tqtywt = 0;
        foreach($this->invoice_details as $key=>$invoice_det){
            $invoice_d = InvoiceDetail::findOrNew($invoice_det['id']);
            $invoice_d->fill($invoice_det);
            $invoice_details->add($invoice_d);
            if (!array_key_exists($invoice_det['acid_sale'], $sale_acc_data)) {
                $sale_acc_data[$invoice_det['acid_sale']] = ['amount' => 0,'weight' => 0];
            }
            $qtywt = ($invoice_det['rate_on'] == 'Q') ? $invoice_det['qty']:$invoice_det['weight'];
            $sale_acc_data[$invoice_det['acid_sale']]['amount'] += $invoice_det['net_amount']-floatval($invoice_det['gst_vat_amount']);
            $sale_acc_data[$invoice_det['acid_sale']]['weight'] += $qtywt;
            $tqtywt += $qtywt ;
            $gst_details[$key] = [];
            foreach ($invoice_det['trans_gst_details'] as $gstdet) {
                $arr = getResultModel($transdetids, TransGstDetail::class, $gstdet);
                $gst_details[$key][] = $arr[0];
                $transdetids = $arr[1];
                if (!array_key_exists($gstdet['acid_gst'], $gst_amounts)) {
                    $gst_amounts[$gstdet['acid_gst']] = ['amount' => 0,'weight' => 0];
                }
                $gst_amounts[$gstdet['acid_gst']]['amount'] += $gstdet['gst_value'];
                $gst_amounts[$gstdet['acid_gst']]['weight'] += $qtywt;
            }
        }
        $new_ids = $invoice_details->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);
        $this->_party = Account::find($this->bill_party_id)->load('account_detail');
        $cash_book_ids = $this->GetCashBookDetails($cash_book_ids,$gst_amounts,$sale_acc_data,$tqtywt,$due_date,$gst_vat);
        // bills
        $result = getResultModel($bill_ids, Bill::class, [
            'trans_date' => $this->invoice_date, 'ac_id' => $this->bill_party_id, 'dr_cr' => 'D',
            'ref' => $invoice->bill_no, 'ref_date' => $this->invoice_date, 'ref_key' => '',
            'amount' => $this->net_amt,'branch_id' => $this->current_branch_id
        ]);
        $bills[] = $result[0];
        $bill_ids = $result[1];

        // BL DETAILS
        $bl_details = new Collection();
        foreach($this->bl_details as $key=>$bl_det){
            $bl_detail = InvoiceDetail::findOrNew($bl_det['id']);
            $bl_detail->fill($bl_det);
            $bl_details->add($bl_detail);
        }
        $new_bl_ids = $bl_details->pluck('id')->toArray();
        $detach_bl = array_diff($old_bl_ids,$new_bl_ids);

        DB::connection(getYearlyDbConn())->beginTransaction();
            if($this->form_id > 0){
                $uid = addUid($invoice->uid);
            }
            $invoice->save();
            if($this->form_id ==0){
                $invoice->vcode = 'SA'. $invoice->id;
                $generated_no = getYearlyNextEntryNumberFront('SA-'.$branch_prefix);
                $invoice->invoice_no =  $branch_prefix  . $generated_no;
                $invoice->invoice_no_part =$generated_no;
            }
            $invoice->branch_id= $this->current_branch_id;
            $invoice->save();
            $this->setPart($invoice->invoice_no);
            foreach($bills as $val) {
                $val['ref'] = $invoice->invoice_no;
                $val['ref_key'] = '';
            }
            foreach ($invoice_details as $key => $invoice_det) {
                $item = Item::findOrFail($invoice_det['item_id']);
                if($this->item_type == ''){
                    $this->item_type = $item['gst_vat'];
                }
                else if($this->item_type != $item['gst_vat']){
                    throw ValidationException::withMessages(['gen_msg' =>'Please select either GST items or VAT. You cannot mix them in a sales invoice']);
                }

                $invoice_det->invoice_id = $invoice['id'];
                $invoice_det->save();
                foreach ($gst_details[$key] as $gstdet) {
                    $gstdet->fill(['vcode' => $invoice->vcode, 'det_id' => $invoice_det->id,'branch_id'=>$this->current_branch_id ]);
                    $gstdet->save();
                }
            }
            if($gst_vat == 'V') {
                $vat_cst_details->vcode = $invoice->vcode;
                $vat_cst_details->save();
            } else if($this->form_id > 0) {
                TransVatDetail::where('vcode',$invoice->vcode)->delete();
            }
            $invoice->cash_books()->saveMany($this->cash_books);
            $invoice->bl_details()->saveMany($bl_details);
            $invoice->bills()->saveMany($bills);
            InvoiceDetail::whereIn('id',$detach)->delete();
            BlDetail::whereIn('id',$detach_bl)->delete();
            Bill::whereIn('id',$bill_ids)->delete();
            TransGstDetail::whereIn('id', $transdetids)->delete();
        DB::connection(getYearlyDbConn())->commit();

        return reply(true,[
            'invoice' => $invoice
        ]);
    }

    private function GetCashBookDetails($cbids, $gst_amounts,$sale_acc_data,$tqtywt,$due_date,$gst_vat)
    {
        $para = new GetParameters();
        $party_name = $this->_party->name;
        $this->cash_books = [];
        $cash_cr = $this->cash_cr == 'C' ? 'C':'T';
        $bill_no = $this->form_id > 0 ? $this->invoice_no:'@@@@@';
        $prt = 'Bill No. ' .$bill_no;
        $prt1 = $prt . ' ' . $party_name;
        // vcode, trans_date, due_date, dr_cr, cash_cr, ac_id, branch_id, amount, weight, part
        // dd($this->acid_party);
        if($cash_cr == 'T') {
            $acid = $this->_party->account_detail && intval($this->_party->account_detail->ledger_ac_id) > 0 ? $this->_party->account_detail->ledger_ac_id:$this->bill_party_id;
            $cashbooks[] = ['dr_cr' => 'D', 'ac_id' => $acid, 'amount' => $this->net_amt, 'part' => $prt,'weight' => $tqtywt,'due_date' => $due_date];
        }
        foreach($sale_acc_data as $key => $val) {
            $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $key, 'amount' => $val['amount'], 'part' => $prt1,'weight' => $val['weight']];
        }
        if($gst_vat == 'G') {
            foreach($gst_amounts as $key => $val) {
                $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $key, 'amount' => $val['amount'], 'part' => $prt1,'weight' => $val['weight']];
            }
        } else {
            $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $this->vat_cst_details['vat_cst_acid'], 'amount' => $this->vat_cst_details['vat_cst_amt'], 'part' => $prt1,'weight' => $tqtywt];
            $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $this->vat_cst_details['vat_cst_surcharge_acid'], 'amount' => $this->vat_cst_details['surcharge_amt'], 'part' => $prt1,'weight' => $tqtywt];
        }
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => ($this->is_liquor == 'N') ? $para->getParaValue('tcs_ac_id'):$para->getParaValue('tcs_ac_id_liquor'), 'amount' => $this->tcs_amount, 'part' => $prt1];
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $para->getParaValue('ac_id_roff'), 'amount' => $this->round_off, 'part' => $prt1];
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $gst_vat == 'G' ? $para->getParaValue('ac_id_freight_gst'):$para->getParaValue('ac_id_freight_vat'),'amount' => floatval($this->freight)+floatval($this->freight_amount), 'part' => $prt1];
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $para->getParaValue('ac_id_excise_duty'),'amount' => floatval($this->add_excise_amount)-floatval($this->less_excise_amount), 'part' => $prt1];
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $para->getParaValue('ac_id_barcode_fee'),'amount' => $this->bar_code_fee, 'part' => $prt1];
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $para->getParaValue('ac_id_export_fee'),'amount' => $this->export_fee, 'part' => $prt1];
        $cashbooks[] = ['dr_cr' => 'C', 'ac_id' => $para->getParaValue('ac_id_court_fee'),'amount' => $this->court_fee, 'part' => $prt1];

        $fixvalues = ['cash_cr' => $cash_cr, 'trans_date' => $this->invoice_date,'branch_id' => getCurrentBranchId()];
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


    private function getPayTerm() {
        $pay_term = null;
        if($this->sale_order_id == 0) {
            $pay_term = ($t = AccountDetail::where('ac_id',$this->bill_party_id)->first()) ? $t->pay_term:null;
        } else {
            $pay_term = ($sale_cont = SaleOrderDetail::where('sale_order_id',$this->sale_order_id)->first()->sale_contract) ? $sale_cont->pay_term:null;
        }
        return $pay_term;
    }
}
