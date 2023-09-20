<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Country extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "countries";
    protected $connection = 'mysql';
    protected $fillable = [
        'name'
    ];
}
