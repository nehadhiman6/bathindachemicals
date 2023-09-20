<?php

namespace App\Http\Controllers\Yearly;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleOrderRequest;
use App\Models\Accounts\Account;
use App\Models\Accounts\AccountDetail;
use App\Models\Company\SaleContract;
use App\Models\Company\SaleContractDetail;
use App\Models\Yearly\SaleOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;


class SaleOrderController extends Controller
{
    public function index(Request $request){
        if (Gate::denies('sale-orders')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Sale/SaleOrder/SaleOrderList',[
            'transport_types' => getTransportTypes()
        ]);
    }

    public function saleOrderList (Request $request){
        if (Gate::denies('sale-orders')) {
            return deny();
        }
        $count = SaleOrder::count();
        $filteredCount = $count;

        $sale_contracts = SaleOrder::leftJoin(getSharedDb().'.accounts','accounts.id','=','sale_orders.client_id');

        if ($searchStr = $request->input('search.value')) {
            $sale_contracts = $sale_contracts->where('accounts.name', 'like', "%{$searchStr}%");
        }

        $sale_contracts = $sale_contracts->where('sale_orders.branch_id',getCurrentBranchId());
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'sale_orders.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='client_name'){
            $field_name = 'accounts.name';
        }

        if($field_name){
            $sale_contracts = $sale_contracts->orderBy($field_name, $asc_desc);
        }
        else{
            $sale_contracts = $sale_contracts->orderBy('sale_orders.id','DESC');
        }
        $sale_contracts = $sale_contracts->take($request->length);
        $filteredCount = $sale_contracts->count();
        if ($request->start > 0) {
            $sale_contracts->skip($request->start);
        }
        $sale_contracts = $sale_contracts->groupBy('sale_orders.id')
        ->select('sale_orders.*','accounts.name as client_name')->withCount(['sale_order_dispatches','sale_invoice'])->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $sale_contracts,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store (SaleOrderRequest $request){


        if (Gate::denies('sale-orders-add')) {
            return deny();
        }
        return $request->save();
    }

    public function update (SaleOrderRequest $request){
        if (Gate::denies('sale-orders-modify')) {
            return deny();
        }
        return $request->save();
    }

    public function edit(Request $request,$id){
        if (Gate::denies('sale-orders-modify') || Gate::denies('sale-orders-view')) {
            return deny();
        }
        $sale_order = SaleOrder::whereId($id)->with([
            'sale_order_details',
            'client.account_yearly',
            'bill_party',
            'ship_party',
            'transport',
            'sale_order_details.sale_order_packs.brand',
            'sale_order_details.item',
            'sale_order_details.packing',
            'sale_order_details.sale_contract',
            'sale_order_details.gst.gst_types.details',
            'sale_order_details.vat_cst',
            'sale_order_details.foc_item',
            'sale_order_details.foc_packing',
            'sale_order_details.foc_brand',
            'sale_order_details.sale_order_packs.packing',
        ])->first();

        $account = Account::findOrFail($sale_order->bill_party_id);
        $credit_limit = $account->getAccountLimit();
        $balance = getAccountBalanceLimit($sale_order->bill_party_id);

        $sale_order['limit'] = $credit_limit;
        $sale_order['balance'] = $balance;


        foreach($sale_order->sale_order_details as $sale_detail){
            $sale_contract_item = SaleContractDetail::where('sale_contract_id',$sale_detail->sale_contract_id)->where('item_id',$sale_detail->item_id)->first();
            $party_cat_id = ($t = AccountDetail::where('ac_id',$sale_order->bill_party_id)->first()) ? $t->party_cat_id:0;
            $calcData = SaleContract::getPackRatesData($sale_detail->sale_contract_id,$sale_detail->item_id,$party_cat_id,$sale_order->sale_order_date);
            $focCalcData = SaleContract::getSaleOrderFocPacking($sale_detail->sale_contract_id, $sale_detail->foc_item_id);
            $sale_detail['calcData'] = $calcData;
            $sale_detail['focCalcData'] = $focCalcData;
            $sale_detail['sale_contract_item'] = $sale_contract_item;

        }
        return reply(true, [
            'sale_order' => $sale_order,
        ]);
    }

}
