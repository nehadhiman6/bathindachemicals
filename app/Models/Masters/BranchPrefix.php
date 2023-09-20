<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchPrefix extends Model
{
    use HasFactory;
    protected $table = 'branch_prefixes';
    protected $connection = 'mysql';
    protected $fillable = [
        'prefix_id',
        'branch_id',
        'prefix_value',
    ];
}
