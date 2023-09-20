<?php

namespace App\Models\Yearly;

use App\Traits;
use App\Models\Masters\Branch;
use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "transfers";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'branch_id_from',
        'branch_id_to',
        'iss_date',
        'iss_doc_no',
        'iss_doc_no_print',
        'rec_date',
        'rec_doc_no',
        'rec_doc_no_print',
        'status',
        'acid_tpt',
        'vehical_no',
        'gr_no',
        'gr_date',
        'slip_no',
        'total_wt',
        'tare_wt',
        'net_wt',
        'rec_slip_no',
        'rec_total_wt',
        'rec_tare_wt',
        'rec_net_wt',
        'amount',
        'remarks',
        'received_by',
        'uid',
    ];

     public function setIssDateAttribute($date)
    {
        $this->attributes['iss_date'] = setDateAttribute($date);
    }

    public function getIssDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setGrDateAttribute($date)
    {
        $this->attributes['gr_date'] = setDateAttribute($date);
    }

    public function getGrDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setRecDateAttribute($date)
    {
        $this->attributes['rec_date'] = setDateAttribute($date);
    }

    public function getRecDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function details(){
        return $this->hasMany(TransferDetail::class,'transfer_id');
    }

    public function transport(){
        return $this->belongsTo(Account::class,'acid_tpt');
    }

    public function branch_to(){
        return $this->belongsTo(Branch::class,'branch_id_to');
    }

    public function branch_from(){
        return $this->belongsTo(Branch::class,'branch_id_from');
    }

    public function cash_books()
    {
        return $this->hasMany(Cashbook::class, 'vcode', 'vcode');
    }

}
