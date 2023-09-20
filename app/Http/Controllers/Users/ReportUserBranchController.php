<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Auth\ReportUserBranch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportUserBranchController extends Controller
{

    public function show(Request $request,$user_id)
    {
        $report_user_branches = ReportUserBranch::where('user_id',$user_id)->with('branch')->get();
        return reply(true,[
            'report_user_branches'=>$report_user_branches
        ]);
    }

    public function store(Request $request){
        $user_id = $request->user_id;
        $user = User::findOrFail($user_id);
        $old_ids = $user->report_user_branches->pluck('id')->toArray();

        $report_user_branches = new Collection();
        foreach($request->branches as $user_bran){
            $user_c = ReportUserBranch::firstOrNew(['user_id'=>$user_id,'branch_id'=>$user_bran]);
            $user_c->branch_id = $user_bran;
            $report_user_branches->add($user_c);
        }

        $new_ids = $report_user_branches->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::beginTransaction();
            $user->report_user_branches()->saveMany($report_user_branches);
            ReportUserBranch::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true,[
            'report_user_branches' =>$report_user_branches
        ]);
    }
}
