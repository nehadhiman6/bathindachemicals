<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Uid extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "uid";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'uid',
    ];
}
