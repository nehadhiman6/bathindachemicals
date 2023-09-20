<?php

namespace App\Models\Yearly;

use App\Models\Accounts\Achead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
use Illuminate\Support\Facades\DB;

class TransVatDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "trans_vat_details";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'vat_cst_id',
        'vcode',
        'vat_cst',
        'vat_cst_on',
        'vat_cst_rate',
        'vat_cst_amt',
        'surcharge_rate',
        'surcharge_amt',
        'vat_cst_acid',
        'vat_cst_surcharge_acid',
    ];


}
