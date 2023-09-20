<?php

namespace App\Models\Company;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ItemFreight extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "item_freights";
    protected $fillable = [
        'item_id',
        'wef_date',
        'uid'
    ];

    public function item_freight_details(){
        return $this->hasMany(ItemFreightDetail::class,'item_freight_id');
    }

    public function getWefDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setWefDateAttribute($date)
    {
        $this->attributes['wef_date'] = setDateAttribute($date);
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

}
