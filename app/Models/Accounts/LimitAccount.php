<?php

namespace App\Models\Accounts;

use App\Models\Masters\TypeMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class LimitAccount extends Model
{
    use HasFactory;    use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "limit_accounts";
    protected $connection = 'mysql';
    protected $fillable = [
        'ac_id',
        'limit_sub_group_id',
    ];

    public function account(){
        return $this->belongsTo(Account::class,'ac_id');
    }

    public function limit_sub_group(){
        return $this->belongsTo(TypeMaster::class,'limit_sub_group_id');
    }

    public function getLimit(){

    }
}
