<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class AccountYearly extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "accounts";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'ac_id',
        'local_outside',
    ];
}
