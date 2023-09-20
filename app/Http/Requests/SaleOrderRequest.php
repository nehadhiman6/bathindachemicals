<?php

namespace App\Http\Requests;

use App\Models\Accounts\AccountDetail;
use App\Models\Company\SaleContract;
use App\Models\Items\Item;
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDetail;
use App\Models\Yearly\SaleOrderDetailPack;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected $item_type = '';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $current_branch_id = getCurrentBranchId();
        validateBranch();
        $branch_prefix = validatePrefix('sale_order',);
        $rules = [];
        $rules += [
            'sale_order_date'=>'required|date_format:d-m-Y|before_or_equal:' . getToday('d-m-Y'),
            'client_id'=>'required|exists:accounts,id',
            'bill_party_id' => 'required|exists:accounts,id',
            'ship_party_id' => 'required|exists:accounts,id',
            'packed_loose' => 'required|in:packed,loose',
            'transport_type'=>'required',
            // 'dispatch_advice' => 'required|in:Y,N',
            'vehical_no'=>'nullable|max:15',
            'own_vehicle'=>'nullable|in:Y,N',
            'freight'=>'nullable|numeric|required_if:own_vehicle,N',
            'sale_order_details'=>'Array|min:1',
            'sale_order_details.*.item_id'=>'required|exists:items,id',
            'sale_order_details.*.sale_contract_id'=>'required|exists:'.getCompDbConn().'.sales_contracts,id',
            'sale_order_details.*.qty'=>'required_if:packed_loose,loose',
            'sale_order_details.*.sale_order_packs.*.qty'=>'nullable|numeric',
            'uid' => 'nullable|unique:' . getYearlyDbConn() . '.sale_orders,uid,' . $this->form_id,
        ];

        if($this->packed_loose =='packed'){
            $rules += [ 'sale_order_details.*.sale_order_packs'=>'Array|min:1'];
        }

       return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'sale_order_details.*.item_id.required'=>'Item Mandatory!',
            'sale_order_details.*.qty.required'=>'Qty Mandatory',
            'sale_order_details.*.sale_order_packs.*.qty.required_if'=>'Sale order Packing details required.'
        ];
        return $messages;
    }

    public function save(){


        $current_branch_id = getCurrentBranchId();
        validateBranch();
        $branch_prefix = validatePrefix('sale_order');
        $sale_order = SaleOrder::findOrNew($this->form_id);
        $sale_order->fill($this->all());
        $old_ids = $sale_order->sale_order_details()->pluck('id')->toArray();

        $sale_order_details = new Collection();

        // $total_add_less =$this->discount_amt + $this->freight_amt + $this->export_fee;
        // $net_amount = $this->basic_amount;
        foreach($this->sale_order_details as $sale_order_det){

            $item = Item::findOrFail($sale_order_det['item_id']);
            if($this->item_type == ''){
                $this->item_type = $item['gst_vat'];
            }
            else if($this->item_type != $item['gst_vat']){
                throw ValidationException::withMessages(['gen_msg' =>'Please select either GST items or VAT. You cannot mix them in a sales order']);
            }

            $so_det = SaleOrderDetail::findOrNew($sale_order_det['id']);
            $old_pack_ids = $so_det->sale_order_packs()->pluck('id')->toArray();
            $so_det->fill($sale_order_det);

            $this->getPendingQuantityWeight($this->bill_party_id,$sale_order_det['sale_contract_id'],$sale_order_det['item_id'],$this->sale_order_date, $sale_order_det);
            $this->checkValidity($sale_order_det['sale_contract_id']);
            // dd("here");
            //RATIO
            // $ratio = $sale_order_det['basic_amount']/$net_amount;
            // $add_less  = $ratio  * $total_add_less;
            // $so_det['add_less'] =  $add_less;
            $sale_order_pack_details = new Collection();
            foreach($sale_order_det['sale_order_packs'] as $sale_order_pack){
                if($sale_order_pack['qty'] >0){
                    $so_pack = SaleOrderDetailPack::findOrNew($sale_order_pack['id']);
                    $so_pack->fill($sale_order_pack);
                    $sale_order_pack_details->add($so_pack);
                }
            }

            $new_pack_ids = $sale_order_pack_details->pluck('id')->toArray();
            $detach_packs = array_diff($old_pack_ids,$new_pack_ids);

            $so_det['pack_coll'] = $sale_order_pack_details;
            $so_det['pack_detach'] = $detach_packs;
            $sale_order_details->add($so_det);
        }

        $new_ids = $sale_order_details->pluck('id')->toArray();
        $detach = array_diff($old_ids,$new_ids);


        DB::connection(getYearlyDbConn())->beginTransaction();
            if($this->form_id > 0){
                $uid = addUid($sale_order->uid);
            }
            $sale_order->save();
            if($this->form_id ==0){
                $sale_order->vcode = 'SO'. $sale_order->id;
                $generated_no = getYearlyNextEntryNumberFront('SO-'.$branch_prefix);
                $sale_order->sale_order_no = getBranchPrefix('sale_order') . $generated_no;
            }
            $sale_order->branch_id= $current_branch_id;

            $sale_order->save();
            foreach($sale_order_details as $sale_order_det){
                if(isset($sale_order_det['pack_coll'])){
                    $pack_coll = $sale_order_det['pack_coll'];
                    unset($sale_order_det['pack_coll']);
                }
                if(isset($sale_order_det['pack_detach'])){
                    $pack_detach = $sale_order_det['pack_detach'];
                    unset($sale_order_det['pack_detach']);
                }
                $sale_order_det->sale_order_id = $sale_order['id'];
                $sale_order_det->save();
                if($pack_coll){
                    $sale_order_det->sale_order_packs()->saveMany($pack_coll);
                }
                if($pack_detach){
                    SaleOrderDetailPack::whereIn('id',$pack_detach)->delete();
                }
            }
            SaleOrderDetail::whereIn('id',$detach)->delete();
            SaleOrderDetailPack::whereIn('sale_order_det_id',$detach)->delete();

            $sale_order_details = SaleOrderDetail::where('sale_order_id', $sale_order['id'])->get();
            foreach($sale_order_details as $sale_order_det){
                $this->updateSaleContractStatus($sale_order_det,$this->packed_loose);
            }
        DB::connection(getYearlyDbConn())->commit();

        return reply(true,[
            'sale_order' => $sale_order
        ]);
    }

    public function updateSaleContractStatus($sale_detail,$packed_loose){
        $sale_contract = SaleContract::where('id',$sale_detail->sale_contract_id)->first();
        $total_qty = 0;

        foreach($sale_contract->sale_contract_details as $contract_det){
            if($contract_det->qty){
                $total_qty += $contract_det->qty;
            }
        }
        $consumed = 0;
        $sale_order_details_sum = SaleOrderDetail::where('sale_contract_id',$sale_contract['id'])->sum('qty');
        $sale_order_det_ids = SaleOrderDetail::where('sale_contract_id',$sale_contract['id'])->pluck('id')->toArray();

        $sale_order_pack_sum = SaleOrderDetailPack::whereIn('sale_order_det_id',$sale_order_det_ids)->sum('qty');

        $pending_qty = $total_qty - $sale_order_details_sum - $sale_order_pack_sum;
        if($pending_qty <=0){
            $sale_contract->status = 'C';
            $sale_contract->save();
        }
    }


    public function getPendingQuantityWeight($bill_party_id,$sale_contract_id,$item_id,$sale_order_date, $sale_order_det){
        $term = $sale_order_det['rate_on'] =='W' ? 'weight':'qty';
        $party_cat_id = ($t = AccountDetail::where('ac_id',$bill_party_id)->first()) ? $t->party_cat_id:0;
        $data =  SaleContract::getPackRatesData($sale_contract_id,$item_id,$party_cat_id,$sale_order_date);
        $qty = 0;
        $consumed_qty = 0;
        $pending_qty = 0;
        if($data['sale_contract_det']){
            $qty =$data['sale_contract_det'][$term];
            foreach($data['sale_contract_det']['consumed'] as $consumed){
                $consumed_qty += $consumed[$term] ? $consumed[$term] :0 ;
                foreach($consumed['sale_order_packs'] as $sale_order_pack){
                    $consumed_qty += $sale_order_pack[$term]? $sale_order_pack[$term]:0;
                }
            }
            $pending_qty = $qty - $consumed_qty;
        }

        $current = $sale_order_det[$term] ?$sale_order_det[$term]:0;
        foreach($sale_order_det['sale_order_packs'] as $sale_order_pack){
            $current += $sale_order_pack[$term];
        }

        if($current > $pending_qty){
            $sale_contract = SaleContract::findOrFail($sale_contract_id);
            throw ValidationException::withMessages(['gen_msg' =>'Pending ' .$term .'  cannot be surpassed. Please adjust the ' .$term .' accordingly for the sale Contarct No.' . $sale_contract['contract_no']]);
        }
        //SALE ORDER QTY
        // dd($pending_qty);
        return $pending_qty;
    }
    public function checkValidity($sale_contract_id){
        $sale_contract = SaleContract::findOrFail($sale_contract_id);
        $valid = isBetween($this->sale_order_date,'d-m-Y',$sale_contract['valid_from_date'],$sale_contract['valid_to_date'],'d-m-Y');
        if(!$valid){
            throw ValidationException::withMessages(['gen_msg' =>'The sale contract perioed is expired (from '.$sale_contract['valid_from_date'].' to '.$sale_contract['valid_to_date'].' ). Sale Contract No.' . $sale_contract['contract_no']]);
        }
        //SALE ORDER QTY
        // dd($pending_qty);
    }
}
