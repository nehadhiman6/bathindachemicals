<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Account;
use App\Models\Accounts\LowerTdsSetting;
use Illuminate\Support\Facades\Gate;

class LowerTdsSettingController extends Controller
{
    public function show($id){
        if (Gate::denies('lower-tds-setting')) {
            return deny();
        }
        $account = Account::find($id);
        return Inertia::render('ProjectComponents/Accounts/LowerTds/LowerTdsList',[
            'account' => $account
        ]);
    }

    public function lowerTdsSettingList(Request $request){
        if (Gate::denies('lower-tds-setting')) {
            return deny();
        }
        $count = LowerTdsSetting::count();
        $filteredCount = $count;

        $lower_tds_settings = LowerTdsSetting::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $lower_tds_settings = $lower_tds_settings->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $lower_tds_settings = $lower_tds_settings->orderBy($field_name, $asc_desc);
        }
        else{
            $lower_tds_settings = $lower_tds_settings->orderBy('id','DESC');

        }

        $lower_tds_settings = $lower_tds_settings->take($request->length);
        $filteredCount = $lower_tds_settings->count();
        if ($request->start > 0) {
            $lower_tds_settings->skip($request->start);
        }
        $lower_tds_settings = $lower_tds_settings->get()->load('tds_account','tds_section');
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $lower_tds_settings,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('lower-tds-setting-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $lower_tds_setting = LowerTdsSetting::findOrNew($request->form_id);
        $lower_tds_setting->fill($request->all());
        $lower_tds_setting->save();

        return reply(true, [
            'lower_tds_setting' => $lower_tds_setting
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'tds_sec_id' => 'required|exists:tds_sections,id',
            'certificate_no'=> 'required|max:10',
            'date'=> 'required|date_format:d-m-Y',
            'rate'=> 'required',
            'acid_tds'=> 'required|exists:accounts,id',
            'from_date'=> 'required|date_format:d-m-Y',
            'to_date'=> 'required|date_format:d-m-Y',
            'amount'=> 'required',
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('lower-tds-setting-modify')) {
            return deny();
        }
        $lower_tds_setting = LowerTdsSetting::whereId($id)->first()->load('tds_account','tds_section');
        return reply(true, [
            'lower_tds_setting' => $lower_tds_setting,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('lower-tds-setting-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
