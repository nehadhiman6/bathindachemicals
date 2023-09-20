<?php

namespace App\Models\Items;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Item extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "items";
    protected $connection = 'mysql';
    protected $fillable = [
        'item_name',
        'item_code',
        'main_group_id',
        'sub_group_id',
        'other_sub_group_id',
        'item_unit_id',
        'store_item',
        'minimum_level',
        'maximum_level',
        'reorder_level',
        'item_type',
        'item_type2',
        'pur_ledger_acid',
        'sale_ledger_acid',
        'tsf_pur_ledger_acid',
        'tsf_sale_ledger_acid',
        'rebate_acid',
        'quality_check',
        'hsn_code',
        'gst_vat',
        'gst_id',
        'vat_cst_id',
        'vat_rate',
        'tcs_applicable',
        'tcs_acid',
        'brokerage_per_unit',
        'ethanol_parameters',
        'remarks',
        'ethenol_parameters_remarks',
        'active',
        'is_liqour'
    ];

    public function item_branches(){
        return $this->hasMany(ItemBranch::class,'item_id');
    }
    public function item_stores(){
        return $this->hasMany(ItemLocation::class,'item_id');
    }
    public function secondary_unit(){
        return $this->hasMany(ItemUnitConversion::class,'item_id');
    }

    public function main_group(){
        return $this->belongsTo(ItemGroup::class,'main_group_id');
    }
    public function sub_group(){
        return $this->belongsTo(ItemGroup::class,'sub_group_id');
    }
    public function other_sub_group(){
        return $this->belongsTo(ItemGroup::class,'other_sub_group_id');
    }
    public function item_unit(){
        return $this->belongsTo(ItemUnit::class,'item_unit_id');
    }
    public function pur_ledger_account(){
        return $this->belongsTo(Account::class,'pur_ledger_acid');
    }
    public function sale_ledger_account(){
        return $this->belongsTo(Account::class,'sale_ledger_acid');
    }
    public function tsf_pur_ledger_account(){
        return $this->belongsTo(Account::class,'tsf_pur_ledger_acid');
    }
    public function tsf_sale_ledger_account(){
        return $this->belongsTo(Account::class,'tsf_sale_ledger_acid');
    }
    public function rebate_account(){
        return $this->belongsTo(Account::class,'rebate_acid');
    }
    public function tcs_account(){
        return $this->belongsTo(Account::class,'tcs_acid');
    }
    public function gst(){
        return $this->belongsTo(Gst::class,'gst_id');
    }

    public function vat_cst(){
        return $this->belongsTo(VatCst::class,'vat_cst_id');
    }
}
