<?php

namespace App\Models\Yearly;

use App\Models\Items\Gst;
use App\Models\Items\Item;
use App\Models\Items\ItemUnit;
use App\Models\Items\VatCst;
use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class InvoiceDetail extends Model
{

    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "invoice_dets";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'invoice_id',
        'item_id',
        'unit_id',
        'brand_id',
        'packing_id',
        'hsn_code',
        'qty',
        'weight',
        'rate_on',
        'rate',
        'discount',
        'freight',
        'packing_cost',
        'amt_without_gst',
        'basic_amount',
        'final_rate',
        'gst_id',
        'vat_cst_id',
        'gst_vat_amount',
        'add_less',
        'surcharge_amount',
        'net_amount',
        'acid_sale'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function item_unit()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function gst(){
        return $this->belongsTo(Gst::class,'gst_id');
    }

    public function vat_cst(){
        return $this->belongsTo(VatCst::class,'vat_cst_id');
    }


}
