<?php

namespace App\Models\Masters;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class RateDifference extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;

    protected $table = 'rate_differences';
    protected $fillable = [
        'item_id',
        'party_cat_id',
        'wef_date',
        'date_applicable_on',
        'uid'
    ];

    public function  rate_diff_details(){
        return $this->hasMany(RateDifferenceDetail::class,'rate_diff_id');
    }

    public function setWefDateAttribute($date)
    {
        $this->attributes['wef_date'] = setDateAttribute($date);
    }

    public function getWefDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function party_category(){
        return $this->belongsTo(PartyCategory::class,'party_cat_id');
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
}
