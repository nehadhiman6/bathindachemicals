<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoGenerator extends Model
{
    use HasFactory;
    protected $table = 'no_generator';
    protected $connection = 'yearly_db';
    protected $fillable = ['idname', 'no', 'prefix'];
}
