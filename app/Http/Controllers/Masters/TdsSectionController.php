<?php

namespace App\Http\Controllers\Masters;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masters\TdsSection;
use Illuminate\Support\Facades\Gate;

class TdsSectionController extends Controller
{
    public function index(){
        if (Gate::denies('tds-section')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/TdsSection/TdsSectionList');
    }

    public function tdsSectionList (Request $request){
        if (Gate::denies('tds-section')) {
            return deny();
        }
        $count = TdsSection::count();
        $filteredCount = $count;

        $tds_sections = TdsSection::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $tds_sections = $tds_sections ->where(function($q) use($searchStr){
                $q->where('section', 'like', "%{$searchStr}%");
            });
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $tds_sections = $tds_sections->orderBy($field_name, $asc_desc);
        }
        else{
            $tds_sections = $tds_sections->orderBy('id','DESC');

        }

        $tds_sections = $tds_sections->take($request->length);
        $filteredCount = $tds_sections->count();
        if ($request->start > 0) {
            $tds_sections->skip($request->start);
        }
        $tds_sections = $tds_sections->select(['tds_sections.*'])->get()->load(['account1','account2','account3','account4','account5']);
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $tds_sections,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }


    public function store(Request $request)
    {
        if (Gate::denies('tds-section-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {

        if (Gate::denies('tds-section-add')) {
            return deny();
        }
        $this->validateForm($request, $id);
        $tds_section = TdsSection::findOrNew($request->form_id);
        $tds_section->fill($request->all());
        $tds_section->save();

        return reply(true, [
            'tds_section' => $tds_section
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'section' => 'required|string|max:10|unique:tds_sections,section,' . $request->form_id,
            'rate1' => 'required',
            'ac_id1'=> 'required|exists:accounts,id',
            'rate2' => 'nullable|numeric',
            'rate3' => 'nullable|numeric',
            'non_pan_rate' => 'nullable|numeric',
            'higher_rate' => 'nullable|numeric',

        ];
        if($request->rate2 > 0){
            $rules += [
                'ac_id2' => 'required|exists:accounts,id',
            ];
        }
        if($request->rate3 > 0){
            $rules += [
                'ac_id3' => 'required|exists:accounts,id',
            ];
        }
        if($request->non_pan_rate > 0){
            $rules += [
                'ac_id4' => 'required|exists:accounts,id',
            ];
        }
        if($request->higher_rate > 0){
            $rules += [
                'ac_id5' => 'required|exists:accounts,id',
            ];
        }

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('tds-section-modify')) {
            return deny();
        }
        $tds_section = TdsSection::find($id)->load(['account1','account2','account3','account4','account5']);
        return reply(true, [
            'tds_section' => $tds_section,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('tds-section-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
