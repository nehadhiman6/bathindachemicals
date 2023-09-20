<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ItemUnit extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "item_units";
    protected $connection = 'mysql';
    protected $fillable = [
        'unit_name',
        'code',
        'uqc_id',
    ];

    public function uqc(){
        return $this->belongsTo(Uqc::class,'uqc_id');
    }
}
