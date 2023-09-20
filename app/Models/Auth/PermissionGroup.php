<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $table = 'permission_groups';
    protected $fillable = ['opt_group', 'order_no'];
    protected $connection = 'mysql';

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'opt_group_id', 'id')
            ->orderBy('order_no');
    }
}
