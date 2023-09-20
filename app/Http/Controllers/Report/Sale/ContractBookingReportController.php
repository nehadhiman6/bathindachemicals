<?php

namespace App\Http\Controllers\Report\Sale;

use App\Http\Controllers\Controller;
use App\Models\Company\SaleContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ContractBookingReportController extends Controller
{
    public function index(){
        if (Gate::denies('contract-booking-report')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Reports/Sale/ContractBookingReport', [
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('contract-booking-report')) {
            return deny();
        }
        $rules = [
            'start_date' => 'required',
            'to_date' => 'required'
        ];
        $this->validate($request,$rules);
        $dt1 = getDateFormat($request->start_date,'ymd');
        $dt2 = getDateFormat($request->to_date,'ymd');
        $data = SaleContract::join('sales_contract_dets','sales_contracts.id','=','sales_contract_dets.sale_contract_id')
                            ->join(getSharedDb().'.accounts','accounts.id','=','sales_contracts.ac_id')
                            ->join(getSharedDb().'.items','items.id','=','sales_contract_dets.item_id')
                            ->join(getSharedDb().'.item_units','item_units.id','=','sales_contract_dets.unit_id')
                            ->leftJoin(getSharedDb().'.accounts as broker','broker.id','=','sales_contracts.broker_id')
                            ->join(getSharedDb().'.pay_terms','pay_terms.id','=','sales_contracts.pay_term_id')
                            ->whereBetween('sales_contracts.contract_date',[$dt1,$dt2])
                    ->select('sales_contracts.*','accounts.name as ac_name','items.item_name',
                            'item_units.unit_name','sales_contract_dets.qty','sales_contract_dets.rate',
                            'broker.name as broker','sales_contract_dets.tolerance_per','pay_terms.name as pay_term'
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
        $data = $data->get();

        return reply(true,[
            'contract_booking' => $data
        ]);
    }

}
