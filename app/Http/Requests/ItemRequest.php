<?php

namespace App\Http\Requests;

use App\Models\Items\Item;
use App\Models\Items\ItemBranch;
use App\Models\Items\ItemLocation;
use App\Models\Items\ItemUnitConversion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'item_name' =>'required|max:191|unique:items,item_name,' . $this->form_id,
            // 'item_code' =>'nullable',
            'main_group_id' =>'required|numeric',
            'sub_group_id' =>'nullable|numeric',
            'other_sub_group_id' =>'nullable|numeric',
            'item_unit_id' =>'required|numeric',
            'store_item' =>'in:Y,N',
            'minimum_level' =>'nullable|numeric',
            'maximum_level' =>'nullable|numeric',
            'reorder_level' =>'nullable|numeric',
            'item_type' =>'required|nullable',
            'item_type2' =>'required',
            'tsf_pur_ledger_acid' =>'nullable|numeric',
            'tsf_sale_ledger_acid' =>'nullable|numeric',
            'quality_check' =>'required',
            'rebate_acid' =>'required_if:quality_check,Y|numeric|nullable',
            'hsn_code' =>'required',
            'gst_vat' =>'required|in:G,V,E',
            'gst_id' =>'required_if:gst_vat,G',
            'vat_cst_id' =>'required_if:gst_vat,V',
            'vat_rate' =>'nullable',
            'tcs_applicable' =>'required|in:Y,N',
            'tcs_acid'=>'required_if:tcs_applicable,Y',
            'ethanol_partameters' =>'nullable|in:Y,N',
            'remarks' =>'nullable|max:1000',
            'ethenol_parameters_remarks' =>'nullable|max:1000',
            'active' =>'in:Y,N',
        ];

        if(convertToZero($this->sale_ledger_acid) == 0 && convertToZero($this->pur_ledger_acid) == 0){
            $rules +=[
                'sale_ledger_acid' =>'required|numeric',
                'pur_ledger_acid'=>'required|numeric'
            ];
        }

        return $rules;

    }

    public function messages()
    {
        return[
            'sale_ledger_acid.required' => 'Atleast one of purchase or sale account is required',
            'sale_ledger_acid.numeric' => 'Atleast one of purchase or sale account is required',
            'pur_ledger_acid.required' => 'Atleast one of purchase or sale account is required',
            'pur_ledger_acid.numeric' => 'Atleast one of purchase or sale account is required',
            'rebate_acid.required_if' => 'Reabte Account Shoulb be there if Quality Check is Yes',
        ];
    }

    public function save()
    {
        $item = Item::findOrNew($this->form_id);
        $item->fill($this->all());

        $old_item_branch_ids = $item->item_branches()->pluck('id')->toArray();
        $old_item_store_ids = $item->item_stores()->pluck('id')->toArray();
        $old_item_secondary_unit_ids = $item->secondary_unit()->pluck('id')->toArray();

        //Item Branches
        $item_branches = new Collection();

        foreach($this->branches as $branch){
            $it_branch = new ItemBranch();
            $it_branch->branch_id = $branch;
            $item_branches->add($it_branch);
        }



        $new_item_branch_ids = $item_branches->pluck('id')->toArray();
        $detach_branch = array_diff($old_item_branch_ids,$new_item_branch_ids);

        //Item Locations
        $item_stores = new Collection();

        foreach($this->stores as $store){
            $it_store = new ItemLocation();
            $it_store->store_id = $store;
            $item_stores->add($it_store);
        }

        $new_item_store_ids = $item_stores->pluck('id')->toArray();
        $detach_store = array_diff($old_item_store_ids,$new_item_store_ids);


        //secondary_unit
        $secondary_units = new Collection();

        foreach($this->secondary_unit as $s_unit){
            if($s_unit['item_unit_id'] > 0){
                $it_unit = ItemUnitConversion::firstOrNew(['item_unit_id'=>$s_unit['item_unit_id'],'item_id'=>$this->form_id]);
                $it_unit->fill($s_unit);
                $secondary_units->add($it_unit);
            }
        }

        $new_item_secondary_unit_ids = $secondary_units->pluck('id')->toArray();
        $detach_secondary_unit = array_diff($old_item_secondary_unit_ids,$new_item_secondary_unit_ids);



        DB::beginTransaction();
            $item->save();
            if($this->form_id == 0){
                $item->item_code = 'ITEM'.$item['id'];
                $item->save();
            }
            $item->item_branches()->saveMany($item_branches);
            $item->item_stores()->saveMany($item_stores);
            $item->secondary_unit()->saveMany($secondary_units);

            ItemBranch::whereIn('id',$detach_branch)->delete();
            ItemLocation::whereIn('id',$detach_store)->delete();
            ItemUnitConversion::whereIn('id',$detach_secondary_unit)->delete();
        DB::commit();

        return reply(true,[
            'item' =>$item
        ]);
    }
}
