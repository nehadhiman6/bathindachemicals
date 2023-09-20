<?php

namespace App\Models\Company;

use App\Models\Accounts\Account;
use App\Models\Accounts\AccountDetail;
use App\Models\Accounts\PayTerm;
use App\Models\Attachment\CompanyResource;
use App\Models\Masters\Packing;
use App\Models\Masters\PackingFormula;
use App\Models\Masters\PackingFormulaDetail;
use App\Models\Masters\RateDifference;
use App\Models\Masters\RateDifferenceDetail;
use App\Models\Yearly\SaleOrderDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class SaleContract extends Model
{
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "sales_contracts";
    protected $fillable = [
        'sale_contract_id',
        'branch_id',
        'contract_date',
        'contract_no',
        'packed_loose',
        'ac_id',
        'valid_from_date',
        'valid_to_date',
        'valid_extended_upto',
        'pay_term_id',
        'broker_id',
        'brokerage_rate',
        'gst_terms',
        'bargain_no',
        'bargain_date',
        'cust_po_no',
        'cust_po_date',
        'broker_type',
        'remarks',
        'vcode',
        'delivery_terms',
        'packing_id',
        'sap_po_no',
        'sap_po_date',
        'uid',
        'status'
    ];

    public function setCustPoDateAttribute($date)
    {
        $this->attributes['cust_po_date'] = setDateAttribute($date);
    }

    public function getCustPoDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setSapPoDateAttribute($date)
    {
        $this->attributes['sap_po_date'] = setDateAttribute($date);
    }

    public function getSapPoDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setBargainDateAttribute($date)
    {
        $this->attributes['bargain_date'] = setDateAttribute($date);
    }

    public function getBargainDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setContractDateAttribute($date)
    {
        $this->attributes['contract_date'] = setDateAttribute($date);
    }

    public function getContractDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'ac_id', 'id');
    }

    public function broker()
    {
        return $this->belongsTo(Account::class, 'broker_id');
    }
    public function pay_term()
    {
        return $this->belongsTo(PayTerm::class, 'pay_term_id');
    }

    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }


    public function setValidFromDateAttribute($date)
    {
        $this->attributes['valid_from_date'] = setDateAttribute($date);
    }

    public function getValidFromDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setValidToDateAttribute($date)
    {
        $this->attributes['valid_to_date'] = setDateAttribute($date);
    }

    public function getValidToDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setValidExtendedUptoAttribute($date)
    {
        $this->attributes['valid_extended_upto'] = setDateAttribute($date);
    }

    public function getValidExtendedUptoAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function sale_contract_details(){
        return $this->hasMany(SaleContractDetail::class,'sale_contract_id');
    }

    public static function getPackRatesData($sale_contract_id,$item_id,$party_cat_id,$date) {
        $data = [];
        $t = static::find($sale_contract_id);
        $pack_id = $t ? intval($t->packing_id):0;
        $cont_date = $t ? getDateFormat($t->contract_date,'ymd'):null;
        $dt1 = getDateFormat($date,'ymd');
        $sale_cont_det = SaleContractDetail::where('sale_contract_id',$sale_contract_id)
                        ->where('item_id',$item_id)->first();

        if($sale_cont_det){
            $sale_order_items = SaleOrderDetail::where('sale_contract_id',$sale_cont_det['sale_contract_id'])
                        ->where('item_id',$item_id)->with('sale_order_packs')->get();
            $sale_cont_det['consumed'] = $sale_order_items;
        }

        $data['sale_contract_det'] = $sale_cont_det;
        $rate_diff = RateDifference::where('party_cat_id',$party_cat_id)
                    ->where('item_id',$item_id)
                    ->whereRaw("((date_applicable_on = 'C' and wef_date <= '$cont_date') or (date_applicable_on = 'D' and wef_date <= '$dt1'))")
                    ->orderBy('wef_date','desc')
                    ->first();
        if($rate_diff) {
            $rate_diff_dets = RateDifferenceDetail::where('rate_diff_id',$rate_diff->id)
                            ->select('rate_from','rate_to','rate_diff')->orderBy('rate_from')
                            ->get();
            $data['rate_diff'] = $rate_diff_dets;
        }
        $pack_formula = PackingFormula::where('party_cat_id',$party_cat_id)
                    ->where('item_id',$item_id)
                    ->where('packing_id',$pack_id)
                    ->whereRaw("((date_applicable_on = 'C' and wef_date <= '$cont_date') or (date_applicable_on = 'D' and wef_date <= '$dt1'))")
                    ->orderBy('wef_date','desc')
                    ->first();
        if($pack_formula) {
            $pack_formula_dets = PackingFormulaDetail::where('pack_formula_id',$pack_formula->id)
                            ->select('packing_formula_dets.*')->with(['brand','packing'])
                            ->get();
            $data['pack_formula'] = $pack_formula_dets;
        }

        return $data;
    }

    public static function getSaleOrderFocPacking($sale_contract_id,$item_id){
        $data=[];
        $t = static::find($sale_contract_id);
        $party_cat_id = ($t = AccountDetail::where('ac_id',$t->ac_id)->first()) ? $t->party_cat_id:0;
        $packing_id = $t->packing_id;

        $packing_formula_ids = PackingFormula::where('party_cat_id',$party_cat_id)
            ->where('item_id',$item_id)->orderBy('wef_date','DESC')->take(1)->pluck('id')->toArray();

        $packing_formula_dets = PackingFormulaDetail::whereIn('pack_formula_id',$packing_formula_ids)
        ->select(['packing_id','brand_id','weight'])
        ->get();
        return $packing_formula_dets;
    }


    public function resources(){
        return $this->hasMany(CompanyResource::class,'resourceable_id')->where('resourceable_type', SaleContract::class);
    }

}
