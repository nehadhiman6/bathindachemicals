<?php

namespace App\Models\Yearly;

use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class SaleOrderDetailPack extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "sale_order_det_packs";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'packing_id',
        'qty',
        'weight',
        'discount',
        'final_rate',
        'brand_id',
        'add_less',
        'amt_without_gst',
        'add_less',
        'net_rate',
        'net_amount',
        // need to add brand id
        //final_rate
    ];

    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }


}
