<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class ItemLocation extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "item_locations";
    protected $connection = 'mysql';
    protected $fillable = [
        'item_id',
        'store_id',
    ];

    public function store(){
        return $this->belongsTo(Store::class,'store_id');
    }
}
