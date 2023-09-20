<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Preference extends Model
{
    use HasFactory;
    protected $table = 'preferences';
    protected $fillable = ['user_id', 'para_name', 'para_value'];
    protected $connection = 'mysql';
}
