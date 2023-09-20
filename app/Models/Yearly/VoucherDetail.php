<?php

namespace App\Models\Yearly;

use App\Traits;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Voucher;
use App\Models\Accounts\Account;
use App\Models\Masters\TdsSection;
use App\Models\Yearly\AccountYear;
use App\Models\Masters\VoucherType;
use App\Models\Accounts\AccountHead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherDetail extends Model
{
    use Traits\UserAutoUpdate;
    use HasFactory;
    protected $table = "voucher_dets";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'voucher_id',
        'drcr',
        'sno',
        'ac_id',
        'amount',
        'disc_tds_amt',
        'part',
        'weight',
        'section_id',
        'rate',
        'tds_on',
        'tds_adj',
        'tds_amt',
        'acid_tds',
        'tds_part',
        'beneficiary_name'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'ac_id', 'id');
    }

    public function section(){
        return $this->belongsTo(TdsSection::class,'section_id');
    }

    public function tds_account(){
        return $this->belongsTo(Account::class,'acid_tds');
    }

    public function accountYearly()
    {
        return $this->hasOne(AccountYearly::class, 'ac_id', 'ac_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'id');
    }


    public function bills()
    {
        return $this->hasMany(Bill::class, 'det_id', 'id');
    }


}
