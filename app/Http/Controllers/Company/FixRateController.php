<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\FixRate;
use App\Models\Company\FixRateDetail;
use App\Models\Company\ItemFreight;
use App\Models\Company\ItemFreightDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FixRateController extends Controller
{
    public function index(Request $request){
        return Inertia::render('ProjectComponents/Masters/FixRate/FixRateList',[
        ]);
    }

    public function fixRatesList (Request $request){
        if (Gate::denies('fix-rates')) {
            return deny();
        }
        $count = FixRate::count();
        $filteredCount = $count;

        $fix_rates = FixRate::leftJoin('fix_rate_dets','fix_rate_dets.fix_rate_id','=','fix_rates.id')
                    ->join(getSharedDb().'.items','items.id','=','fix_rate_dets.item_id');

        if ($searchStr = $request->input('search.value')) {
            $fix_rates = $fix_rates->where('items.item_name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'fix_rates.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='item_name'){
            $field_name = 'items.item_name';
        }

        if($field_name){
            $fix_rates = $fix_rates->orderBy($field_name, $asc_desc);
        }
        else{
            $fix_rates = $fix_rates->orderBy('fix_rates.id','DESC');
        }
        $fix_rates = $fix_rates->take($request->length);
        $filteredCount = $fix_rates->count();
        if ($request->start > 0) {
            $fix_rates->skip($request->start);
        }
        $fix_rates = $fix_rates->groupBy('fix_rates.id')->select('fix_rates.*')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $fix_rates,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store (Request $request){
        if (Gate::denies('fix-rates-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    public function update (Request $request){
        if (Gate::denies('fix-rates-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    public function edit(Request $request,$id){
        if (Gate::denies('fix-rates-modify') || Gate::denies('fix-rates-view')) {
            return deny();
        }
        $fix_rate = FixRate::whereId($id)->with(['fix_rate_details.item','fix_rate_details.packing'])->first();
        return reply(true, [
            'fix_rate' => $fix_rate,
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [
            'wef_date'=>'required|date_format:Y-m-d|unique:'.getCompDbConn().'.fix_rates,wef_date,'. $request->form_id,
            'fix_rate_details.*.item_id'=>'required|numeric|exists:items,id',
            'fix_rate_details.*.packing_id'=>'required|numeric|exists:packings,id',
            'fix_rate_details.*.rate'=>'required|numeric',
            'uid' => 'nullable|unique:' . getCompDbConn() . '.fix_rates,uid,' . $request->form_id,
        ];

        $messages = [
            'fix_rate_details.*.item_id.required'=>'Item Mandatory',
            'fix_rate_details.*.packing_id.required'=>'Packing Mandatory'
        ];
        $request['wef_date'] = getDateFormat($request['wef_date'],'ymd');
        $this->validate($request, $rules,$messages);
        $request['wef_date'] = getDateFormat($request['wef_date'],'dmy');
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $fix_rate = FixRate::findOrNew($request->form_id);
        $fix_rate->fill($request->all());
        $old_ids = $fix_rate->fix_rate_details()->pluck('id')->toArray();

        $fix_rate_details_coll = new Collection();
        foreach($request->fix_rate_details as $fix_rate_det){
            $fix_rate_detail = FixRateDetail::findOrNew($fix_rate_det['id']);
            $fix_rate_detail->fill($fix_rate_det);
            $fix_rate_details_coll->add($fix_rate_detail);
        }
        $new_ids = $fix_rate_details_coll->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::connection(getCompDbConn())->beginTransaction();
            if($request->form_id>0){
                $uid = addUid($fix_rate->uid);
            }
            $fix_rate->save();
            $fix_rate->fix_rate_details()->saveMany($fix_rate_details_coll);
            FixRateDetail::whereIn('id',$detach)->delete();
        DB::connection(getCompDbConn())->commit();


        return reply(true, [
            'fix_rate' => $fix_rate
        ]);
    }
}
