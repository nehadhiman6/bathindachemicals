<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\SaleContract;
use App\Models\Company\SaleContractDetail;
use App\Models\Masters\BranchPrefix;
use App\Models\Masters\Packing;
use App\Models\Yearly\Parameter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SaleContractController extends Controller
{
    public function index(Request $request){
        $packing = null;
        $para= getParameterValue('sale_contract_pack_id');
        $para_value =$para ? $para['para_value'] :0;
        if($para_value > 0){
            $packing = Packing::findOrFail($para_value);
        }

        $brokerage_parameters = Parameter::whereIn('para_name',['brokerage_per_nag','brokerage_per_quintal','brokerage_per_drum',
        'brokerage_per_nag_unit',
        'brokerage_per_quintal_unit',
        'brokerage_per_drum_unit',
        ])->get()->load('account','packing','item');
        return Inertia::render('ProjectComponents/Sale/SaleContract/SaleContractList',[
            'default_packing' =>$packing,
            'brokerage_parameters'=>$brokerage_parameters
        ]);
    }

    public function saleContractList (Request $request){
        if (Gate::denies('sale-contracts')) {
            return deny();
        }
        $count = SaleContract::count();
        $filteredCount = $count;

        $sale_contracts = SaleContract::leftJoin(getSharedDb().'.accounts','accounts.id','=','sales_contracts.ac_id');

        if ($searchStr = $request->input('search.value')) {
            $sale_contracts = $sale_contracts->where('accounts.name', 'like', "%{$searchStr}%");
        }

        $sale_contracts = $sale_contracts->where('sales_contracts.branch_id',getCurrentBranchId());
        $asc_desc = $request->order[0]['dir'];
        $field_name = 'sales_contracts.'. $request->columns[$request->order[0]['column']]['data'];

        if($request->columns[$request->order[0]['column']]['data'] =='client_name'){
            $field_name = 'accounts.name';
        }

        if($field_name){
            $sale_contracts = $sale_contracts->orderBy($field_name, $asc_desc);
        }
        else{
            $sale_contracts = $sale_contracts->orderBy('sales_contracts.id','DESC');
        }
        $sale_contracts = $sale_contracts->take($request->length);
        $filteredCount = $sale_contracts->count();
        if ($request->start > 0) {
            $sale_contracts->skip($request->start);
        }
        $sale_contracts = $sale_contracts->groupBy('sales_contracts.id')->withCount('resources')->select('sales_contracts.*','accounts.name as client_name')->get();
        return [
            'draw' => intval($request->draw),
            'start'=>$request->start,
            'data' => $sale_contracts,
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
        ];
    }

    public function store (Request $request){
        if (Gate::denies('sale-contracts-add')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    public function update (Request $request){
        if (Gate::denies('sale-contracts-modify')) {
            return deny();
        }
        return $this->saveForm($request);
    }

    public function edit(Request $request,$id){
        if (Gate::denies('sale-contracts-modify') || Gate::denies('sale-contracts-view')) {
            return deny();
        }
        $sale_contract = SaleContract::whereId($id)->with(['account','pay_term','broker','sale_contract_details.item_unit','sale_contract_details.item','packing'])->first();
        return reply(true, [
            'sale_contract' => $sale_contract,
        ]);
    }

    private function validateForm(Request $request, $id)
    {
        $current_branch_id = getCurrentBranchId();
        validateBranch();
        $branch_prefix = validatePrefix('sale_contract');

        $rules = [];
        $rules += [
            'contract_date'=>'required|date_format:d-m-Y',
            'ac_id'=>'required',
            'valid_from_date'=>'required|date_format:d-m-Y',
            'valid_to_date'=>'required|date_format:d-m-Y',
            'valid_extended_upto'=>'nullable|date_format:d-m-Y',
            'pay_term_id'=>'required|exists:pay_terms,id',
            // 'broker_id'=>'required|exists:accounts,id',
            // 'brokerage_rate'=>'required|numeric',
            'gst_terms'=>'required|in:I,E',
            'bargain_no'=>'nullable|numeric',
            'bargain_date'=>'nullable|date_format:d-m-Y',
            'cust_po_no'=>'nullable|max:15',
            'cust_po_date'=>'nullable|date_format:d-m-Y',
            'broker_type'=>'nullable|in:prec,per_unit',
            'remarks'=>'nullable|max:200',
            'delivery_terms'=>'required|in:F,M,K', //FOR, EX-MILL, EX-KANDLA
            // 'packing_id'=>'required',
            'sap_po_no'=>'nullable|max:25',
            'sap_po_date'=>'nullable|date_format:d-m-Y',
            'sale_contract_details'=>'Array|min:1',
            'packed_loose' => 'required|in:packed,loose',
            'sale_contract_details.*.item_id'=>'required|exists:items,id',
            'sale_contract_details.*.qty'=>'required|numeric',
            'sale_contract_details.*.unit_id'=>'required|exists:item_units,id',
            'sale_contract_details.*.rate'=>'required|numeric',
            // 'sale_contract_details.*.bargain_price_diff'=>'required',
            // 'sale_contract_details.*.packed_loose'=>'required|in:packed,loose',
            'sale_contract_details.*.tolerance_per'=>'nullable|numeric|max:5',
            'uid' => 'nullable|unique:' . getCompDbConn() . '.sales_contracts,uid,' . $request->form_id,
            // 'name' => 'required|string|max:100|unique:banks,name,' . $request->form_id,
        ];
        if($request->close_qty){
            $rules += ['valid_extended_upto'=>'nullable|date_format:d-m-Y'];
            foreach($request->sale_contract_details as $sale_con_det){
                $rules += [ 'sale_contract_details.*.close_qty'=>'required|numeric|max:'.$sale_con_det['qty']];
            }
        }

        // $atleast_one_with_packed = false;
        // foreach($request->sale_contract_details as $sale_contract_detail){
        //     if($sale_contract_detail['packed_loose'] == 'packed'){
        //         $atleast_one_with_packed = true;
        //     }
        // }

        if($request->packed_loose == 'packed'){
            $rules += ['packing_id' =>'required|exists:packings,id'];
        }

        $messages = [
            'sale_contract_details.*.item_id.required'=>'Item Mandatory!',
            'sale_contract_details.*.qty.required'=>'Qty Mandatory',
            'sale_contract_details.*.unit_id.required'=>'Unit Mandatory',
            'sale_contract_details.*.rate.required'=>'Rate Mandatory',
            'sale_contract_details.*.bargain_price_diff.required'=>'Bargain Price Diff Mandatory',
            'sale_contract_details.*.packed_loose.in'=>'Invalid',
            'sale_contract_details.*.packed_loose.required'=>'Mandatory',
            'sale_contract_details.*.tolerance_per.max'=>'can not exceed Maximum limit i.e 5',
            'sale_contract_details.*.tolerance_per'=>'Mandatory',
            'sale_contract_details.*.close_qty.required'=>'Mandatory',
            'sale_contract_details.*.close_qty.max'=>'Should not Exceed Qty',

        ];

        $this->validate($request, $rules,$messages);
    }

    private function saveForm(Request $request, $id = 0)
    {
        $this->validateForm($request, $id);
        $sale_contract = SaleContract::findOrNew($request->form_id);
        $sale_contract->fill($request->all());
        $old_ids = $sale_contract->sale_contract_details()->pluck('id')->toArray();

        $sale_contract_details = new Collection();
        foreach($request->sale_contract_details as $sale_contract_det){
            $sale_con_det = SaleContractDetail::findOrNew($sale_contract_det['id']);
            $sale_con_det->fill($sale_contract_det);
            $sale_contract_details->add($sale_con_det);
        }
        $new_ids = $sale_contract_details->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::connection(getCompDbConn())->beginTransaction();
            $uid = addUid($sale_contract->uid);
            $sale_contract->save();
            $sale_contract->branch_id= getCurrentBranchId();
            if($request->form_id ==0){
                $sale_contract->vcode = 'SC'.$sale_contract['id'];
                $branch_prefix = getBranchPrefix('sale_contract');
                $generated_no = getNextSeriesNumberCompany('SC-'.$branch_prefix);
                $sale_contract->contract_no = getBranchPrefix('sale_contract') . $generated_no;
            }
            $sale_contract->save();
            $sale_contract->sale_contract_details()->saveMany($sale_contract_details);
            SaleContractDetail::whereIn('id',$detach)->delete();
        DB::connection(getCompDbConn())->commit();


        return reply(true, [
            'sale_contract' => $sale_contract
        ]);
    }
}
