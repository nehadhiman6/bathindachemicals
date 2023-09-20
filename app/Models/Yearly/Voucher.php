<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Yearly\VoucherDetail;
use App\Models\Accounts\Account;
use App\Models\Auth\User;
use App\Models\Yearly\Cashbook;
use App\Models\Yearly\Bill;
use App\Traits;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Voucher extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "vouchers";
    protected $connection = 'yearly_db';
    // protected static $other_id = 'voucher_id';
    protected $fillable = [
        'branch_id',
        'vcode',
        'voucher_no',
        'voucher_no_part',
        'voucher_date',
        'voucher_type',
        'tr_type',
        'acid_other',
        'acid_tds',
        'approved',
        'approved_by',
        'approved_at',
        'uid'
    ];

    public function setTrTypeAttribute($value)
    {
        $this->attributes['tr_type'] = setAmountAttribute($value);
    }

    public function setVoucherDateAttribute($date)
    {
        $this->attributes['voucher_date'] = setDateAttribute($date);
    }

    public function getVoucherDateAttribute($date)
    {
        return getDateAttribute($date);
    }


    public function voucher_details()
    {
        return $this->hasMany(VoucherDetail::class, 'voucher_id', 'id');
    }


    public function account_tds()
    {
        return $this->belongsTo(Account::class, 'acid_tds', 'id');
    }

    public function account_other()
    {
        return $this->belongsTo(Account::class, 'acid_other', 'id');
    }

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class, 'vcode', 'vcode');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'vcode', 'vcode');
    }
    public function user_created(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function setPrefix($date) {
        if(in_array($this->voucher_type,['P','R'])) {
            $this->prefix = $this->tr_type.$this->voucher_type;
        } else {
            $this->prefix = 'JV';
        }
        $this->prefix .= $this->branch_id.substr($date,0,10);
    }

}
