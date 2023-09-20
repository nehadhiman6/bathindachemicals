<?php

namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use App\Models\Masters\Company;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(){
        if (Gate::denies('companies')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Company/CompanyList', [
            'years' =>getYears()
        ]);
    }

    public function companiesList(Request $request){
        if (Gate::denies('companies')) {
            return deny();
        }
        $count = Company::count();
        $filteredCount = $count;

        $companies = Company::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $companies = $companies->where('company_name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $companies = $companies->orderBy($field_name, $asc_desc);
        }
        else{
            $companies = $companies->orderBy('id','DESC');

        }

        $companies = $companies->take($request->length);
        $filteredCount = $companies->count();
        if ($request->start > 0) {
            $companies->skip($request->start);
        }
        $companies = $companies->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $companies,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('companies-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $company = Company::findOrNew($request->form_id);
        $company->fill($request->all());
        $company->save();
        if ($request->form_id == 0) {
            $company->shared_comp_id = nextnoShared('company');
        }
        $company->save();

        return reply(true, [
            'company' => $company
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'company_name' => 'required|string|max:100|unique:companies,company_name,' . $request->form_id,
            'print_name' => 'required|string|max:100|unique:companies,print_name,' . $request->form_id,
            'print_name' => 'required|string|max:100|unique:companies,print_name,' . $request->form_id,
            'print_name' => 'required|string|max:100|unique:companies,print_name,' . $request->form_id,
            'print_name' => 'required|string|max:100|unique:companies,print_name,' . $request->form_id,

        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('companies-modify')) {
            return deny();
        }
        $company = Company::whereId($id)->first()->load([]);
        return reply(true, [
            'company' => $company,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('companies-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
