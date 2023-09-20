<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoGenerator extends Model
{
    use HasFactory;
    protected $table = 'no_generator';
    protected $connection = 'mysql';
    protected $fillable = ['idname', 'no', 'prefix'];
}
