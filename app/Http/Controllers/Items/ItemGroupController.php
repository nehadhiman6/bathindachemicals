<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\ItemGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemGroupController extends Controller
{
    public function index(Request $request){

        $type = $request->getRequestUri();
        $type = str_replace('-','_',ltrim($type, $type[0]));
        return Inertia::render('ProjectComponents/Items/ItemGroup/ItemGroupList', [
            'master_type' => $type
        ]);
    }

    public function itemGroupList(Request $request){
        $count = ItemGroup::count();
        $filteredCount = $count;
        $type = $request['type'] == 'main_groups' ? 'M':($request['type']  == 'sub_groups' ? 'S1' :'S2');
        $item_group = ItemGroup::where('type','=', $type);

        if ($searchStr = $request->input('search.value')) {
            $item_group = $item_group->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $item_group = $item_group->orderBy($field_name, $asc_desc);
        }
        else{
            $item_group = $item_group->orderBy('id','DESC');

        }

        $item_group = $item_group->take($request->length);
        $filteredCount = $item_group->count();
        if ($request->start > 0) {
            $item_group->skip($request->start);
        }
        $item_group = $item_group->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $item_group,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $item_group = ItemGroup::findOrNew($request->form_id);
        $item_group->fill($request->all());
        $item_group->save();

        return reply(true, [
            'item_group' => $item_group
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:item_groups,name,' . $request->form_id . ',id,type,' . $request['type'],
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        $item_group = ItemGroup::whereId($id)->first();
        $item_group->load(['main_group','sub_group']);
        return reply(true, [
            'item_group' => $item_group,
        ]);
    }

    public function update(Request $request, $id)
    {
        return $this->saveForm($request, $id);
    }
}
