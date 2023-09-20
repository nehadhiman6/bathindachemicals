<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\PartyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(){
        if (Gate::denies('party-categories')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/PartyCategory/PartyCategoryList', [
        ]);
    }

    public function partyCategoryList(Request $request){
        if (Gate::denies('party-categories')) {
            return deny();
        }
        $count = PartyCategory::count();
        $filteredCount = $count;

        $party_categories = PartyCategory::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $party_categories = $party_categories->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $party_categories = $party_categories->orderBy($field_name, $asc_desc);
        }
        else{
            $party_categories = $party_categories->orderBy('id','DESC');
        }

        $party_categories = $party_categories->take($request->length);
        $filteredCount = $party_categories->count();
        if ($request->start > 0) {
            $party_categories->skip($request->start);
        }

        $party_categories = $party_categories->with('packing')->selectRaw('*, CASE WHEN rate_diff_applicable = "Y" THEN "Yes" ELSE "No" END as rate_diff_applicable')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $party_categories,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('party-categories-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $party_category = PartyCategory::findOrNew($request->form_id);
        $party_category->fill($request->all());
        $party_category->save();

        return reply(true, [
            'party_category' => $party_category
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:party_cats,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('party-categories-modify')) {
            return deny();
        }
        $party_category = PartyCategory::whereId($id)->first()->load(['packing']);
        return reply(true, [
            'party_category' => $party_category,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('party-categories-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
