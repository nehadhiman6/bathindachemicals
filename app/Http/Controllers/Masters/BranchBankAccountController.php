<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Branch;
use App\Models\Masters\BranchBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class BranchBankAccountController extends Controller
{

    public function show($branch_id){
        if (Gate::denies('branch-bank-accounts')) {
            return deny();
        }
        $branch = Branch::findOrFail($branch_id);
        return Inertia::render('ProjectComponents/Masters/BranchBankAccount/BranchBankAccountList',[
            'branch' => $branch,
            'branch_id' => $branch_id
        ]);
    }

    public function branchBankAccountsList(Request $request){
        if (Gate::denies('branch-bank-accounts')) {
            return deny();
        }
        $count = BranchBankAccount::where('branch_id',$request->branch_id)->count();
        $filteredCount = $count;

        $branch_bank_accounts = BranchBankAccount::where('branch_id',$request->branch_id);
        if ($searchStr = $request->input('search.value')) {
            $branch_bank_accounts = $branch_bank_accounts->where('bank_account_number', 'like', "%{$searchStr}%");
        }

        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $branch_bank_accounts = $branch_bank_accounts->orderBy($field_name, $asc_desc);
        }
        else{
            $branch_bank_accounts = $branch_bank_accounts->orderBy('id','DESC');

        }

        $branch_bank_accounts = $branch_bank_accounts->take($request->length);
        $filteredCount = $branch_bank_accounts->count();
        if ($request->start > 0) {
            $branch_bank_accounts->skip($request->start);
        }

        $branch_bank_accounts = $branch_bank_accounts->with(['ifsc'])->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $branch_bank_accounts,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('branch-bank-accounts-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $branch_bank_account = BranchBankAccount::findOrNew($request->form_id);
        $branch_bank_account->fill($request->all());
        $branch_bank_account->save();

        return reply(true, [
            'branch_bank_account' => $branch_bank_account
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'branch_id' =>'required|exists:branches,id',
            'benificiary_name'=>'required|max:191',
            'bank_account_number'=>'required|numeric',
            'ifsc_id' =>'required|exists:ifscs,id',
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('branch-bank-accounts-modify')) {
            return deny();
        }
        $branch_bank_account = BranchBankAccount::whereId($id)->first()->load(['ifsc']);
        return reply(true, [
            'branch_bank_account' => $branch_bank_account,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('branch-bank-accounts-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
