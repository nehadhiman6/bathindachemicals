<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;
    protected $table = "role_user";
    protected $fillable = ['role_id', 'user_id'];
    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
