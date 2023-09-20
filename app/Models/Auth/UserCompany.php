<?php

namespace App\Models\Auth;

use App\Models\Masters\Company;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Traits;

class UserCompany extends Model
{
    use Traits\UserAutoUpdate;
    protected $table = "company_users";
    protected $fillable = ['user_id', 'company_id'];
    protected $connection = 'mysql';

    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }
}
