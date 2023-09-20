<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class PartyCategory extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "party_cats";
    protected $connection = 'mysql';
    protected $fillable = [
        'name',
        'rate_diff_applicable',
        'packing_id',
    ];


    public function packing(){
        return $this->belongsTo(Packing::class,'packing_id');
    }
    public function category_brands(){
        return $this->hasMany(BrandCategory::class,'category_id');
    }

}
