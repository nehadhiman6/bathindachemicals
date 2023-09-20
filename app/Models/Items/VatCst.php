<?php

namespace App\Models\Items;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class VatCst extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'mysql';
    protected $table = "vat_cst";
    protected $fillable = [
        'name',
        'vat_rate',
        'cst_rate',
        'sur_on_vat',
        'sur_on_cst',
        'vat_ac_id',
        'cst_ac_id',
        'sur_vat_ac_id',
        'sur_cst_ac_id',
    ];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = ucwords(strtolower(setNameAttribute($name)));
    }

    public function vat_account(){
        return $this->belongsTo(Account::class,'vat_ac_id');
    }

    public function cst_account(){
        return $this->belongsTo(Account::class,'cst_ac_id');
    }

    public function surcharge_vat_account(){
        return $this->belongsTo(Account::class,'sur_vat_ac_id');
    }
    public function surcharge_cst_account(){
        return $this->belongsTo(Account::class,'sur_cst_ac_id');
    }
}
