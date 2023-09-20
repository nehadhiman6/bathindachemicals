<?php

namespace App\Http\Controllers\Masters;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Masters\Kanda;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class KandaController extends Controller
{
    public function index(){
        if (Gate::denies('kanda')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Kanda/KandaList');
    }

    public function KandaList(Request $request){
        if (Gate::denies('kanda')) {
            return deny();
        }
        $count = Kanda::count();
        $filteredCount = $count;

        $kandas = Kanda::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $kandas = $kandas->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $kandas = $kandas->orderBy($field_name, $asc_desc);
        }
        else{
            $kandas = $kandas->orderBy('id','DESC');

        }

        $kandas = $kandas->take($request->length);
        $filteredCount = $kandas->count();
        if ($request->start > 0) {
            $kandas->skip($request->start);
        }
        $kandas = $kandas->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $kandas,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('kanda-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $bag = Kanda::findOrNew($request->form_id);
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
            'name' => 'required|string|max:25|unique:'.getCompDbConn().'.kanda,name,' . $request->form_id,
            'api_key' => 'required|max:100',
            'criteria' => 'required|max:10',
            'prefix' => 'required|string|max:10|unique:'.getCompDbConn().'.kanda,prefix,' . $request->form_id,
            'tkt_prefix' => 'required|max:10',
            'vsiteid' => 'required|max:10',
            'url' => 'required|max:100',
        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('kanda-modify')) {
            return deny();
        }
        $kanda = Kanda::whereId($id)->first();
        return reply(true, [
            'kanda' => $kanda,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('kanda-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
