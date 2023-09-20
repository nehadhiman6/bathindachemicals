<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Models\Items\Gst;
use App\Models\Items\GstDetail;
use App\Models\Items\GstType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class GstController extends Controller
{

    public function index(Request $request){
        return Inertia::render('ProjectComponents/Items/Gst/GstList');
    }

    public function gstsList(Request $request)
    {
        if (Gate::denies('gsts')) {
            return deny();
        }
            $count = Gst::count();
            $filteredCount = $count;
            $gsts = Gst::orderBy('id', 'DESC');

            if ($searchStr = $request->input('search.value')) {
                $gsts = $gsts->where('name', 'like', "%{$searchStr}%");
            }
            $gsts = $gsts->take($request->length);
            $filteredCount = $gsts->count();

            if ($request->start > 0) {
                $gsts->skip($request->start);
            }

            $gsts = $gsts->select('gst.*')->distinct()->get();
            $gsts->load(['types.details']);
            return [
                'draw' => intval($request->draw),
                'start' => $request->start,
                'data' => $gsts,
                'recordsTotal' => $count,
                'recordsFiltered' => $filteredCount,
            ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('gsts-add')) {
            return deny();
        }
        $this->validateForm($request);
        $gst = new Gst();
        $gst->fill($request->all());
        $gst = $this->saveGstTypeAndDetails($gst, $request);
        return reply(true, [
            'gst' => $gst
        ]);

    }

    public function saveGstTypeAndDetails($gst, $request)
    {
        $gst_types = new Collection();

        $type = new GstType();
        $type->wef_date = $request->wef_date;
        $type->gst_type = "L";
        $gst_types->add($type);

        $type = new GstType();
        $type->wef_date = $request->wef_date;
        $type->gst_type = "C";
        $gst_types->add($type);

        $gst_details = new Collection();
        if ($request->cgst) {
            $detail = new GstDetail();
            $detail->name = "cgst";
            $detail->rate = $request->cgst;
            $detail->rate_on = "Amount";
            $detail->acid_input = $request->cgst_account['acid_input'];
            $detail->acid_output = $request->cgst_account['acid_output'];

            $gst_details->add($detail);
        }

        if ($request->sgst) {
            $detail = new GstDetail();
            $detail->name = "sgst";
            $detail->rate = $request->sgst;
            $detail->rate_on = "Amount";
            $detail->acid_input = $request->sgst_account['acid_input'];
            $detail->acid_output = $request->sgst_account['acid_output'];
            $gst_details->add($detail);
        }

        if ($request->igst) {
            $detail = new GstDetail();
            $detail->name = "igst";
            $detail->rate = $request->igst;
            $detail->rate_on = "Amount";
            $detail->acid_input = $request->igst_account['acid_input'];
            $detail->acid_output = $request->igst_account['acid_output'];
            $gst_details->add($detail);
        }

        $ces_sgst_details = new Collection();
        if ($request->cess) {
            $detail = new GstDetail();
            $detail->name = "cess";
            $detail->rate = $request->cess;
            $detail->rate_on = $request->rate_on;
            $detail->acid_input = $request->cess_account['acid_input'];
            $detail->acid_output = $request->cess_account['acid_output'];
            $gst_details->add($detail);

            $detail = new GstDetail();
            $detail->name = "cess";
            $detail->rate = $request->cess;
            $detail->rate_on = $request->rate_on;
            $detail->acid_input = $request->cess_account['acid_input'];
            $detail->acid_output = $request->cess_account['acid_output'];

            $ces_sgst_details->add($detail);
        }

        // return $gst_types;
        DB::beginTransaction();
        $gst->save();

        foreach ($gst_types as $gst_type) {
            $gst_type->gst_id =  $gst->id;
            $gst_type->save();

            foreach ($gst_details as $gst_detail) {
                if ($gst_type->gst_type == "L" && ($gst_detail->name == "sgst" || $gst_detail->name == "cgst")) {
                    $gst_detail->gst_type_id = $gst_type->id;
                    $gst_detail->gst_id = $gst->id;
                    $gst_detail->save();
                } elseif ($gst_type->gst_type == "C" && $gst_detail->name == "igst") {
                    $gst_detail->gst_type_id = $gst_type->id;
                    $gst_detail->gst_id = $gst->id;
                    $gst_detail->save();
                } elseif ($gst_type->gst_type == "L" && $gst_detail->name == "cess") {
                    $gst_detail->gst_type_id = $gst_type->id;
                    $gst_detail->gst_id = $gst->id;
                    $gst_detail->save();
                }
            }

            if ($gst_type->gst_type == "C") {
                foreach ($ces_sgst_details as $ces_sgst_detail) {
                    $ces_sgst_detail->gst_type_id = $gst_type->id;
                    $ces_sgst_detail->gst_id = $gst->id;
                    $ces_sgst_detail->save();
                }
            }
        }
        DB::commit();
    }


    public function show($id)
    {
        $gst = Gst::findorFail($id);
        $gst->load('types.details.account_input', 'types.details.account_output');
        // return $gst;
        return reply(true, [
            'gst' => $gst
        ]);
    }

    public function edit($id)
    {
        if (Gate::denies('gsts-modify')) {
            return deny();
        }
        $gst = Gst::findorFail($id);
        $gst->load('types.details.account_input', 'types.details.account_output');
        // return $gst;
        return response()->json([
            'success' => "Gst Loaded",
            'gst' => $gst,
        ], 200, ['app-status' => 'success']);
    }

    private function validateForm($request, $id = 0, $change = false)
    {
        $rules = [
            'name' => 'required|unique:gst,name,' .$id ,
            'cgst_account.acid_input' => 'required',
            'cgst_account.acid_output' => 'required',
            'sgst_account.acid_input' => 'required',
            'sgst_account.acid_output' => 'required',
            'igst_account.acid_input' => 'required',
            'igst_account.acid_output' => 'required',
            'wef_date' => 'required|date_format:d-m-Y',
            'cgst' => 'required|numeric',
            'sgst' => 'required|numeric',
            'igst' => 'required|numeric',
            'cess' => 'nullable|numeric'

        ];
        if($request->cess != ''){
            $rules+=[
            'cess_account.acid_input' => 'required_unless:cess,0',
            'cess_account.acid_output' => 'required_unless:cess,0'];
        }
        if ($change) {
            $old_gst = GstType::where('wef_date', Carbon::createFromFormat('d-m-Y', $request->wef_date)->format('Y-m-d'))->where('gst_id', $id)->get();
            if ($old_gst) {
                $rules += ['wef_date' => 'unique:gst_types,gst_id,null,null,wef_date,' . $request->wef_date . ',null,null,gst_type'];
            }
        }

        $this->validate($request, $rules, [
            'wef_date.date_format' => 'This is not a Valid Date Format. Valid Format is DD/MM/YYYY',
            'wef_date.required' => 'Date is required',
            'cgst.required' => 'This Field is mandatory',
            'sgst.required' => 'This Field is mandatory',
            'igst.required' => 'This Field is mandatory',
            'cgst_account.acid_input.required' => 'This Field is mandatory',
            'cgst_account.acid_output.required' => 'This Field is mandatory',
            'sgst_account.acid_input.required' => 'This Field is mandatory',
            'sgst_account.acid_output.required' => 'This Field is mandatory',
            'igst_account.acid_input.required' => 'This Field is mandatory',
            'igst_account.acid_output.required' => 'This Field is mandatory',
            'cess_account.acid_input.required_unless' => 'This Field is mandatory',
            'cess_account.acid_output.required_unless' => 'This Field is mandatory',
        ]);

        if ($request->cess > 0) {
            $this->validate($request, [
                'rate_on' => 'required|not_in:0'
            ]);
        }
    }

    public function changeGstRate(Request $request, $id)
    {
        if (Gate::denies('gst-change-rates')) {
            return deny();
        }
        $this->validateForm($request, $id, true);


        $gst = Gst::findorFail($id);
        $gst->wef_date = $request->wef_date;
        $gst->update();


        $gst = $this->saveGstTypeAndDetails($gst, $request);
        return reply(true, [
            'gst' => $gst
        ]);
    }


    public function update(Request $request, $id)
    {
        if (Gate::denies('gsts-modify')) {
            return deny();
        }
        $this->validateForm($request, $id);

        $gst = Gst::findorFail($request->form_id);
        $gst->fill($request->all());
        $gst->update();


        $gst_types = new Collection();
        $gst_details_state = new Collection();
        $gst_details_interstate = new Collection();

        $gst_type1 = GstType::firstOrNew(['gst_id' => $gst->id, 'gst_type' =>  'L']);
        // return $gst_type;
        $gst_type1->wef_date = $request->wef_date;
        $gst_types->add($gst_type1);

        $detail = GstDetail::firstOrNew(['gst_id' => $gst->id, 'gst_type_id' => $gst_type1->id, 'name' => 'cgst']);
        $detail->rate = $request->cgst;
        $detail->acid_input = $request->cgst_account['acid_input'];
        $detail->acid_output = $request->cgst_account['acid_output'];
        $gst_details_state->add($detail);

        $detail = GstDetail::firstOrNew(['gst_id' => $gst->id, 'gst_type_id' => $gst_type1->id, 'name' => 'sgst']);
        $detail->rate = $request->sgst;
        $detail->acid_input = $request->sgst_account['acid_input'];
        $detail->acid_output = $request->sgst_account['acid_output'];
        $gst_details_state->add($detail);

        $remove_cess = []; //in case of edit

        if ($request->cess) {
            $detail = GstDetail::firstOrNew(['gst_id' => $gst->id, 'gst_type_id' => $gst_type1->id, 'name' => 'cess']);
            $detail->rate = $request->cess;
            $detail->rate_on = $request->rate_on;
            $detail->acid_input = $request->cess_account['acid_input'];
            $detail->acid_output = $request->cess_account['acid_output'];
            $gst_details_state->add($detail);
        } else {
            $detail = GstDetail::where(['gst_id' => $gst->id, 'gst_type_id' => $gst_type1->id, 'name' => 'cess'])->first();

            if ($detail) {
                array_push($remove_cess, $detail->id);
            }
        }


        $gst_type = GstType::firstOrNew(['gst_id' => $gst->id, 'gst_type' => 'C']);
        $gst_type->wef_date = $request->wef_date;
        $gst_types->add($gst_type);

        if ($request->cess) {
            $detail = GstDetail::firstOrNew(['gst_id' => $gst->id, 'gst_type_id' => $gst_type->id, 'name' => 'cess']);
            $detail->rate = $request->cess;
            $detail->rate_on = $request->rate_on;
            $detail->acid_input = $request->cess_account['acid_input'];
            $detail->acid_output = $request->cess_account['acid_output'];
            $gst_details_interstate->add($detail);
        } else {
            $detail = GstDetail::where(['gst_id' => $gst->id, 'gst_type_id' => $gst_type->id, 'name' => 'cess'])->first();
            if ($detail) {
                array_push($remove_cess, $detail->id);
            }
        }

        $detail = GstDetail::firstOrNew(['gst_id' => $gst->id, 'gst_type_id' => $gst_type->id, 'name' => 'igst']);
        $detail->rate = $request->igst;
        $detail->acid_input = $request->igst_account['acid_input'];
        $detail->acid_output = $request->igst_account['acid_output'];
        $gst_details_interstate->add($detail);
        $gst->types()->saveMany($gst_types);
        foreach ($gst_types as $gtype) {
            if ($gtype->gst_type == 'L')
                $gtype->details()->saveMany($gst_details_state);
            else
                $gtype->details()->saveMany($gst_details_interstate);
        }
        GstDetail::whereIn('id', $remove_cess)->delete();
        return reply(true, [
            'gst' => $gst
        ]);
    }

    public function gstDetails(Request $request)
    {
        $gst = Gst::find($request->gst_id);
        $gst->load('gst_types.details');
        return reply(true, [
            'gst' => $gst
        ]);
    }

}
