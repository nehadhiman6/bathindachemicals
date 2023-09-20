<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Masters\PackingFormula;
use App\Models\Masters\PackingFormulaDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PackingFormulaController extends Controller
{
    public function index(){
        if (Gate::denies('packing-formulas')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Masters/PackingFormula/PackingFormulaList');
    }

    public function packingFormulaList(Request $request){
        if (Gate::denies('packing-formulas')) {
            return deny();
        }
        $count = PackingFormula::count();
        $filteredCount = $count;

        $rate_differences = PackingFormula::leftJoin('party_cats','party_cats.id','=', 'packing_formulas.party_cat_id')
            ->leftJoin('items','items.id','=', 'packing_formulas.item_id')
            ->leftJoin('packings','packings.id','=', 'packing_formulas.packing_id');

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
        $rate_differences = $rate_differences->groupBy('packing_formulas.id')->select(['packing_formulas.*','party_cats.name as party_category','items.item_name','packings.name as packing_name'])->get();
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
        if (Gate::denies('packing-formulas-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }


    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $packing_formula = PackingFormula::findOrNew($request->form_id);
        $old_ids = $packing_formula->packing_formula_details()->pluck('id')->toArray();
        $packing_formula->fill($request->all());

        $packing_formula_details = new Collection();
        foreach($request->packing_formula_details as $pack_for_det){
            $rate_diff_d = PackingFormulaDetail::findOrNew($pack_for_det['id']);
            $rate_diff_d->fill($pack_for_det);
            $packing_formula_details->add($rate_diff_d);
        }

        $new_ids = $packing_formula_details->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);

        DB::beginTransaction();
            $uid = addUid($packing_formula->uid);
            $packing_formula->save();
            $packing_formula->packing_formula_details()->saveMany($packing_formula_details);
            PackingFormulaDetail::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true, [
            'packing_formula' => $packing_formula
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $rules = [];
        $rules += [

            'item_id' => 'required|unique:packing_formulas,item_id,' . $request->form_id.',id,party_cat_id,'.$request->party_cat_id.',wef_date,'.getDateFormat($request->wef_date,'ymd').',packing_id,'.$request->packing_id,
            'uid' => 'nullable|unique:packing_formulas,uid,' . $request->form_id,
            'date_applicable_on'=>'required|in:C,D',
            'packing_formula_details'=>'Array|min:1',
            'packing_formula_details.*.brand_id'=>'required|exists:brands,id',
            'packing_formula_details.*.packing_id'=>'required|exists:packings,id',
            'packing_formula_details.*.divisor'=>'required|gt:0',
        ];
        $this->validate($request, $rules);
    }


    public function edit($id)
    {
        if (Gate::denies('packing-formulas-modify') || Gate::denies('packing-formulas-view')) {
            return deny();
        }
        $packing_formula = PackingFormula::whereId($id)->first()->load(['item','party_category','packing_formula_details.brand','packing_formula_details.packing','packing']);
        return reply(true, [
            'packing_formula' => $packing_formula,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('packing-formulas-modify')) {
            return deny();
        }
        return $this->saveForm($request, $id);
    }
}
