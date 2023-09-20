<?php

namespace App\Models\Masters;

use App\Models\Accounts\LimitAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class TypeMaster extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "type_masters";  //Client Vendor Pay Terms
    protected $connection = 'mysql';
    protected $fillable = [
        'name','type','limit'
    ];

    public function limit_accounts(){
        return $this->hasMany(LimitAccount::class,'limit_sub_group_id');
    }
}
