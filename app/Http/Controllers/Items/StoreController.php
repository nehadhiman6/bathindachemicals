<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index(){
        if (Gate::denies('stores')) {
            return deny();
        }
        // return Inertia::render('ProjectComponents/Masters/City/CityList');
        return Inertia::render('ProjectComponents/Items/Store/StoreList');
    }

    public function storesList(Request $request){
        if (Gate::denies('stores')) {
            return deny();
        }
        $count = Store::count();
        $filteredCount = $count;

        $stores = Store::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $stores = $stores->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $stores = $stores->orderBy($field_name, $asc_desc);
        }
        else{
            $stores = $stores->orderBy('id','DESC');

        }

        $stores = $stores->take($request->length);
        $filteredCount = $stores->count();
        if ($request->start > 0) {
            $stores->skip($request->start);
        }
        $stores = $stores->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $stores,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('stores-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $store = Store::findOrNew($request->form_id);
        $store->fill($request->all());
        $store->save();

        return reply(true, [
            'store' => $store
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:stores,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('stores-modify')) {
            return deny();
        }
        $store = Store::whereId($id)->first()->load([]);
        return reply(true, [
            'store' => $store,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('stores-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
