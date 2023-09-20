<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\VatCst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class VatCstController extends Controller
{
    public function index(){
        if (Gate::denies('vat-cst')) {
            return deny();
        }
        return Inertia::render('ProjectComponents/Items/VatCst/VatCstList');
    }

    public function vatCstList(Request $request)
    {
        if (Gate::denies('vat-cst')) {
            return deny();
        }
            $count = VatCst::count();
            $filteredCount = $count;
            $vat_csts = VatCst::orderBy('id', 'DESC');

            if ($searchStr = $request->input('search.value')) {
                $vat_csts = $vat_csts->where('name', 'like', "%{$searchStr}%");
            }
            $vat_csts = $vat_csts->take($request->length);
            $filteredCount = $vat_csts->count();

            if ($request->start > 0) {
                $vat_csts->skip($request->start);
            }

            $vat_csts = $vat_csts->select('vat_cst.*')->distinct()->get();
            return [
                'draw' => intval($request->draw),
                'start' => $request->start,
                'data' => $vat_csts,
                'recordsTotal' => $count,
                'recordsFiltered' => $filteredCount,
            ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('vat-cst-add')) {
            return deny();
        }
        $this->validateForm($request);
        $vst_cst = new VatCst();
        $vst_cst->fill($request->all());
        $vst_cst->save();
        return reply(true, [
            'vst_cst' => $vst_cst
        ]);

    }


    private function validateForm($request, $id = 0, $change = false)
    {
        $rules = [
            'name' => 'required|unique:vat_cst,name,' . $id,
            'vat_rate' => 'nullable|required_without:cst_rate|numeric',
            'cst_rate' => 'nullable|required_without:vat_rate|numeric',
            'sur_on_vat' => 'nullable|numeric',
            'sur_on_cst' => 'nullable|numeric'
        ];

        if($request->vat_rate > 0){
            $rules += ['vat_ac_id'=>'required|exists:accounts,id'];
        }  if($request->cst_rate > 0){
            $rules += ['cst_ac_id'=>'required|exists:accounts,id'];
        } if($request->sur_on_vat > 0){
            $rules += ['sur_vat_ac_id'=>'required|exists:accounts,id'];
        }  if($request->sur_on_cst > 0){
            $rules += ['sur_cst_ac_id'=>'required|exists:accounts,id'];
        }

        $this->validate($request, $rules);
    }

    public function edit($id)
    {
        if (Gate::denies('vat-cst-modify')) {
            return deny();
        }
        $vat_cst = VatCst::findorFail($id);
        $vat_cst->load(
            'vat_account',
            'cst_account',
            'surcharge_vat_account',
            'surcharge_cst_account',
        );
        // return $gst;

        return reply(true,[
            'vat_cst'=>$vat_cst
        ]);
    }

    public function update(Request $request,$id)
    {
        if (Gate::denies('vat-cst-modify')) {
            return deny();
        }
        $this->validateForm($request,$id);
        $vst_cst = VatCst::findOrFail($id);
        $vst_cst->fill($request->all());
        $vst_cst->save();
        return reply(true, [
            'vst_cst' => $vst_cst
        ]);

    }

}

