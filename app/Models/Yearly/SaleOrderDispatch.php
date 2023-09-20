<?php

namespace App\Models\Yearly;

use App\Models\Items\Item;
use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class SaleOrderDispatch extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "sale_order_dispatches";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'sale_order_id',
        'item_id',
        'packing_id',
        'brand_id',
        'destination',
        'qty',
        'weight',
    ];

    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function sale_order(){
        return $this->belongsTo(SaleOrder::class,'sale_order_id');
    }

}
