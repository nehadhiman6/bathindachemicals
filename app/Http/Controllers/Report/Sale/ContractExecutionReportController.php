<?php

namespace App\Http\Controllers\Report\Sale;

use App\Http\Controllers\Controller;
use App\Models\Company\SaleContract;
use App\Models\Yearly\Parameter;
use App\Models\Yearly\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ContractExecutionReportController extends Controller
{
    public function index(){
        if (Gate::denies('contract-execution-report')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Reports/Sale/ContractExecutionReport', [
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('contract-execution-report')) {
            return deny();
        }
        $rules = [
            'start_date' => 'required',
            'to_date' => 'required'
        ];
        $this->validate($request,$rules);
        $dt1 = getDateFormat($request->start_date,'ymd');
        $dt2 = getDateFormat($request->to_date,'ymd');
        $unit_id_qtl = Parameter::getParaValue("brokerage_per_quintal_unit");
        $data = SaleContract::join('sales_contract_dets','sales_contracts.id','=','sales_contract_dets.sale_contract_id')
                            ->join(getSharedDb().'.accounts','accounts.id','=','sales_contracts.ac_id')
                            ->join(getSharedDb().'.items','items.id','=','sales_contract_dets.item_id')
                            ->join(getSharedDb().'.item_units','item_units.id','=','sales_contract_dets.unit_id')
                            ->join(getSharedDb().'.branches','branches.id','=','sales_contracts.branch_id')
                            ->leftJoin(getSharedDb().'.accounts as broker','broker.id','=','sales_contracts.broker_id')
                            ->whereBetween('sales_contracts.contract_date',[$dt1,$dt2])
                    ->select('sales_contracts.*','accounts.name as ac_name','items.item_name','branches.name as branch_name',
                            'item_units.unit_name','sales_contract_dets.qty','sales_contract_dets.unit_id',
                            'sales_contract_dets.item_id','sales_contract_dets.rate',
                            'sales_contract_dets.remarks as item_desc','sales_contracts.packed_loose',

                    )->orderBy('sales_contracts.contract_date')->orderBy('sales_contracts.id');
        if($request->branch_ids && count($request->branch_ids) > 0) {
            $data = $data->whereIn('sales_contracts.branch_id',$request->branch_ids);
        } else {
            $branch_id = getCurrentBranchId();
            $data = $data->where('sales_contracts.branch_id',$branch_id);
        }
        if($request->item_ids && count($request->item_ids) > 0) {
            $data = $data->whereIn('sale_contract_dets.item_id',$request->item_ids);
        }
        if($request->packed_loose != 'all') {
            $data = $data->where('sales_contracts.packed_loose',$request->packed_loose);
        }
        if($request->ac_ids && count($request->ac_ids) > 0) {
            $data = $data->whereIn('sales_contracts.ac_id',$request->ac_ids);
        }
        if($request->sale_contract_ids && count($request->sale_contract_ids) > 0) {
            $data = $data->whereIn('sales_contracts.id',$request->sale_contract_ids);
        }

        $sale_cont_ids = $data->pluck('id')->toArray();
        $data = $data->get();
        $sale_orders = SaleOrder::join('sale_order_dets','sale_order_dets.sale_order_id','=','sale_orders.id')
                        ->join(getSharedDb().'.items','items.id','=','sale_order_dets.item_id')
                        ->join(getCompanyDb().'.sales_contracts','sale_order_dets.sale_contract_id','=','sales_contracts.id')
                        ->leftJoin('sale_order_det_packs','sale_order_dets.id','=','sale_order_det_packs.sale_order_det_id')
                        ->leftJoin(getSharedDb().'.packings','packings.id','=',DB::raw("ifnull(sale_order_det_packs.packing_id,sale_order_dets.packing_id)"))
                        ->leftJoin('invoices','sale_orders.id','=','invoices.sale_order_id')
                        ->select('sale_orders.id','sale_orders.sale_order_no','sale_orders.sale_order_date','items.item_name',
                                'packings.name as packing','invoices.invoice_no','invoices.invoice_date','sales_contracts.gst_terms',
                                'sales_contracts.delivery_terms','sale_order_dets.freight','sale_order_dets.packing_cost',
                                DB::raw("ifnull(sale_order_det_packs.qty,sale_order_dets.qty) as qty"),
                                DB::raw("ifnull(sale_order_det_packs.weight/100,sale_order_dets.weight) as weight"),
                                DB::raw("ifnull(sale_order_det_packs.final_rate,sale_order_dets.rate) as rate"),
                                DB::raw("ifnull(sale_order_det_packs.discount,sale_order_dets.discount) as discount"),
                                DB::raw("sale_order_det_packs.net_rate"),
                                DB::raw("case when sale_order_det_packs.id is not null then 'Q' else sale_order_dets.rate_on end as rate_on"),
                                DB::raw("concat('C',sale_order_dets.sale_contract_id,'I',sale_order_dets.item_id) as cont_item"),
                                DB::raw("$unit_id_qtl as unit_id_qtl"),

        )->whereIn('sale_order_dets.sale_contract_id',$sale_cont_ids)
            ->orderBy('sale_orders.sale_order_date')->orderBy('sale_orders.id');
        $sale_orders = $sale_orders->get()->groupBy('cont_item')->toArray();

        return reply(true,[
            'contract_execution' => $data,
            'sale_orders' => $sale_orders
        ]);
    }
}
