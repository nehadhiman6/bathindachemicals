<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Model;
use App\Traits;
use App\Models\Yearly\VoucherDetail;
use App\Models\Accounts\Account;

class Bill extends Model
{
    use Traits\UserAutoUpdate;
    protected $table = "bills";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'vcode',
        'trans_date',
        'det_id',
        'dr_cr',
        'ref',
        'ref_date',
        'ref_type',
        'ref_key',
        'ac_id',
        'branch_id',
        'amount',
        'part'
    ];

    public function setTransDateAttribute($date)
    {
        $this->attributes['trans_date'] = setDateAttribute($date);
    }

    public function getTransDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setRefDateAttribute($date)
    {
        $this->attributes['ref_date'] = setDateAttribute($date);
    }

    public function getRefDateAttribute($date)
    {
        return getDateAttribute($date);
    }


    public function setRefKeyAttribute()
    {
        $this->attributes['ref_key'] = getRefKey($this->ref, $this->ref_date);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'ref_key', 'ref_key');
    }

    public function opening_detail()
    {
        return $this->belongsTo(AccountOpening::class, 'det_id', 'id');
    }


    public function account()
    {
        return $this->belongsTo(Account::class, 'ac_id', 'id');
    }
}
