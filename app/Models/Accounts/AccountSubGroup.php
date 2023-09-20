<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountSubGroup extends Model
{
    public $timestamps = false;
    protected $table = "ac_sub_groups";
    protected $connection = 'mysql';
    protected $fillable = [
        'sub_group_name',
        'ac_group_id',
    ];

    public static function getAcGrpId($grp_name) {
        return ($t = static::where('sub_group_name',$grp_name)->first()) ? $t->ac_group_id:0;
    }
}
