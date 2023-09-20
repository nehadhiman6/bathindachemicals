<?php

namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use App\Models\Masters\Bank;
use App\Models\Masters\Ifsc;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class IfscsController extends Controller
{
    public function index(){
        if (Gate::denies('ifscs')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/IFSC/IfscList', [
            'banks' => Bank::all()->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->name,

                ];
            }),
        ]);
    }

    public function ifscsList(Request $request){
        if (Gate::denies('ifscs')) {
            return deny();
        }
        $count = Ifsc::count();
        $filteredCount = $count;

        $ifscs = Ifsc::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $ifscs = $ifscs->where('ifsc_code', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $ifscs = $ifscs->orderBy($field_name, $asc_desc);
        }
        else{
            $ifscs = $ifscs->orderBy('id','DESC');

        }

        $ifscs = $ifscs->take($request->length);
        $filteredCount = $ifscs->count();
        if ($request->start > 0) {
            $ifscs->skip($request->start);
        }
        $ifscs = $ifscs->select()->with('bank')->get(['id', 'name']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $ifscs,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('ifscs-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $ifsc = Ifsc::findOrNew($request->form_id);

        $ifsc->fill($request->all());
        $ifsc->save();

        return reply(true, [
            'ifsc' => $ifsc
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        // dd("here1");
        $rules = [];
        $rules += [
            'ifsc_code' => 'required|string|max:100|unique:ifscs,ifsc_code,' . $request->form_id,
            'branch' => 'required|string|max:100|unique:ifscs,branch,' . $request->form_id,
        ];


        $this->validate($request, $rules);
        // dd("hjk");

    }


    public function edit($id)
    {
        if (Gate::denies('ifscs-modify')) {
            return deny();
        }
        $ifsc = Ifsc::whereId($id)->first()->load(['bank']);
        return reply(true, [
            'ifsc' => $ifsc,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('ifscs-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
