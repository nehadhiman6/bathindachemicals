<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Packing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PackingController extends Controller
{
    public function index(Request $request){
        $type = $request->getRequestUri();
        $type = str_replace('-','_',ltrim($type, $type[0]));
        if (($type == 'L' && Gate::denies('loose-packings')) || ($type == 'O' && Gate::denies('oil-packings')) || ($type == 'Q' && Gate::denies('liquor-packings'))) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Packing/PackingList', [
            'packings' => Packing::all()->map(function ($city) {
            }),
            'packing_type' => $type
        ]);
    }

    public function packingsList(Request $request){
        $type = $request->type;
        if (($type == 'L' && Gate::denies('loose-packings')) || ($type == 'O' && Gate::denies('oil-packings')) || ($type == 'Q' && Gate::denies('liquor-packings'))) {
            return deny();
        }
        $count = Packing::where('type','=', $request->type)->count();
        $filteredCount = $count;

        $packings = Packing::where('type','=', $request->type);

        if ($searchStr = $request->input('search.value')) {
            $packings = $packings->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $packings = $packings->orderBy($field_name, $asc_desc);
        }
        else{
            $packings = $packings->orderBy('id','DESC');

        }

        $packings = $packings->take($request->length);
        $filteredCount = $packings->count();
        if ($request->start > 0) {
            $packings->skip($request->start);
        }
        $packings = $packings->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $packings,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        $type = $request->type;
        if (($type == 'L' && Gate::denies('loose-packings-add')) || ($type == 'O' && Gate::denies('oil-packings-add')) || ($type == 'Q' && Gate::denies('liquor-packings-add'))) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $packing = Packing::findOrNew($request->form_id);
        $packing->fill($request->all());
        $packing->save();

        return reply(true, [
            'packing' => $packing
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:packings,name,' . $request->form_id.',id,type,'.$request->type,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        $packing = Packing::whereId($id)->first()->load([]);
        $type = $packing ? $packing->type:'';
        if (($type == 'L' && Gate::denies('loose-packings-modify')) || ($type == 'O' && Gate::denies('oil-packings-modify')) || ($type == 'Q' && Gate::denies('liquor-packings-modify'))) {
            return deny();
        }
        return reply(true, [
            'packing' => $packing,
        ]);
    }

    public function update(Request $request, $id)
    {
        $type = $request->type;
        if (($type == 'L' && Gate::denies('loose-packings-modify')) || ($type == 'O' && Gate::denies('oil-packings-modify')) || ($type == 'Q' && Gate::denies('liquor-packings-modify'))) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
