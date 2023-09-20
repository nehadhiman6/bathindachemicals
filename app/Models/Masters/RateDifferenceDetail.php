<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class RateDifferenceDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = 'rate_diff_dets';
    protected $fillable = [
        'rate_diff_id',
        'rate_from',
        'rate_to',
        'rate_diff',
    ];
}
