<?php

namespace App\Http\Controllers\Report\Sale;

use App\Http\Controllers\Controller;
use App\Models\Yearly\Invoice;
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDetail;
use App\Models\Yearly\TransGstDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SaleReportController extends Controller
{
    public function index(){
        if (Gate::denies('ledger-report')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Reports/Sale/SaleReport', [
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('ledger-report')) {
            return deny();
        }
        $rules = [
            'start_date' => 'required',
            'to_date' => 'required'
        ];
        $this->validate($request,$rules);
        $dt1 = getDateFormat($request->start_date,'ymd');
        $dt2 = getDateFormat($request->to_date,'ymd');

        $sale_order_dets = SaleOrderDetail::select('sale_order_id','item_id','sale_contract_id');
        $sale_order_dets1 = SaleOrderDetail::select('sale_order_id','foc_item_id as item_id','sale_contract_id')
                ->where('foc_item_id','>',0);
        $sale_order_dets->unionAll($sale_order_dets1);
        $sale_orders = SaleOrder::joinSub($sale_order_dets,'sale_order_dets','sale_orders.id','=','sale_order_dets.sale_order_id')
                            ->join(getCompanyDb().'.sales_contracts','sale_order_dets.sale_contract_id','=','sales_contracts.id')
                            ->leftJoin(getSharedDb().'.accounts','accounts.id','=','sales_contracts.broker_id')
                            ->select('sale_orders.id','sale_order_dets.item_id','sale_orders.sale_order_no','accounts.name','sales_contracts.contract_no');
        $trans_gst_details = TransGstDetail::getGstQry('SA');
        $data = Invoice::join('invoice_dets','invoices.id','=','invoice_dets.invoice_id')
                        ->join(getSharedDb().'.accounts as cl','invoices.client_id','=','cl.id')
                        ->join(getSharedDb().'.account_details as cld','cld.ac_id','=','cl.id')
                        ->join(getSharedDb().'.cities as clc','clc.id','=','cld.city_id')
                        ->join(getSharedDb().'.accounts as sh','invoices.ship_party_id','=','sh.id')
                        ->join(getSharedDb().'.account_details as shd','shd.ac_id','=','sh.id')
                        ->join(getSharedDb().'.cities as shc','shc.id','=','shd.city_id')
                        ->join(getSharedDb().'.accounts as blp','invoices.bill_party_id','=','blp.id')
                        ->join(getSharedDb().'.account_details as bld','bld.ac_id','=','blp.id')
                        ->join(getSharedDb().'.cities as blc','blc.id','=','bld.city_id')
                        ->join(getSharedDb().'.items','invoice_dets.item_id','=','items.id')
                        ->join(getSharedDb().'.accounts as sa','invoice_dets.acid_sale','=','sa.id')
                        ->join(getSharedDb().'.item_units','invoice_dets.unit_id','=','item_units.id')
                        ->join(getSharedDb().'.uqcs','uqcs.id','=','item_units.uqc_id')
                        ->leftJoinSub($sale_orders,'a1',function($join) {
                            $join->on('a1.id','=','invoices.sale_order_id')
                                ->on('invoice_dets.item_id','=','a1.item_id');
                        })->leftJoin(getSharedDb().'.packings', 'invoice_dets.packing_id','=','packings.id')
                        ->leftJoin(getSharedDb().'.brands', 'invoice_dets.brand_id','=','brands.id')
                        ->leftJoinSub($trans_gst_details,'trans_gst_details',function($join) {
                            $join->on('trans_gst_details.vcode','=','invoices.vcode')
                                ->on('invoice_dets.id','=','trans_gst_details.det_id');
                        })
                        ->leftJoin('trans_vat_details','trans_vat_details.vcode','=','invoices.vcode')
                        ->whereBetween('invoices.invoice_date',[$dt1,$dt2])
                        ->select('invoices.*','cl.name as ac_name','clc.name as city_name','blp.name as bill_to_party',
                                'sh.name as ship_to_party','shc.name as ship_to_city','a1.sale_order_no','a1.contract_no',
                                'items.item_name','invoice_dets.hsn_code','packings.name as packing_name','blc.name as bill_to_city',
                                'sa.name as sale_ac_name','invoice_dets.qty as sale_qty','invoice_dets.weight',
                                'item_units.unit_name as unit',DB::raw("invoice_dets.net_amount-ifnull(invoice_dets.gst_vat_amount,0) as sale_value"),
                                'trans_gst_details.igstamt as igst','trans_gst_details.cgstamt as cgst','trans_gst_details.sgstamt as sgst',
                                DB::raw("case when ifnull(trans_vat_details.vat_cst,'') = 'v' then trans_vat_details.vat_cst_amt else 0 end as vat"),
                                DB::raw("case when ifnull(trans_vat_details.vat_cst,'') = 'c' then trans_vat_details.vat_cst_amt else 0 end as cst"),
                                "trans_vat_details.surcharge_amt",
                                'a1.name as broker','brands.name as brand_name'
                    )->orderByRaw("invoices.invoice_date,invoices.invoice_no_part");
        if($request->branch_ids && count($request->branch_ids) > 0) {
            $data = $data->whereIn('invoices.branch_id',$request->branch_ids);
        } else {
            $branch_id = getCurrentBranchId();
            $data = $data->where('invoices.branch_id',$branch_id);
        }
        if($request->item_ids && count($request->item_ids) > 0) {
            $data = $data->whereIn('invoice_dets.item_id',$request->item_ids);
        }
        if($request->ac_ids && count($request->ac_ids) > 0) {
            $data = $data->whereIn('invoices.bill_party_id',$request->ac_ids);
        }
        if($request->packed_loose != 'all') {
            $data = $data->where('invoices.packed_loose',$request->packed_loose);
        }
        $data = $data->get();

        //  PARAMETERS
        // start_date
        // to_date
        // branches_ids
        // item_ids
        // ac_ids
        // packed_loose

        // state.columnDefs.push({ title: 'Freight',targets: target++,data: 'freight'});
        // state.columnDefs.push({ title: 'Bill Value',targets: target++,data: 'nat_amt'});
        // state.columnDefs.push({ title: 'Agent/Broker',targets: target++,data: 'agent'});
        // state.columnDefs.push({ title: 'IRN No.',targets: target++,data: 'irn_no'});


        return reply(true,[
            'sales' => $data
        ]);
    }
}


