<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ItemUnitConversion extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "item_unit_conversions";
    protected $connection = 'mysql';
    protected $fillable = [
        'item_id',
        'item_unit_id',
        'multiplier',
        'divider',
        'conversion_factor',
    ];

    public function item_unit(){
        return $this->belongsTo(ItemUnit::class,'item_unit_id');
    }
}
