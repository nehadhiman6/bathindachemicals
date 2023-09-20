<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Bank extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "banks";
    protected $connection = 'mysql';
    protected $fillable = [
        'name'
    ];
}
