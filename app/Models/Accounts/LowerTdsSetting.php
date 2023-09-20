<?php

namespace App\Models\Accounts;

use App\Models\Masters\TdsSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class LowerTdsSetting extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "lower_tds_setting";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'ac_id',
        'tds_sec_id',
        'certificate_no',
        'date',
        'rate',
        'acid_tds',
        'from_date',
        'to_date',
        'amount',
    ];

    public function setDateAttribute($date)
    {
        $this->attributes['date'] = setDateAttribute($date);
    }

    public function getDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setFromDateAttribute($date)
    {
        $this->attributes['from_date'] = setDateAttribute($date);
    }

    public function getFromDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setToDateAttribute($date)
    {
        $this->attributes['to_date'] = setDateAttribute($date);
    }

    public function getToDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function tds_account(){
        return $this->belongsTo(Account::class,'acid_tds');
    }

    public function tds_section(){
        return $this->belongsTo(TdsSection::class,'tds_sec_id');
    }
}
