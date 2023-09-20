<?php

namespace App\Models\Items;

use App\Models\Masters\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class ItemBranch extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "item_branches";
    protected $connection = 'mysql';
    protected $fillable = [
        'item_id',
        'branch_id',
    ];

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }
}
