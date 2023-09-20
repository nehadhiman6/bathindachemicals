<?php

namespace App\Models\Masters;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Branch extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "branches";
    protected $connection = 'mysql';
    protected $guarded = ['form_id',];


    public function company()
    {
        return  $this->belongsTo(Company::class,'company_id');
    }
    public function city()
    {
        return  $this->belongsTo(City::class,'city_id');
    }
    public function ifsc()
    {
        return  $this->belongsTo(Ifsc::class,'ifsc_id');
    }
    public function account()
    {
        return  $this->belongsTo(Account::class,'account_id');
    }

    public static function getBranchName($id) {
        return ($t = static::find($id)) ? $t->name:'';
    }
}


