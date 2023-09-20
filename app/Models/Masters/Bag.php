<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Bag extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "bags";
    protected $connection = 'mysql';
    protected $fillable = [
        'name','weight'
    ];
}
