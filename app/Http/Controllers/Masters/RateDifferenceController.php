<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\RateDifference;
use App\Models\Masters\RateDifferenceDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RateDifferenceController extends Controller
{
    public function index(){
        if (Gate::denies('rate-differences')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/RateDifference/RateDifferenceList');
    }

    public function rateDifferenceList(Request $request){
        if (Gate::denies('rate-differences')) {
            return deny();
        }
        $count = RateDifference::count();
        $filteredCount = $count;

        $rate_differences = RateDifference::leftJoin('party_cats','party_cats.id','=', 'rate_differences.party_cat_id')
        ->leftJoin('items','items.id','=', 'rate_differences.item_id');

        if ($searchStr = $request->input('search.value')) {
            $rate_differences = $rate_differences->where('name', 'like', "%{$searchStr}%");
        }
        $asc_desc = $request->order[0]['dir'];
        $field_name = $request->columns[$request->order[0]['column']]['data'];
        if($field_name){
            $rate_differences = $rate_differences->orderBy($field_name, $asc_desc);
        }
        else{
            $rate_differences = $rate_differences->orderBy('id','DESC');

        }

        $rate_differences = $rate_differences->take($request->length);
        $filteredCount = $rate_differences->count();
        if ($request->start > 0) {
            $rate_differences->skip($request->start);
        }
        $rate_differences = $rate_differences->groupBy('rate_differences.id')->select(['rate_differences.*','party_cats.name as party_category','items.item_name'])->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $rate_differences,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('rate-differences-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $rate_difference = RateDifference::findOrNew($request->form_id);
        $old_ids = $rate_difference->rate_diff_details()->pluck('id')->toArray();
        $rate_difference->fill($request->all());

        $rate_diff_details = new Collection();
        foreach($request->rate_diff_details as $rate_diff_det){
            $rate_diff_d = RateDifferenceDetail::findOrNew($rate_diff_det['id']);
            $rate_diff_d->fill($rate_diff_det);
            $rate_diff_details->add($rate_diff_d);
        }

        $new_ids = $rate_diff_details->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);

        DB::beginTransaction();
            if($request->form_id > 0){
                $uid = addUid($rate_difference->uid);
            }
            $rate_difference->save();
            $rate_difference->rate_diff_details()->saveMany($rate_diff_details);
            RateDifferenceDetail::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true, [
            'rate_difference' => $rate_difference
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [

            'item_id' => 'required|unique:rate_differences,item_id,' . $request->form_id.',id,party_cat_id,'.$request->party_cat_id.',wef_date,'.getDateFormat($request->wef_date,'ymd'),
            'uid' => 'nullable|unique:rate_differences,uid,' . $request->form_id,
            'rate_diff_details'=>'Array|min:1',
            'rate_diff_details.*.rate_from'=>'numeric|required',
            'rate_diff_details.*.rate_to'=>'numeric|required',
            'rate_diff_details.*.rate_diff'=>'numeric|required',
        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('rate-differences-modify') || Gate::denies('rate-differences-view')) {
            return deny();
        }
        $rate_difference = RateDifference::whereId($id)->first()->load(['item','party_category','rate_diff_details']);
        return reply(true, [
            'rate_difference' => $rate_difference,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('rate-differences-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
