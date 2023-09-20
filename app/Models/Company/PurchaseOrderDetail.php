<?php

namespace App\Models\Company;

use App\Traits;
use App\Models\Items\Item;
use App\Models\Items\ItemUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "pur_ord_dets";
    protected $fillable = [
        'pur_ord_id',
        'item_id',
        'unit_id',
        'qty_from',
        'qty_to',
        'qty_extended',
        'rate',
        'rate_extended',
    ];

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function unit(){
        return $this->belongsTo(ItemUnit::class,'unit_id');
    }

    public function setQtyExtendedAttribute($value)
    {
        $this->attributes['qty_extended'] = setAmountAttribute($value);
    }

    public function setRateExtendedAttribute($value)
    {
        $this->attributes['rate_extended'] = setAmountAttribute($value);
    }
}
