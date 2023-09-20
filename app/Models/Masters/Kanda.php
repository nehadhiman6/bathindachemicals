<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Kanda extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "kanda";
    protected $connection = 'company_db';
    protected $fillable = [
        'name','api_key','criteria','prefix','tkt_prefix','vsiteid','url'
    ];
}
