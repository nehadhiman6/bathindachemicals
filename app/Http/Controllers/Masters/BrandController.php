<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index(){
        if (Gate::denies('brands')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/Brand/BrandList', [
        ]);
    }

    public function brandsList(Request $request){
        if (Gate::denies('brands')) {
            return deny();
        }
        $count = Brand::count();
        $filteredCount = $count;

        $brands = Brand::where('id','!=', 0);

        if ($searchStr = $request->input('search.value')) {
            $brands = $brands->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $brands = $brands->orderBy($field_name, $asc_desc);
        }
        else{
            $brands = $brands->orderBy('id','DESC');
        }

        $brands = $brands->take($request->length);
        $filteredCount = $brands->count();
        if ($request->start > 0) {
            $brands->skip($request->start);
        }
        $brands = $brands->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $brands,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('brands-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $brand = Brand::findOrNew($request->form_id);
        $brand->fill($request->all());
        $brand->save();

        return reply(true, [
            'brand' => $brand
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'name' => 'required|string|max:100|unique:brands,name,' . $request->form_id,
        ];

        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('brands-modify')) {
            return deny();
        }
        $brand = Brand::whereId($id)->first()->load([]);
        return reply(true, [
            'brand' => $brand,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('brands-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
