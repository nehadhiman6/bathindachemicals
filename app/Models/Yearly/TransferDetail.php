<?php

namespace App\Models\Yearly;

use App\Traits;
use App\Models\Items\Item;
use App\Models\Items\ItemUnit;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "transfer_dets";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'transfer_id',
        'item_id',
        'unit_id',
        'packing_id',
        'qty',
        'weight',
        'rate',
        'rate_on',
        'rec_qty',
        'rec_weight',
        'amount',
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

    public function setQtyAttribute($value)
    {
        $this->attributes['qty'] = setAmountAttribute($value);
    }

    public function setWeightAmtAttribute($value)
    {
        $this->attributes['weight'] = setAmountAttribute($value);
    }

    public function setRateAmtAttribute($value)
    {
        $this->attributes['rate'] = setAmountAttribute($value);
    }

    public function setRecQtyAmtAttribute($value)
    {
        $this->attributes['rec_qty'] = setAmountAttribute($value);
    }

    public function setRecWeightAttribute($value)
    {
        $this->attributes['rec_weight'] = setAmountAttribute($value);
    }
}
