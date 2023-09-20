<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\UserBranch;
use App\Models\Auth\UserCompany;
use App\Models\Masters\Branch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserCompanyController extends Controller
{
    public function show(Request $request,$user_id)
    {
        $user_companies = UserCompany::where('user_id',$user_id)->with('company')->get();
        return reply(true,[
            'user_companies'=>$user_companies
        ]);
    }

    public function store(Request $request){
        $user_id = $request->user_id;
        $user = User::findOrFail($user_id);
        $old_ids = $user->user_companies->pluck('id')->toArray();

        $user_companies = new Collection();
        foreach($request->companies as $user_comp){
            $user_c = UserCompany::firstOrNew(['user_id'=>$user_id,'company_id'=>$user_comp]);
            $user_c->company_id = $user_comp;
            $user_companies->add($user_c);
        }

        $new_ids = $user_companies->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);

        $company_ids = UserCompany::whereIn('id',$detach)->pluck('company_id')->toArray();
        $detach_branches = Branch::whereIn('company_id',$company_ids)->pluck('id')->toArray();

        DB::beginTransaction();
            $user->user_companies()->saveMany($user_companies);
            UserCompany::whereIn('id',$detach)->delete();
            UserBranch::whereIn('branch_id',$detach_branches)->where('user_id',$user_id)->delete();
        DB::commit();
        return reply(true,[
            'user_companies' =>$user_companies
        ]);
    }
}
