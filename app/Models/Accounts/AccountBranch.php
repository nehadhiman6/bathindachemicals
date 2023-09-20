<?php

namespace App\Models\Accounts;

use App\Models\Masters\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class AccountBranch extends Model
{
    use HasFactory;    use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "account_branches";
    protected $connection = 'mysql';
    protected $fillable = [
        'ac_id',
        'branch_id',
    ];

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }
}
