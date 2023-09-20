<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefix extends Model
{
    use HasFactory;
    protected $table = 'prefix';
    protected $connection = 'mysql';
    protected $fillable = [  'prefix',
    'type',
    'label'];
}
