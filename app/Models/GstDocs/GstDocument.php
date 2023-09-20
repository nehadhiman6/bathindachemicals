<?php

namespace App\Models\GstDocs;

use App\Models\Auth\User;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Cashbook;
use App\Models\Accounts\Account;
use App\Models\Yearly\TransGstDetail;
use Illuminate\Database\Eloquent\Model;
use App\Models\GstDocs\GstDocumentDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits;

class GstDocument extends Model
{
    use Traits\UserAutoUpdate;
    use HasFactory;
    protected $table = "gst_docs";
    protected $connection = 'yearly_db';
    protected static $other_id = 'gst_doc_id';
    protected $fillable = [
        'vcode', 'doc_date', 'doc_type', 'l_o_type', 'ac_gst_type', 'doc_no', 'doc_no_print', 'party_doc_no', 'party_doc_date',
        'gst_reason_id', 'cash_cr', 'pur_sale_type', 'rev_charges', 'ac_id', 'gr_no', 'gr_date', 'ref_no', 'ref_date',
        'tpt_id', 'veh_no', 'add_less_other1_amount', 'acid_add_less_other1', 'add_less_other2_amount', 'acid_add_less_other2', 'add_oth_amt',
        'less_oth_amt', 'remarks', 'gst_amt', 'round_off', 'doc_amt', 'stock_entry','tds_per', 'tds_on', 'tds_amount', 'tds_account', 'tds_type', 'uid','branch_id'
        ,'freight_type','freight_amt','eway_bill_no','eway_bill_date','doc_no_print','bill_no','fyear'

    ];
    public function getDocDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setDocDateAttribute($date)
    {
        $this->attributes['doc_date'] = setDateAttribute($date);
    }

    public function getPartyDocDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setPartyDocDateAttribute($date)
    {
        $this->attributes['party_doc_date'] = setDateAttribute($date);
    }

    public function getGrDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setGrDateAttribute($date)
    {
        $this->attributes['gr_date'] = setDateAttribute($date);
    }

    public function getRefDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setRefDateAttribute($date)
    {
        $this->attributes['ref_date'] = setDateAttribute($date);
    }

    public function getEwayBillDateAttribute($date)
    {
        return getDateAttribute($date);
    }
    public function setEwayBillDateAttribute($date)
    {
        $this->attributes['eway_bill_date'] = setDateAttribute($date);
    }

    public function details()
    {
        return $this->hasMany(GstDocumentDetail::class, 'doc_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'ac_id', 'id');
    }

    public function ac_less_one()
    {
        return $this->belongsTo(Account::class, 'acid_add_less_other1', 'id');
    }

    public function ac_less_two()
    {
        return $this->belongsTo(Account::class, 'acid_add_less_other2', 'id');
    }
    public function tds_ac()
    {
        return $this->belongsTo(Account::class, 'tds_account', 'id');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function transport()
    {
        return $this->belongsTo(Account::class, 'tpt_id', 'id');
    }

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class, 'vcode', 'vcode');
    }

    public function trans_gst_details()
    {
        return $this->hasMany(TransGstDetail::class, 'vcode', 'vcode');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'vcode', 'vcode');
    }

    // public static function delDocument($id)
    // {
    //     $gst_doc = static::find($id);
    //     DB::beginTransaction();
    //     GstDocumentDetail::where('doc_id', $gst_doc->id)->delete();
    //     TransGstDetail::where('vcode', $gst_doc->vcode)->delete();
    //     Cashbook::where('vcode', $gst_doc->vcode)->delete();
    //     Bill::where('vcode', $gst_doc->vcode)->delete();
    //     StockLgr::where('vcode', $gst_doc->vcode)->delete();
    //     ScrapLgr::where('vcode', $gst_doc->vcode)->delete();
    //     $gst_doc->delete();
    //     DB::commit();
    // }

    // public function einvoiceData()
    // {
    //     return $this->belongsTo(EInvoiceData::class, 'vcode', 'vcode');
    // }



    public function setAddLessOther1AmountAttribute($value)
    {
        $this->attributes['add_less_other1_amount'] = setAmountAttribute($value);
    }

    public function setAddLessOther2AmountAttribute($value)
    {
        $this->attributes['add_less_other2_amount'] = setAmountAttribute($value);
    }

    public function setLessOtherAmtAttribute($value)
    {
        $this->attributes['less_oth_amt'] = setAmountAttribute($value);
    }

    public function setFreightAmtAttribute($value)
    {
        $this->attributes['freight_amt'] = setAmountAttribute($value);
    }

    public function setRoundOffAttribute($value)
    {
        $this->attributes['round_off'] = setAmountAttribute($value);
    }
    public function setTdsPerAttribute($value)
    {
        $this->attributes['tds_per'] = setAmountAttribute($value);
    }

    public function setLessOthAmtAttribute($value)
    {
        $this->attributes['less_oth_amt'] = setAmountAttribute($value);
    }

    public function setAddOthAmtAttribute($value)
    {
        $this->attributes['add_oth_amt'] = setAmountAttribute($value);
    }

}
