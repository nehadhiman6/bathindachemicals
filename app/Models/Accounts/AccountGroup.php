<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    public $timestamps = false;
    protected $table = "ac_groups";
    protected $connection = 'mysql';
    protected $fillable = [
        'group_name',
        'under',
        'nature',
        'affect_gp'
    ];
}
