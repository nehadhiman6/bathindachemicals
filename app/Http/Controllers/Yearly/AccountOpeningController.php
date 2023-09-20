<?php

namespace App\Http\Controllers\Yearly;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpeningRequest;
use App\Models\Accounts\Account;
use App\Models\Accounts\AccountBranch;
use App\Models\Masters\Branch;
use App\Models\Yearly\AccountOpening;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountOpeningController extends Controller
{
    public function show(Request $request,$id){
        $account = Account::findOrFail($id);

        $account_branches = AccountBranch::where('ac_id', $id)->pluck('branch_id')->toArray();
        $account_openings = AccountOpening::where('ac_id',$id)->with('bills')->get();

        $branches = Branch::whereIn('id',$account_branches)->get();
        if($branches->count() == 0){
            $branches = Branch::all();
        }
        return Inertia::render('ProjectComponents/Accounts/Account/AccountOpening',[
            'account' => $account,
            'branches' => $branches,
            'openings' => $account_openings
        ]);
    }


    public function store(OpeningRequest $request){
        return $request->save();
    }


}
