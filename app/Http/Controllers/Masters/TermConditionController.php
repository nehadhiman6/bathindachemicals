<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\TermCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TermConditionController extends Controller
{
    public function index(){
        if (Gate::denies('term-conditions')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/TermCondition/TermConditionList');
    }

    public function termConditionsList(Request $request){
        if (Gate::denies('term-conditions')) {
            return deny();
        }
        $count = TermCondition::count();
        $filteredCount = $count;

        $term_conditions = TermCondition::orderBy('type', 'ASC')
        ->orderBy('sno', 'ASC');

        if ($searchStr = $request->input('search.value')) {
            $term_conditions = $term_conditions->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $term_conditions = $term_conditions->orderBy($field_name, $asc_desc);
        }
        else{
            $term_conditions = $term_conditions->orderBy('id','DESC');

        }

        $term_conditions = $term_conditions->take($request->length);
        $filteredCount = $term_conditions->count();
        if ($request->start > 0) {
            $term_conditions->skip($request->start);
        }
        $term_conditions = $term_conditions->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $term_conditions,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('term-conditions-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $term_condition = TermCondition::findOrNew($request->form_id);
        $term_condition->fill($request->all());
        $term_condition->save();

        return reply(true, [
            'term_condition' => $term_condition
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'type' => 'required|string',
            'sno' => 'unique:terms_and_conditions,sno,'.$request->form_id.',id,type,'.$request->type,
            'terms' => 'required|string',
        ];

        $this->validate($request, $rules);
    }

    public function edit($id)
    {
        if (Gate::denies('term-conditions-modify')) {
            return deny();
        }
        $term_condition = TermCondition::whereId($id)->first()->load([]);
        return reply(true, [
            'term_condition' => $term_condition,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('term-conditions-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
