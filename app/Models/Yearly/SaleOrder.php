<?php

namespace App\Models\Yearly;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class SaleOrder extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "sale_orders";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'vcode',
        'sale_order_no',
        'branch_id',
        'sale_order_date',
        'client_id',
        'bill_party_id',
        'ship_party_id',
        'transport_id',
        'vehical_no',
        'transport_type',
        'packed_loose',
        'discount_amt',
        'freight_amt',
        'export_fee',
        'basic_amount',
        'gst_vat_amt',
        'tcs_per',
        'tcs_amt',
        'net_amt',
        'own_vehicle',
        'freight',
        'dispatch_advice',
        'uid',
    ];

     public function setSaleOrderDateAttribute($date)
    {
        $this->attributes['sale_order_date'] = setDateAttribute($date);
    }

    public function getSaleOrderDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function sale_order_details(){
        return $this->hasMany(SaleOrderDetail::class,'sale_order_id');
    }
    public function client(){
        return $this->belongsTo(Account::class,'client_id');
    }
    public function bill_party(){
        return $this->belongsTo(Account::class,'bill_party_id');
    }
    public function ship_party(){
        return $this->belongsTo(Account::class,'ship_party_id');
    }
    public function transport(){
        return $this->belongsTo(Account::class,'transport_id');
    }
    public function sale_order_dispatches(){
        return $this->hasMany(SaleOrderDispatch::class,'sale_order_id');
    }

    public function sale_invoice(){
        return $this->hasOne(Invoice::class,'sale_order_id');
    }

}
