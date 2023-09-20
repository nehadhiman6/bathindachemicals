<?php

namespace App\Models\Company;

use App\Models\Accounts\Account;
use App\Models\Accounts\PayTerm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class PurchaseOrder extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'company_db';
    protected $table = "pur_orders";
    protected $fillable = [
        'po_type',
        'doc_no',
        'doc_no_print',
        'seller_acid',
        'buyer_acid',
        'pay_term_id',
        'del_from',
        'del_to',
        'del_extended_date',
        'condition',
        'broker_acid',
        'direct',
        'del_term',
        'remarks',
        'branch_id'
    ];

    public function details(){
        return $this->hasMany(PurchaseOrderDetail::class,'pur_ord_id');
    }

    public function getDelFromAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setDelFromAttribute($date)
    {
        $this->attributes['del_from'] = setDateAttribute($date);
    }

    public function getDelToAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setDelToAttribute($date)
    {
        $this->attributes['del_to'] = setDateAttribute($date);
    }

    public function getDelExtendedDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setDelExtendedDateAttribute($date)
    {
        $this->attributes['del_extended_date'] = setDateAttribute($date);
    }

    public function seller(){
        return $this->belongsTo(Account::class,'seller_acid');
    }

    public function buyer(){
        return $this->belongsTo(Account::class,'buyer_acid');
    }

    public function pay_term(){
        return $this->belongsTo(PayTerm::class,'pay_term_id');
    }

    public function broker(){
        return $this->belongsTo(Account::class,'broker_acid');
    }


}
