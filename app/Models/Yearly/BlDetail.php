<?php

namespace App\Models\Yearly;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class BlDetail extends Model
{

    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "bl_dets";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'invoice_id',
        'bl_no',
        'bl_date',
        'bl_qty',
    ];
}
