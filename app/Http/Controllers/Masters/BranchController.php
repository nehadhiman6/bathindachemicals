<?php

namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use App\Models\Masters\Branch;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Masters\Company;
use Illuminate\Support\Facades\Gate;

class BranchController extends Controller
{
    public function show(Request $request, $company_id){
        $company=Company::FindorFail($company_id);
        return Inertia::render('ProjectComponents/Masters/Branch/BranchList', [
                'company_id' => $company_id,
                'company_name' => $company->company_name
        ]);
    }
    public function index(){
        if (Gate::denies('branches')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Branch/BranchList', [
                    'branches' => Company::all()->map(function ($branch) {
                        return [
                            // 'id' => $branch->id,
                            // 'branch_name' => $branch->name,

                        ];
                    }),
                ]);
    }

    public function branchesList(Request $request){
        if (Gate::denies('branches')) {
            return deny();
        }
        $count = Branch::count();
        $filteredCount = $count;

        $branches = Branch::where('company_id','=', $request->company_id);

        if ($searchStr = $request->input('search.value')) {
            $branches = $branches->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $branches = $branches->orderBy($field_name, $asc_desc);
        }
        else{
            $branches = $branches->orderBy('id','DESC');

        }

        $branches = $branches->take($request->length);
        $filteredCount = $branches->count();
        if ($request->start > 0) {
            $branches->skip($request->start);
        }


        $branches = $branches->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $branches,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('branches-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $branch = Branch::findOrNew($request->form_id);
        $branch->fill($request->all());

        // $state = Branch::findOrFail($request->state_id);
        // if($state){
        //     $branch->country_id =   $state->country_id;
        // }
        $branch->save();

        return reply(true, [
            'branch' => $branch
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:branches,name,' . $request->form_id,
            'email'=>'nullable|email'
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('branches-modify')) {
            return deny();
        }
        $branch = Branch::whereId($id)->first()->load(['city','ifsc','account']);
        return reply(true, [
            'branch' => $branch,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('branches-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

    public function getSkipBranches($id){
        $branch = Branch::find($id);
        $branches = explode(",",$branch->skip_add_branches);
        $data = [];
        foreach ($branches as $value) {
            $b = Branch::find($value);
            $data[$value]= $b->name;
        }
        return reply(true, [
            'data' => $data
        ]);
    }

    public function addSkipBranches(Request $request){
        $branch = Branch::findOrFail($request->form_id);
        $branch_ids = implode(",", $request->skip_branches);
        $branch->skip_add_branches =$branch_ids;
        $branch->update();
        return reply(true, [
            'branch' => $branch
        ]);

    }
}
