<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Branch;
use App\Models\Masters\BranchPrefix;
use App\Models\Masters\Prefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class BranchPrefixController extends Controller
{
    public function index(){
        if (Gate::denies('branch-prefixes')) {
            return deny();
        }

        $branch_prefixes  = BranchPrefix::all();
        $branches  = Branch::all();
        $prefixes = Prefix::all();

        return Inertia::render('ProjectComponents/Masters/BranchPrefixes/BranchPrefixes', [
            'prefixes' => $prefixes,
            'branches' => $branches,
            'branch_prefixes' =>$branch_prefixes
        ]);
    }

    public function store(Request $request){
        if (Gate::denies('branch-prefixes-add') || Gate::denies('branch-prefixes-add')) {
            return deny();
        }
        $this->validateForm($request);
        return $this->saveForm($request);
    }

    public function validateForm($request){
        $this->validate($request,[ //
        ]);
    }
    public function saveForm($request){
        DB::beginTransaction();
        foreach($request->branch_prefixes as $branch_prefixes){
            foreach($branch_prefixes['prefixes'] as $prefix){
                $branch_prefix = BranchPrefix::firstOrNew(['prefix_id'=>$prefix['prefix_id'],'branch_id'=>$prefix['branch_id']]);
                $branch_prefix->prefix_value = $prefix['prefix_value'];
                $branch_prefix->save();
            }
        }
        DB::commit();

        return reply(true,[

        ]);
    }
}
