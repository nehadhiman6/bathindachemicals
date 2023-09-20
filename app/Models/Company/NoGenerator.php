<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoGenerator extends Model
{
    use HasFactory;
    protected $table = 'no_generator';
    protected $connection = 'company_db';
    protected $fillable = ['idname', 'no', 'prefix'];
}
