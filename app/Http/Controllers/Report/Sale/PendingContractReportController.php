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

class PendingContractReportController extends Controller
{
    public function index(){
        if (Gate::denies('pending-contract-report')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Reports/Sale/PendingContractReport', [
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('pending-contract-report')) {
            return deny();
        }

        $rules = [
            'start_date' => 'required',
            'to_date' => 'required'
        ];
        $unit_id_qtl = Parameter::getParaValue("brokerage_per_quintal_unit");
        $this->validate($request,$rules);
        $dt1 = getDateFormat($request->start_date,'ymd');
        $dt2 = getDateFormat($request->to_date,'ymd');
        $data = SaleContract::join('sales_contract_dets','sales_contracts.id','=','sales_contract_dets.sale_contract_id')
                            ->join(getSharedDb().'.accounts','accounts.id','=','sales_contracts.ac_id')
                            ->join(getSharedDb().'.items','items.id','=','sales_contract_dets.item_id')
                            ->join(getSharedDb().'.item_units','item_units.id','=','sales_contract_dets.unit_id')
                            ->join(getSharedDb().'.branches','branches.id','=','sales_contracts.branch_id')
                            ->leftJoin(getSharedDb().'.accounts as broker','broker.id','=','sales_contracts.broker_id')
                            ->whereBetween('sales_contracts.contract_date',[$dt1,$dt2])
                    ->select('sales_contracts.*','accounts.name as ac_name','items.item_name','branches.name as branch_name',
                            'item_units.unit_name','sales_contract_dets.qty',
                            'sales_contract_dets.item_id','sales_contract_dets.rate',
                            'sales_contracts.packed_loose',

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
        $sale_cont_ids = $data->pluck('id')->toArray();
        $data = $data->get();
        $sale_orders = SaleOrder::join('sale_order_dets','sale_order_dets.sale_order_id','=','sale_orders.id')
                        ->join(getCompanyDb().'.sales_contract_dets',function($join) {
                            $join->on('sale_order_dets.sale_contract_id','=','sales_contract_dets.sale_contract_id')
                                ->on('sale_order_dets.item_id','=','sales_contract_dets.item_id');
                        })
                        ->join(getSharedDb().'.items','items.id','=','sale_order_dets.item_id')
                        ->leftJoin('sale_order_det_packs','sale_order_dets.id','=','sale_order_det_packs.sale_order_det_id')
                        ->leftJoin('invoices','sale_orders.id','=','invoices.sale_order_id')
                        ->select(
                                DB::raw("concat('C',sale_order_dets.sale_contract_id,'I',sale_order_dets.item_id) as cont_item"),
                                DB::raw("sum(ifnull(case when sales_contract_dets.unit_id <> $unit_id_qtl then sale_order_det_packs.qty else sale_order_det_packs.weight/100 end,case when sale_order_dets.rate_on = 'Q' then sale_order_dets.qty else sale_order_dets.weight end)) as qty")
        )->whereIn('sale_order_dets.sale_contract_id',$sale_cont_ids)
            ->groupByRaw("1");
        $sale_orders = $sale_orders->get()->groupBy('cont_item')->toArray();

        return reply(true,[
            'contracts' => $data,
            'sale_orders' => $sale_orders
        ]);


        //  PARAMETERS
        // start_date
        // to_date
        // branches_ids
        // item_ids
        // ac_ids
        // packed_loose:'all',
        // status:'a',
        // pending_grt:''
    }
}
