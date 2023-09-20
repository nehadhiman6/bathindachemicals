<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class BrandCategory extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "brand_categories";
    protected $connection = 'mysql';
    protected $fillable = [
        'brand_id',
        'category_id',
    ];

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function category(){
        return $this->belongsTo(PartyCategory::class,'category_id');
    }
}
