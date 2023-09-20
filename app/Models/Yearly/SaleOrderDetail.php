<?php

namespace App\Models\Yearly;

use App\Models\Company\SaleContract;
use App\Models\Items\Gst;
use App\Models\Items\Item;
use App\Models\Items\VatCst;
use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class SaleOrderDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "sale_order_dets";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'sale_order_id',
        'item_id',
        'unit_id',
        'packing_id',
        'sale_contract_id',
        'qty',
        'weight',
        'rate_on',
        'rate',
        'discount',
        'freight',
        'packing_cost',
        'basic_amount',
        'gst_vat_id',
        'gst_vat_rate',
        'gst_vat_amount',
        'amt_without_gst',
        'net_amount',
        'foc_item_id',
        'foc_packing_id',
        'foc_brand_id',
        'foc_qty',
        'foc_weight',
        'add_less',
        'final_rate' // before GST

        //need to add final rate
    ];

    public function sale_order_packs(){
        return $this->hasMany(SaleOrderDetailPack::class,'sale_order_det_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }

    public function sale_contract()
    {
        return $this->belongsTo(SaleContract::class, 'sale_contract_id');
    }

    public function gst()
    {
        return $this->belongsTo(Gst::class, 'gst_vat_id');
    }

    public function vat_cst()
    {
        return $this->belongsTo(VatCst::class, 'gst_vat_id');
    }
    public function foc_item()
    {
        return $this->belongsTo(Item::class, 'foc_item_id');
    }
    public function foc_packing()
    {
        return $this->belongsTo(Packing::class, 'foc_packing_id');
    }
    public function foc_brand()
    {
        return $this->belongsTo(Brand::class, 'foc_brand_id');
    }
}
