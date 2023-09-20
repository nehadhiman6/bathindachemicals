<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Brand extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "brands";
    protected $connection = 'mysql';
    protected $fillable = ['name'];

    public function brand_categories(){
        return $this->hasMany(BrandCategory::class,'brand_id');
    }

}
