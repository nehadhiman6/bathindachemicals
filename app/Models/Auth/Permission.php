<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = "permissions";
    protected $fillable = ['name', 'label', 'opt_group_id', 'order_no'];
    protected $connection = 'mysql';

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'opt_group_id', 'id')
            ->orderBy('order_no');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
