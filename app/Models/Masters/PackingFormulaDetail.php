<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class PackingFormulaDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "packing_formula_dets";
    protected $connection = 'mysql';
    protected $fillable = [
        'pack_formula_id',
        'brand_id',
        'packing_id',
        'conversion',
        'tin_cost',
        'extra',
        'divisor',
        'muliplier',
        'packing_cost',
        'freight',
        'weight',
        'net_rate', // before GST
        'final_rate'
    ];

    public function packing(){
        return $this->belongsTo(Packing::class,'packing_id');
    }
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

}
