<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\Brand;
use App\Models\Masters\BrandCategory;
use App\Models\Masters\PartyCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BrandCategoryController extends Controller
{
    public function show(Request $request, $typeable_id){

        $this->validate($request,[
            'type'=>'required|in:brand,category'
        ]);
        $typeable = null;
        $data= [];
        if($request->type == 'brand'){
            $typeable = Brand::findorFail($typeable_id);
            $data = $typeable->with('brand_categories.category');
        }
        else if($request->type == 'category'){
            $typeable = PartyCategory::FindorFail($typeable_id);
            $data = $typeable->with('category_brands.brand');
        }
        return Inertia::render('ProjectComponents/Masters/CategoriesBrands/CategoriesBrandsList', [
            'typeable_id' => $typeable_id,
            'typeable_name' => $typeable->name,
            'type'=>$request->type,
            'data'=>$data
        ]);
    }



    public function brandsCategoryList(Request $request){

        $id = $request->type == 'brand'? 'brand_id':'category_id';
        $count = BrandCategory::count();
        $filteredCount = $count;

        $categories_brands = BrandCategory::where($id,'=', $request->typeable_id);

        if ($searchStr = $request->input('search.value')) {
            $categories_brands = $categories_brands->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $categories_brands = $categories_brands->orderBy($field_name, $asc_desc);
        }
        else{
            $categories_brands = $categories_brands->orderBy('id','DESC');

        }

        $categories_brands = $categories_brands->take($request->length);
        $filteredCount = $categories_brands->with(['brand','category'])->count();
        if ($request->start > 0) {
            $categories_brands->skip($request->start);
        }

        $categories_brands = $categories_brands->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $categories_brands,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request){
        $this->validate($request,[
            'type' =>'required',
            'typeable_id'=>'required'
        ]);

        $typeable = null;
        $old_ids = [];
        if($request->type == 'brand'){
            $typeable = Brand::findorFail($request->typeable_id);
            $old_ids = $typeable->brand_categories()->pluck('id')->toArray();
        }
        else if($request->type == 'category'){
            $typeable = PartyCategory::FindorFail($request->typeable_id);
            $old_ids = $typeable->category_brands()->pluck('id')->toArray();
        }

        $typeable_coll = new Collection();
        foreach($request->typeable_data as $data){
            if($request->type == 'category'){
                $target = BrandCategory::firstOrNew(['brand_id'=>$data,'category_id'=>$request->typeable_id]);
            }
            elseif($request->type == 'brand'){
                $target = BrandCategory::firstOrNew(['brand_id'=>$request->typeable_id,'category_id'=>$data]);
            }
            $typeable_coll->add($target);
        }

        $new_ids = $typeable_coll->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);

        DB::connection(getCompDbConn())->beginTransaction();
            if($request->type == 'category'){
                $typeable->category_brands()->saveMany($typeable_coll);
            }
            elseif($request->type == 'brand'){
                $typeable->brand_categories()->saveMany($typeable_coll);
            }
            BrandCategory::whereIn('id',$detach)->delete();
        DB::connection(getCompDbConn())->commit();


        return reply(true);

    }



    public function edit(Request $request,$id)
    {

        if($request->type == 'brand'){
            $typeable = Brand::findorFail($id);
            $typeable->load('brand_categories.category');
        }
        else if($request->type == 'category'){
            $typeable = PartyCategory::FindorFail($id);
            $typeable->load('category_brands.brand');

        }
        return reply(true, [
            'typeable' => $typeable,
        ]);
    }
}
