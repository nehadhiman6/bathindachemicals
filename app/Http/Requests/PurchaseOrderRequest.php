<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Cashbook;
use App\Models\Accounts\Account;
use App\Models\Company\PurchaseOrder;
use App\Models\Company\PurchaseOrderDetail;
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

class PurchaseOrderRequest extends FormRequest
{
    protected $current_branch_id=0;
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
            'seller_acid'                    => 'required|exists:accounts,id',
            'buyer_acid'                    => 'required|exists:accounts,id',
            'pay_term_id'                    => 'required|exists:pay_terms,id',
            'del_from'                    => 'required|date_format:d-m-Y',
            'del_to'                    => 'required|date_format:d-m-Y',
            'condition'                    => 'nullable',
            'del_term'      =>       'required',
            'details.*.item_id'          => 'required|exists:items,id',
            'details.*.qty_from'              => 'required|numeric',
            'details.*.qty_to'              => 'required|numeric',
            'details.*.rate'             => 'required|numeric',
        ];
        return $rules;
    }

    public function messages()
    {
        $msgs = [
            'details.*.item_id.required'=>'Item Mandatory !!',
            'details.*.qty_from.required'=>'Qty From Mandatory !!',
            'details.*.qty_to.required'=>'Qty To Mandatory !!',
            'details.*.rate.required'=>'Rate Mandatory !!',

        ];
        return $msgs;
    }

    public function save()
    {

        $detids = [];
        $this->current_branch_id = getCurrentBranchId();
        $prefix = 'purchase_order';
        $branch_prefix = validatePrefix($prefix);
        if ($this->form_id > 0) {
            $pur_order = PurchaseOrder::findOrFail($this->form_id);
            $detids = $pur_order->details->pluck('id')->toArray();
        } else {
            $pur_order = new PurchaseOrder();
        }
        $pur_order->fill($this->all());
        if ($this->form_id == 0) {
            $pur_order->vcode = '';
        }
        $s_no = 1;
        $pur_order_details =  [];
        foreach ($this->details as $key => $det) {
            $arr = getResultModel($detids, PurchaseOrderDetail::class,$det);
            $detids = $arr[1];
            $pur_order_details[$key] = $arr[0];
            $s_no++;
        }
        DB::connection(getYearlyDbConn())->beginTransaction();
        if (intval($this->form_id) == 0) {
                $session_years = session()->get('fy');
                $pur_order->doc_no = getNextSeriesNumberCompany('PO-'.$branch_prefix.$session_years);
                $pur_order->doc_no_print = $branch_prefix  . $pur_order->doc_no;
                $pur_order->branch_id = $this->current_branch_id;
        }
        $pur_order->save();
        $pur_order->vcode = 'PO'. $pur_order->id;
        $pur_order->save();
        $pur_order->details()->saveMany($pur_order_details);
        PurchaseOrderDetail::whereIn('id', $detids)->delete();
        DB::connection(getYearlyDbConn())->commit();
        return reply('ok', [
            'success' => true,
            'pur_order' => $pur_order
        ]);
    }
}
