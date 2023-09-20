<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\UserBranch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserBranchController extends Controller
{

    public function show(Request $request,$user_id)
    {
        $user_branches = UserBranch::where('user_id',$user_id)->with('branch')->get();
        return reply(true,[
            'user_branches'=>$user_branches
        ]);
    }

    public function store(Request $request){
        $user_id = $request->user_id;
        $user = User::findOrFail($user_id);
        $old_ids = $user->user_branches->pluck('id')->toArray();

        $user_branches = new Collection();
        foreach($request->branches as $user_bran){
            $user_c = UserBranch::firstOrNew(['user_id'=>$user_id,'branch_id'=>$user_bran]);
            $user_c->branch_id = $user_bran;
            $user_branches->add($user_c);
        }

        $new_ids = $user_branches->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::beginTransaction();
            $user->user_branches()->saveMany($user_branches);
            UserBranch::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true,[
            'user_branches' =>$user_branches
        ]);
    }
}
