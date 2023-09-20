<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Company extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "companies";
    protected $connection = 'mysql';
    protected $fillable = [
        'company_name','print_name','office_address','website','gst_number','pan_number','cin_number','remarks','year','shared_comp_id'
    ];
}
