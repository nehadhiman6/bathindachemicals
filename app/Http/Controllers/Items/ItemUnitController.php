<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\ItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ItemUnitController extends Controller
{
    public function index(){
        if (Gate::denies('item-units')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Items/ItemUnit/ItemUnitList');
    }

    public function itemUnitsList(Request $request){
        if (Gate::denies('item-units')) {
            return deny();
        }
        $count = ItemUnit::count();
        $filteredCount = $count;

        $item_units = ItemUnit::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $item_units = $item_units->where('uqc_name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $item_units = $item_units->orderBy($field_name, $asc_desc);
        }
        else{
            $item_units = $item_units->orderBy('id','DESC');

        }

        $item_units = $item_units->take($request->length);
        $filteredCount = $item_units->count();
        if ($request->start > 0) {
            $item_units->skip($request->start);
        }
        $item_units = $item_units->with('uqc')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $item_units,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function item_unit(Request $request)
    {
        if (Gate::denies('item-units-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $item_unit = ItemUnit::findOrNew($request->form_id);
        $item_unit->fill($request->all());
        $item_unit->save();

        return reply(true, [
            'item_unit' => $item_unit
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'unit_name' => 'required|string|max:100|unique:item_units,unit_name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('item-units-modify')) {
            return deny();
        }
        $item_unit = ItemUnit::whereId($id)->first()->load(['uqc']);
        return reply(true, [
            'item_unit' => $item_unit,
        ]);
    }
    public function store(Request $request)
    {
        if (Gate::denies('item-units-add')) {
            return deny();
        }
        return $this->saveForm($request,0);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('item-units-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
