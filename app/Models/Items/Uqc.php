<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uqc extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "uqcs";
    protected $connection = 'mysql';
    protected $fillable = [
        'uqc',
        'uqc_name',
        'uqc_disp_name',
    ];
}
