<?php

namespace App\Http\Controllers\Masters;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\Masters\ChequeSetting;

class ChequeSettingController extends Controller
{

    public function index(){
        if (Gate::denies('cheque-setting')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/ChequeSetting/ChequeSettingList', [
        ]);
    }

    public function chequeSettingList(Request $request)
    {
        if (Gate::denies('cheque-setting')) {
            return deny();
        }
        $count = ChequeSetting::count();
        $filteredCount = $count;

        $cheque_settings = ChequeSetting::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            // $cheque_settings = $cheque_settings->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $cheque_settings = $cheque_settings->orderBy($field_name, $asc_desc);
        }
        else{
            $cheque_settings = $cheque_settings->orderBy('id','DESC');

        }

        $cheque_settings = $cheque_settings->take($request->length);
        $filteredCount = $cheque_settings->count();
        if ($request->start > 0) {
            $cheque_settings->skip($request->start);
        }
        $cheque_settings = $cheque_settings->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $cheque_settings,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('cheque-setting-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $cheque = ChequeSetting::findOrNew($request->form_id);
        $cheque->fill($request->all());
        $cheque->save();

        return reply(true, [
            'cheque' => $cheque
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'acid'  => 'required',
            'width'  => 'required',
            'height'  => 'required',
            'date_top'  => 'required',
            'date_left'  => 'required',
            'name_top'  => 'required',
            'name_left'  => 'required',
            'name_char'  => 'required',
            'amt_w1_top'  => 'required',
            'amt_w1_left'  => 'required',
            'amt_w1_char'  => 'required',
            'amt_w2_top'  => 'required',
            'amt_w2_left'  => 'required',
            'amt_w2_char'  => 'required',
            'amt_f_top'  => 'required',
            'amt_f_left'  => 'required',
            'for_top'  => 'required',
            'for_left'  => 'required',
            'for_char'  => 'required',
            'for_flag'  => 'required',
            // 'sign1_top'  => 'required',
            'sign1_left'  => 'required',
            'sign1_flag'  => 'required',
            'sign1'  => 'required',
            'sign2_top'  => 'required',
            'sign2_left'  => 'required',
            'sign2_flag'  => 'required',
            'sign2'  => 'required',
            'sign3_left'  => 'required',
            // 'sign3_Flag'  => 'required',
            'sign3'  => 'required',
            'print_mode'  => 'required',
            'date_font'  => 'required',
            'name_font'  => 'required',
            'amt_w1_font'  => 'required',
            'amt_w2_font'  => 'required',
            'amt_f_font'  => 'required',
            'tds_flag'  => 'required',
            // 'tds_top'  => 'required',
            // 'tds_left'  => 'required',
            // 'tds_char'  => 'required',
            // 'tds_font'  => 'required',

        ];

        $this->validate($request, $rules);
    }

    public function edit($id)
    {
        if (Gate::denies('cheque-setting-modify')) {
            return deny();
        }
        $cheque = ChequeSetting::whereId($id)->first()->load(['account']);
        return reply(true, [
            'cheque' => $cheque,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('cheque-setting-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
