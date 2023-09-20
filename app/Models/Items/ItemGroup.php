<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ItemGroup extends Model
{
    use HasFactory; use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "item_groups";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'type',
        'm_group_id',
        's_group_id',
        'oil'
    ];

    public function main_group(){
        return $this->belongsTo(ItemGroup::class,'m_group_id');
    }

    public function sub_group(){
        return $this->belongsTo(ItemGroup::class,'s_group_id');
    }
}
