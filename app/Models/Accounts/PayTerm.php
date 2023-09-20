<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class PayTerm extends Model
{
    use HasFactory;use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "pay_terms";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'days_1',
        'days_2',
        'days_3',
        'days_4',
        'percentage_1',
        'percentage_2',
        'percentage_3',
        'percentage_4',
    ];
}
