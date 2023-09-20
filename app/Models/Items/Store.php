<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Store extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "stores";
    protected $connection = 'mysql';
    protected $fillable = [
        'name'
    ];

}
