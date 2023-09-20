<?php

namespace App\Http\Controllers\Masters;

use Inertia\Inertia;
use App\Models\Masters\Bag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class BagController extends Controller
{
    public function index(){
        if (Gate::denies('bags')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Bag/BagList');
    }

    public function bagsList(Request $request){
        if (Gate::denies('bags')) {
            return deny();
        }
        $count = Bag::count();
        $filteredCount = $count;

        $bags = Bag::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $bags = $bags->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $bags = $bags->orderBy($field_name, $asc_desc);
        }
        else{
            $bags = $bags->orderBy('id','DESC');

        }

        $bags = $bags->take($request->length);
        $filteredCount = $bags->count();
        if ($request->start > 0) {
            $bags->skip($request->start);
        }
        $bags = $bags->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $bags,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('bags-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $bag = Bag::findOrNew($request->form_id);
        $bag->fill($request->all());
        $bag->save();

        return reply(true, [
            'bag' => $bag
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:25|unique:bags,name,' . $request->form_id,
            'weight' => 'required|numeric|min:0.001',

        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('bags-modify')) {
            return deny();
        }
        $bag = Bag::whereId($id)->first()->load([]);
        return reply(true, [
            'bag' => $bag,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('bags-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
