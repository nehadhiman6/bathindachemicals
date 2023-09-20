<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\City;
use App\Models\Masters\District;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CityController extends Controller
{
    public function index(){
        if (Gate::denies('cities')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/City/CityList', [
            'districts' => District::all()->map(function ($district) {
                return [
                    'id' => $district->id,
                    'name' => $district->name,
                ];
            }),
        ]);
    }

    public function citieslist (Request $request){
        if (Gate::denies('cities')) {
            return deny();
        }
        $count = City::count();
        $filteredCount = $count;

        $cities = City::leftJoin('districts','districts.id','=','cities.district_id')
                ->leftJoin('states','districts.state_id','=','states.id');

        if ($searchStr = $request->input('search.value')) {
            $cities = $cities
            ->where(function($q) use($searchStr){
                $q->where('cities.name', 'like', "%{$searchStr}%")
                ->orWhere('districts.name', 'like', "%{$searchStr}%")
                ->orWhere('states.name', 'like', "%{$searchStr}%");
            });
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name =='name'){
            $cities = $cities->orderBy('cities.name', $asc_desc);
        }
        else if($field_name =='district_id'){
            $cities = $cities->orderBy('districts.name',$asc_desc);
        }
        else if($field_name =='state_id'){
            $cities = $cities->orderBy('states.name',$asc_desc);
        }

        $cities = $cities->take($request->length);
        $filteredCount = $cities->count();
        if ($request->start > 0) {
            $cities->skip($request->start);
        }
        $cities = $cities->groupBy('cities.id')->select(['cities.*','states.name as state_name','districts.name as district_name'])->with('district.state')->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $cities,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('cities-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $city = City::findOrNew($request->form_id);
        $city->fill($request->all());

        $district = District::findOrFail($request->district_id);
        if($district){
            $city->state_id =   $district->state_id;
        }
        $state = State::findOrFail($district->state_id);
        if($state){
            $city->country_id =   $state->country_id;
        }

        $city->save();

        return reply(true, [
            'city' => $city
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:cities,name,' . $request->form_id,
            // 'district_id' => 'required|exists:cities,district_id',
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('cities-modify')) {
            return deny();
        }
        $city = City::whereId($id)->first()->load(['district.state']);
        return reply(true, [
            'city' => $city,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('cities-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }

}
