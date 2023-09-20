<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\ItemFreight;
use App\Models\Company\ItemFreightDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ItemFreightController extends Controller
{
    public function index(Request $request){
        return Inertia::render('ProjectComponents/Masters/ItemFreight/ItemFreightList',[
        ]);
    }

    public function itemFreightsList (Request $request){
        if (Gate::denies('item-freights')) {
            return deny();
        }
        $count = ItemFreight::count();
        $filteredCount = $count;

        $item_freights = ItemFreight::leftJoin(getSharedDb().'.items','items.id','=','item_freights.item_id');

        if ($searchStr = $request->input('search.value')) {
            $item_freights = $item_freights->where('items.item_name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'item_freights.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='item_name'){
            $field_name = 'item_freights.item_name';
        }

        if($field_name){
            $item_freights = $item_freights->orderBy($field_name, $asc_desc);
        }
        else{
            $item_freights = $item_freights->orderBy('item_freights.id','DESC');
        }
        $item_freights = $item_freights->take($request->length);
        $filteredCount = $item_freights->count();
        if ($request->start > 0) {
            $item_freights->skip($request->start);
        }
        $item_freights = $item_freights->groupBy('item_freights.id')->select('item_freights.*','items.item_name as item_name')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $item_freights,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store (Request $request){
        if (Gate::denies('item-freights-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    public function update (Request $request){
        if (Gate::denies('item-freights-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    public function edit(Request $request,$id){
        if (Gate::denies('item-freights-modify') || Gate::denies('item-freights-view')) {
            return deny();
        }
        $item_freight = ItemFreight::whereId($id)->with(['item','item_freight_details.city'])->first();
        return reply(true, [
            'item_freight' => $item_freight,
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'wef_date'=>'required|date_format:d-m-Y',
            'item_id' => 'required|unique:'.getCompDbConn().'.item_freights,item_id,'. $request->form_id.',id,wef_date,' . getDateFormat($request->wef_date,'ymd'),
            'item_freight_details.*.city_id'=>'required|numeric|exists:cities,id',
            'item_freight_details.*.freight'=>'required|numeric',
            'uid' => 'nullable|unique:' . getCompDbConn() . '.sales_contracts,uid,' . $request->form_id,
        ];

        $messages = [
            'item_id.unique'=>'The record with the same item and date already exists. Please ensure that you do not create duplicates.',
            'item_freight_details.*.city_id.required'=>'City Mandatory'
        ];

        $this->validate($request, $rules,$messages);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $item_freight = ItemFreight::findOrNew($request->form_id);
        $item_freight->fill($request->all());
        $old_ids = $item_freight->item_freight_details()->pluck('id')->toArray();

        $item_freight_details_coll = new Collection();
        foreach($request->item_freight_details as $item_freight_det){
            $item_freight_detail = ItemFreightDetail::findOrNew($item_freight_det['id']);
            $item_freight_detail->fill($item_freight_det);
            $item_freight_details_coll->add($item_freight_detail);
        }
        $new_ids = $item_freight_details_coll->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::connection(getCompDbConn())->beginTransaction();
            if($request->form_id>0){
                $uid = addUid($item_freight->uid);
            }
            $item_freight->save();
            $item_freight->item_freight_details()->saveMany($item_freight_details_coll);
            ItemFreightDetail::whereIn('id',$detach)->delete();
        DB::connection(getCompDbConn())->commit();


        return reply(true, [
            'item_freight' => $item_freight
        ]);
    }
}
