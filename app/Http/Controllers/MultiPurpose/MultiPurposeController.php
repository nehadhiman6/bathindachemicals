<?php

namespace App\Http\Controllers\MultiPurpose;

use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Accounts\AccountDetail;
use App\Models\Accounts\LimitAccount;
use App\Models\Accounts\PayTerm;
use App\Models\Company\FixRateDetail;
use App\Models\Company\ItemFreight;
use App\Models\Company\ItemFreightDetail;
use App\Models\Company\SaleContract;
use App\Models\Company\SaleContractDetail;
use App\Models\Items\Item;
use App\Models\Masters\Branch;
use App\Models\Masters\Packing;
use App\Models\Masters\PartyCategory;
use App\Models\Masters\TypeMaster;
use App\Models\Yearly\AccountYearly;
use App\Models\Yearly\Cashbook;
use App\Models\Yearly\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class MultiPurposeController extends Controller
{
    public function setCurrentSessionBranch(Request $request){
        session()->put('branch_id', $request->current_branch_id);
        return reply(true);
    }

    public function getCurrentSessionBranch(Request $request){
        return Inertia::render('ProjectComponents/Users/SetUserBranch',[
        ]);
    }

    public function getPartyCategoryPacking(Request $request, $ac_id){
        $packing = null;
        $pay_term = null;
        $account_det = AccountDetail::where('ac_id',$ac_id)->first();
        if($account_det->party_cat_id > 0){
            $party_category = PartyCategory::findOrFail($account_det->party_cat_id);
            if($party_category->packing_id > 0){
                $packing = Packing::findOrFail($party_category->packing_id);
            }
        }
        if($account_det->payment_term_id > 0){
            $pay_term = PayTerm::findOrFail($account_det->payment_term_id);
        }
        return reply(true,[
            'packing' => $packing,
            'pay_term' =>$pay_term
        ]);
    }

    public function getSaleContractItem(Request $request, $item_id,$sale_contract_id){
    }

    public function getSaleOrderDetail(Request $request){
        // throw ValidationException::withMessages(['gen_msg' =>'Sorry! You must set a branch to Proceed']);

        $this->validate($request,[
            'sale_order_date' =>'required|date_format:d-m-Y|before_or_equal:' . getToday('d-m-Y'),
            'client_id'=>'required|exists:accounts,id',
            'bill_party_id'=>'required|exists:accounts,id',
            'ship_party_id'=>'required|exists:accounts,id',
            'packed_loose' =>'required|in:packed,loose',
            'transport_type'=>'required',
        ]);

        return reply(true);
    }

    public function getSaleOrderPacksRate(Request $request){
                    //VARIABLES SENT FROM FRONT\
        // bill_party_id
        // item_id
        // sale_contract_id
        // sale_order_date
        $sale_contract_item = SaleContractDetail::where('sale_contract_id',$request->sale_contract_id)->where('item_id',$request->item_id)->first();
        $party_cat_id = ($t = AccountDetail::where('ac_id',$request->bill_party_id)->first()) ? $t->party_cat_id:0;
        return [SaleContract::getPackRatesData($request->sale_contract_id,$request->item_id,$party_cat_id,$request->sale_order_date),$sale_contract_item];
    }


    public function getSaleOrderFocPacking(Request $request){
        return reply(true,[
            'packing_formulas'=>SaleContract::getSaleOrderFocPacking($request->sale_contract_id,$request->item_id)
        ]);
    }
    public function getSaleOrderClient(Request $request,$ac_id){
        $account = Account::findOrFail($ac_id);
        $credit_limit = $account->getAccountLimit();
        $balance = getAccountBalanceLimit($ac_id);

        return reply(true,[
            'limit' =>$credit_limit,
            'balance'=>$balance
        ]);
    }

    public function getCashInHand(Request $request)
    {
        $date = $request->voucher_date;
        $data = getCash($date);
        return $data;
    }


    public function getAccountBalance(Request $request)
    {
        $dt1 = $request->has('date') ? getDateFormat($request->date, 'ymd'):getToday();
        $branch_id = $request->has('branch_id') ? $request->branch_id:-1;
        $balance =  Cashbook::getBalance($request->ac_id,$dt1,$branch_id,'s');
        return reply(true, compact('balance'));
    }

    public function getBranches(Request $request){
        return reply(true,[
            'branches' => Branch::whereIn('id',$request->branches)->get()
        ]);
    }

    public function getItemFreight(Request $reqeust){
        $item_freight = ItemFreight::where('item_id',$reqeust->item_id)
                ->orderBy('wef_date','DESC')->pluck('id')->toArray();
        $city_id = 0;
        $account_detail = AccountDetail::where('ac_id',$reqeust->ac_id)->first();
        if($account_detail){
            $city_id = $account_detail['city_id'];
        }
        $item = Item::findOrFail($reqeust->item_id);
        $item->load(
            'main_group',
            'sub_group'
        );

        $item_freight_dets = ItemFreightDetail::whereIn('item_freight_id',$item_freight)->where('city_id',$city_id)->first();
        return reply(true,[
            'item_freight' => $item_freight_dets,
            'item' =>$item
        ]);

    }

    public function getItemFixRate(Request $reqeust){
        $rate = '';
        $item_freight = FixRateDetail::join('fix_rates','fix_rates.id','=','fix_rate_dets.fix_rate_id')
        ->where('fix_rate_dets.item_id',$reqeust->item_id)
        ->where('fix_rate_dets.packing_id',$reqeust->packing_id)
        ->orderBy('fix_rates.wef_date','DESC')->select('fix_rate_dets.rate')->first();
        if($item_freight){
            $rate = $item_freight['rate'];
        }
        return reply(true,[
            'rate' => $rate
        ]);
    }


    public function getFormInitialData(Request $request,$type){
        $data = [];
        if($type == 'sale'){
            $data = [
                'transport_types' => getTransportTypes(),
                'branch' => getCurrentBranch(),
                'tcs_liquor' => Parameter::getParaValue('tcs_rate_liqour')
            ];
        }
        return reply(true,$data);
    }
}
