<?php

namespace App\Models\Company;

use App\Models\Masters\City;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ItemFreightDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "item_freight_dets";
    protected $fillable = [
        'item_freight_id',
        'city_id',
        'freight',
    ];

    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }
}
