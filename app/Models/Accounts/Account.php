<?php

namespace App\Models\Accounts;

use App\Models\Attachment\SharedResource;
use App\Models\Masters\TypeMaster;
use App\Models\Yearly\AccountOpening;
use App\Models\Yearly\AccountYearly;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Cashbook;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "accounts";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'ac_code',
        'ac_sub_group_id',
        'sub_group_id1',
        'sub_group_id2',
        'bill_wise',
        'active',
        'remarks',
    ];

    public function account_yearly(){
        return $this->hasOne(AccountYearly::class,'ac_id');
    }

    public function account_sub_group(){
        return $this->belongsTo(AccountSubGroup::class,'ac_sub_group_id');
    }

    public function sub_group_one(){
        return $this->belongsTo(TypeMaster::class,'sub_group_id1');
    }

    public function sub_group_two(){
        return $this->belongsTo(TypeMaster::class,'sub_group_id2');
    }

    public function resources(){
        return $this->hasMany(SharedResource::class,'resourceable_id')->where('resourceable_type', Account::class);
    }

    public function account_detail(){
        return $this->hasOne(AccountDetail::class,'ac_id');
    }

    public function account_branches(){
        return $this->hasMany(AccountBranch::class,'ac_id');
    }

    public function account_openings(){
        return $this->hasMany(AccountOpening::class,'ac_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'ac_id');
    }

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class, 'ac_id');
    }

    public function getAccountLimit(){
        $credit_limit =  AccountDetail::where('ac_id', $this->id)->pluck('credit_limit')->first();
        $limit_account = LimitAccount::where('ac_id',$this->id)->first();
        if($limit_account){
            $type_master = TypeMaster::findOrFail($limit_account['limit_sub_group_id']);
            $credit_limit = $type_master['limit'];
        }
        return $credit_limit;
    }

    public static function getName($id)
    {
        if ($id != 0) {
            return ($acc = Account::find($id)) ? $acc->name : '';
        } else {
            return '';
        }
    }

    public function getAccountCashbookBalance($date = null)
    {
        $bal = Cashbook::where('ac_id', $this->id)
            ->select(DB::raw("sum(case when dr_cr = 'D' then amount else -1*amount end) as bal"));
        if ($date) {
            $dt1 = getDateFormat($date, 'ymd');
            $bal = $bal->where('cash_book.trans_date', '<=', $dt1);
        }
        $bal = $bal->first();
        if ($bal) {
            $balance =  $bal->bal; // CAN BE POSITIVE OR NEGATIVE
        } else {
            $balance =  0;
        }
        return $balance;
    }
}
