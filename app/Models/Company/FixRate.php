<?php

namespace App\Models\Company;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class FixRate extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "fix_rates";
    protected $fillable = [
        'wef_date',
        'uid'
    ];

    public function fix_rate_details(){
        return $this->hasMany(FixRateDetail::class,'fix_rate_id');
    }

    public function getWefDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setWefDateAttribute($date)
    {
        $this->attributes['wef_date'] = setDateAttribute($date);
    }
}
