<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class TermCondition extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "terms_and_conditions";  //Client Vendor Pay Terms
    protected $connection = 'mysql';
    protected $fillable = [
        'sno',
        'type',
        'terms',
    ];
}
