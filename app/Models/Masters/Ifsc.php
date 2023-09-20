<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Ifsc extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "ifscs";
    protected $connection = 'mysql';
    protected $fillable = [
        'ifsc_code','bank_id','branch','micr_code','bsr_code',
    ];
    public function bank()
    {
        return  $this->belongsTo(Bank::class,'bank_id');
    }
}
