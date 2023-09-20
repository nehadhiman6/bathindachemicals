<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Accounts\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index(){
        if (Gate::denies('accounts')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Accounts/Account/AccountList',[
            'type' => 'party'
        ]);
    }

    public function getOtherAccount(){
        if (Gate::denies('other-accounts')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Accounts/Account/AccountList',[
            'type' => 'other'
        ]);
    }

    public function accountsList(Request $request){
        if (Gate::denies('accounts')) {
            return deny();
        }
        $count = Account::count();
        $filteredCount = $count;

        $accounts = Account::where('accounts.id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $accounts = $accounts->where('name', 'like', "%{$searchStr}%");
        }
        if(isset($request->type) && $request->type == 'party'){
            $accounts = $accounts
            ->join('account_details', 'accounts.id', '=', 'account_details.ac_id');
        }
        else if(isset($request->type) && $request->type == 'other'){
            $accounts = $accounts
            ->leftJoin('account_details', 'accounts.id', '=', 'account_details.ac_id')
            ->whereNull('account_details.ac_id');
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $accounts = $accounts->orderBy($field_name, $asc_desc);
        }
        else{
            $accounts = $accounts->orderBy('accounts.id','DESC');

        }

        $accounts = $accounts->take($request->length);
        $filteredCount = $accounts->count();
        if ($request->start > 0) {
            $accounts->skip($request->start);
        }
        $accounts = $accounts->select('accounts.*','accounts.id')
        ->withCount('resources')
        ->groupBy('accounts.id')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $accounts,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(AccountRequest $request){
        if (Gate::denies('accounts-add')) {
            return deny();
        }
        return $request->save();
    }

    public function update(AccountRequest $request){
        return $request->save();
    }

    public function edit($id)
    {
        if (Gate::denies('accounts-modify') || Gate::denies('accounts-view')) {
            return deny();
        }
        $account = Account::whereId($id)->first()->load(['account_detail.city','account_detail.client','account_detail.vendor','account_detail.ifsc.bank'
        ,'account_detail.pay_term','account_detail.account_ledger','account_detail.party_category',
        'account_sub_group', 'sub_group_one', 'sub_group_two', 'account_branches.branch',
        ]);
        return reply(true, [
            'account' => $account,
        ]);
    }


}
