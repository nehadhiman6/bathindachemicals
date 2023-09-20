<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class District extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "districts";
    protected $connection = 'mysql';
    protected $fillable = [
        'name','state_id','country_id'
    ];
    public function state()
    {
        return  $this->belongsTo(State::class,'state_id');
    }
}
