<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Model;
use App\Traits;
use App\Models\Accounts\Account;
use Illuminate\Support\Facades\DB;

class Cashbook extends Model
{
    use Traits\UserAutoUpdate;
    protected $table = "cash_book";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'vcode',
        's_no',
        'trans_date',
        'dr_cr',
        'cash_cr',
        'ac_id',
        'amount',
        'part',
        'branch_id',
        'due_date',
        'weight',
        'vchr_no'
    ];

    public function setTransDateAttribute($date)
    {
        $this->attributes['trans_date'] = setDateAttribute($date);
    }

    public function getTransDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setDueDateAttribute($date)
    {
        $this->attributes['due_date'] = setDateAttribute($date);
    }

    public function getDueDateAttribute($date)
    {
        return getDateAttribute($date);
    }


    public function account()
    {
        return $this->belongsTo(Account::class, 'ac_id', 'id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'vcode', 'vcode');
    }


    public static function getOpening($acid, $date)
    {
        $dt1 = getDateFormat($date, 'ymd');
        $opening = static::where('acid', $acid)->where(function ($cond) use ($dt1) {
            $cond->where('vcode', 'OS')
                ->orWhere('trans_date', '<', $dt1);
        })->sum(DB::raw("case when dr_cr = 'D' then amount else -1*amount end"));
        return round($opening, 2);
    }
    // Type is for Returning (v)alue or (s)tring
    public static function getBalance($acid, $date,$branch_id = -1,$type = 'v',$vcode = '')
    {
        logger($vcode);
        $dt1 = getDateFormat($date, 'ymd');
        if($branch_id == -1) {
            $branch_id = getCurrentBranchId();
        }
        $bal = Cashbook::where('ac_id', $acid)->where('branch_id',$branch_id)
            ->where('trans_date', '<=', $dt1)->where('vcode','<>',$vcode)
            ->sum(DB::raw("case when dr_cr = 'D' then amount else -1*amount end"));
        if($type == 's') {
            if (abs($bal) > 0) {
                $balance =  $bal > 0 ? number_format($bal, 2, ".", '') . ' Dr' : number_format(abs($bal), 2, ".", '') . ' Cr';
            } else {
                $balance =  '0.00 Cr';
            }
        } else {
            $balance = round($bal, 2);
        }
        return $balance;
    }
}
