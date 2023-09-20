<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Country;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StateController extends Controller
{
    public function index(){
        if (Gate::denies('states')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/State/StateList', [
            'countries' => Country::all()->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                ];
            }),
        ]);
    }

    public function stateslist (Request $request){
        if (Gate::denies('states')) {
            return deny();
        }
        $count = State::count();
        $filteredCount = $count;

        $states = State::leftJoin('countries','states.country_id','=','countries.id');

        if ($searchStr = $request->input('search.value')) {
            $states = $states ->where(function($q) use($searchStr){
                $q->where('states.name', 'like', "%{$searchStr}%")
                ->orWhere('states.state_code', 'like', "%{$searchStr}%")
                ->orWhere('countries.name', 'like', "%{$searchStr}%");
            });
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $states = $states->orderBy($field_name, $asc_desc);
        }
        else{
            $states = $states->orderBy('id','DESC');

        }

        $states = $states->take($request->length);
        $filteredCount = $states->count();
        if ($request->start > 0) {
            $states->skip($request->start);
        }
        $states = $states->select(['states.*','countries.name as country_name'])->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $states,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('states-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {

        if (Gate::denies('states-modify')) {
            return deny();
        }
        $this->validateForm($request, $id);
        $state = State::findOrNew($request->form_id);
        $state->fill($request->all());
        $state->save();

        return reply(true, [
            'state' => $state
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:states,name,' . $request->form_id,
            'country_id' => 'required|exists:countries,id',
            'state_code'=> 'required|max:2|unique:states,state_code,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('states-modify')) {
            return deny();
        }
        $state = State::whereId($id)->first()->load([]);
        return reply(true, [
            'state' => $state,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('states-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
