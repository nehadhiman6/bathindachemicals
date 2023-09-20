<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Accounts\PayTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PayTermController extends Controller
{
    public function index(){
        if (Gate::denies('pay-terms')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Accounts/PayTerm/PayTermList');
    }

    public function payTermsList(Request $request){
        if (Gate::denies('pay-terms')) {
            return deny();
        }
        $count = PayTerm::count();
        $filteredCount = $count;

        $pay_terms = PayTerm::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $pay_terms = $pay_terms->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $pay_terms = $pay_terms->orderBy($field_name, $asc_desc);
        }
        else{
            $pay_terms = $pay_terms->orderBy('id','DESC');

        }

        $pay_terms = $pay_terms->take($request->length);
        $filteredCount = $pay_terms->count();
        if ($request->start > 0) {
            $pay_terms->skip($request->start);
        }
        $pay_terms = $pay_terms->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $pay_terms,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('pay-terms-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $pay_term = PayTerm::findOrNew($request->form_id);
        $pay_term->fill($request->all());
        $pay_term->save();

        return reply(true, [
            'pay_term' => $pay_term
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:pay_terms,name,' . $request->form_id,
            'days_1' => 'required|string|max:191',
            'percentage_1' => 'required|numeric',
        ];

        if($request['percentage_1'] +$request['percentage_2'] +$request['percentage_3'] +$request['percentage_4'] != 100){
            throw ValidationException::withMessages(['percentage_1' => 'Sum of all four percentages can not be smaller or greater than 100']);
        }

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('pay-terms-modify')) {
            return deny();
        }
        $pay_term = PayTerm::whereId($id)->first();
        return reply(true, [
            'pay_term' => $pay_term,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('pay-terms-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}

