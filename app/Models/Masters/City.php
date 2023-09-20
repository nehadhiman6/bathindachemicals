<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class City extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "cities";
    protected $connection = 'mysql';
    protected $fillable = [
        'name', 'district_id'
    ];
    public function district()
    {
        return  $this->belongsTo(District::class, 'district_id');
    }
}
