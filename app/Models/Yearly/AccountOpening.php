<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class AccountOpening extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "account_openings";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'ac_id',
        'branch_id',
        'dr_cr',
        'opening_amount',
    ];


    public function bills()
    {
        return $this->hasMany(Bill::class, 'det_id', 'id');
    }
}
