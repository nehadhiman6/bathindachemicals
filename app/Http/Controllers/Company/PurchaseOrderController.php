<?php

namespace App\Http\Controllers\Company;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\Company\PurchaseOrder;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Company\PurchaseOrderDetail;

class PurchaseOrderController extends Controller
{
    public function index(Request $request){
        if (Gate::denies('purchase-order')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/PurchaseOrder/PurchaseOrderList',[
            'pur_order_types'=>getPurOrderTypes()
        ]);
    }

    public function purchaseOrderList (Request $request){
        if (Gate::denies('purchase-order')) {
            return deny();
        }
        $count = PurchaseOrder::count();
        $filteredCount = $count;
        $pur_orders = PurchaseOrder::join('pur_ord_dets','pur_ord_dets.pur_ord_id','=','pur_orders.id')
                                    ->join(getSharedDb().'.items','pur_ord_dets.item_id','=','items.id')
                                    ->join(getSharedDb().'.item_units','pur_ord_dets.unit_id','=','item_units.id')
                                    ->leftJoin(getSharedDb().'.accounts','accounts.id','=','pur_orders.seller_acid');

        if ($searchStr = $request->input('search.value')) {
            $pur_orders = $pur_orders->where('accounts.name', 'like', "%{$searchStr}%");
        }

        $pur_orders = $pur_orders->where('pur_orders.branch_id',getCurrentBranchId());
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'pur_orders.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='seller_name'){
            $field_name = 'accounts.name';
        }

        if($field_name){
            $pur_orders = $pur_orders->orderBy($field_name, $asc_desc);
        }
        else{
            $pur_orders = $pur_orders->orderBy('pur_orders.id','DESC');
        }
        $pur_orders = $pur_orders->take($request->length);
        $filteredCount = $pur_orders->count();
        if ($request->start > 0) {
            $pur_orders->skip($request->start);
        }
        $pur_orders = $pur_orders->select('pur_orders.*','accounts.name as seller_name','items.item_name','item_units.unit_name','pur_ord_dets.id as det_id','pur_ord_dets.qty_from','pur_ord_dets.qty_to','pur_ord_dets.rate','pur_ord_dets.qty_extended','pur_ord_dets.rate_extended')->get()->load('seller','buyer','pay_term','broker');
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $pur_orders,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store (PurchaseOrderRequest $request){
        if (Gate::denies('purchase-order-add')) {
            return deny();
        }
        return $request->save();
    }

    public function update (PurchaseOrderRequest $request){
        if (Gate::denies('purchase-order-modify')) {
            return deny();
        }
        return $request->save();
    }

    public function edit($id){
        // dd($id);
        if (Gate::denies('purchase-order-modify')) {
            return deny();
        }
        $pur_order = PurchaseOrder::find($id);
        $pur_order->load(['seller','buyer','pay_term','broker','details.item','details.unit']);
        return reply(true, [
            'pur_order' => $pur_order,
        ]);
    }

    public function qtyExtendedAdd(Request $request){
        if (Gate::denies('qty-extend-order')) {
            return deny();
        }
        $pur_order_detail = PurchaseOrderDetail::find($request->form_id);
        $pur_order_detail->qty_extended = $request->qty_extended;
        $pur_order_detail->rate_extended = $request->rate_extended;
        $pur_order_detail->update();
        return reply(true, [
            'pur_order_detail' => $pur_order_detail,
        ]);

    }

    public function delExtendedAdd(Request $request){
        if (Gate::denies('del-extend-order')) {
            return deny();
        }
        $pur_order = PurchaseOrder::find($request->form_id);
        $pur_order->del_extended_date = $request->del_extended_date;
        $pur_order->update();
        return reply(true, [
            'pur_order' => $pur_order,
        ]);
    }

}
