<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class BranchBankAccount extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "branch_bank_acc";
    protected $connection = 'mysql';
    protected $fillable = [
        'branch_id',
        'benificiary_name',
        'bank_account_number',
        'ifsc_id',
    ];

    public function ifsc()
    {
        return  $this->belongsTo(Ifsc::class,'ifsc_id');
    }
}
