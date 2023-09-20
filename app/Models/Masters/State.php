<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class State extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "states";
    protected $connection = 'mysql';
    protected $fillable = [
        'name', 'state_code','country_id'
    ];

    public function country()
    {
        return  $this->belongsTo(Country::class, 'country_id');
    }
}
