<?php

namespace App\Models\Yearly;

use App\Models\Items\Item;
use App\Models\Masters\Brand;
use App\Models\Masters\Packing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Dispatch extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "dispatches";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'vcode',
        'branch_id',
        'dispatch_date',
        'dispatch_advise_no',
        'dispatch_advise_no_part',
        'dispatch_advise',
        'sale_order_ids',
        'uid'
    ];


    public function setDispatchDateAttribute($date)
    {
        $this->attributes['dispatch_date'] = setDateAttribute($date);
    }

    public function getDispatchDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function sale_order_dispatches(){
        return $this->hasMany(SaleOrderDispatch::class,'dispatch_id');
    }
}
