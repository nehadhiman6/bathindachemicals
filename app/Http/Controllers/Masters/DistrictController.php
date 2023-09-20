<?php

namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use App\Models\Masters\District;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DistrictController extends Controller
{
    public function index(){
        if (Gate::denies('districts')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/District/DistrictList', [
            'states' => State::all()->map(function ($state) {
                return [
                    'id' => $state->id,
                    'state_name' => $state->name,

                ];
            }),
        ]);
    }

    public function districtsList(Request $request){
        if (Gate::denies('districts')) {
            return deny();
        }
        $count = District::count();

        $filteredCount = $count;

        $districts = District::leftJoin('states','districts.state_id','=','states.id');

        if ($searchStr = $request->input('search.value')) {
            $districts = $districts ->where(function($q) use($searchStr){
                $q->where('districts.name', 'like', "%{$searchStr}%")
                ->orWhere('states.name', 'like', "%{$searchStr}%");
            });
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name =='name'){
            $districts = $districts->orderBy('districts.name', $asc_desc);
        }
        else if($field_name =='state_id'){
            $districts = $districts->orderBy('states.name',$asc_desc);
        }

        $districts = $districts->take($request->length);
        $filteredCount = $districts->count();
        if ($request->start > 0) {
            $districts->skip($request->start);
        }


        $districts = $districts->groupBy('districts.id')->select(['districts.*','states.name as state_name'])->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $districts,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('districts-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $district = District::findOrNew($request->form_id);
        $district->fill($request->all());

        $state = State::findOrFail($request->state_id);
        if($state){
            $district->country_id =   $state->country_id;
        }
        $district->save();

        return reply(true, [
            'district' => $district
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:districts,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('districts-modify')) {
            return deny();
        }
        $district = District::whereId($id)->first()->load(['state']);
        return reply(true, [
            'district' => $district,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('districts-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
