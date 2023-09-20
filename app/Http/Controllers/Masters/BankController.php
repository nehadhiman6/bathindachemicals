<?php

namespace App\Http\Controllers\Masters;
use App\Http\Controllers\Controller;
use App\Models\Masters\Bank;
use App\Models\Masters\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
class BankController extends Controller
{
    public function index(){
        if (Gate::denies('banks')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Bank/BankList', [
            'banks' => Bank::all()->map(function ($city) {
                // return [
                //     'id' => $city->id,
                //     'city_name' => $city->city_name,
                //     'state_name' => $city->state ? $city->state['state_name']:"",
                // ];
            }),
        ]);
    }

    public function banksList(Request $request){
        if (Gate::denies('banks')) {
            return deny();
        }
        $count = Bank::count();
        $filteredCount = $count;

        $banks = Bank::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $banks = $banks->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $banks = $banks->orderBy($field_name, $asc_desc);
        }
        else{
            $banks = $banks->orderBy('id','DESC');

        }

        $banks = $banks->take($request->length);
        $filteredCount = $banks->count();
        if ($request->start > 0) {
            $banks->skip($request->start);
        }
        $banks = $banks->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $banks,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('banks-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $bank = Bank::findOrNew($request->form_id);
        $bank->fill($request->all());
        $bank->save();

        return reply(true, [
            'bank' => $bank
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:banks,name,' . $request->form_id,
        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('banks-modify')) {
            return deny();
        }
        $bank = Bank::whereId($id)->first()->load([]);
        return reply(true, [
            'bank' => $bank,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('banks-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
