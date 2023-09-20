<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CountryController extends Controller
{
    public function index(){
        if (Gate::denies('countries')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Country/CountryList', [
            'countries' => Country::all()->map(function ($city) {
                // return [
                //     'id' => $city->id,
                //     'city_name' => $city->city_name,
                //     'state_name' => $city->state ? $city->state['state_name']:"",
                // ];
            }),
        ]);
    }

    public function countriesList(Request $request){
        if (Gate::denies('countries')) {
            return deny();
        }
        $count = Country::count();
        $filteredCount = $count;

        $countries = Country::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $countries = $countries->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $countries = $countries->orderBy($field_name, $asc_desc);
        }
        else{
            $countries = $countries->orderBy('id','DESC');

        }

        $countries = $countries->take($request->length);
        $filteredCount = $countries->count();
        if ($request->start > 0) {
            $countries->skip($request->start);
        }
        $countries = $countries->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $countries,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('countries-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $country = Country::findOrNew($request->form_id);
        $country->fill($request->all());
        $country->save();

        return reply(true, [
            'country' => $country
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:countries,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('countries-modify')) {
            return deny();
        }
        $country = Country::whereId($id)->first()->load([]);
        return reply(true, [
            'country' => $country,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('countries-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
