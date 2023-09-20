<?php

namespace App\Models\Company;

use App\Models\Items\Item;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class FixRateDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "fix_rate_dets";
    protected $fillable = [
        'item_id',
        'fix_rate_id',
        'packing_id',
        'rate',
    ];


    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function packing(){
        return $this->belongsTo(Packing::class,'packing_id');
    }

}
