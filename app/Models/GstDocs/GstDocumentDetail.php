<?php

namespace App\Models\GstDocs;

use App\Traits;
use App\Models\Items\Gst;
use App\Models\Items\Item;
use App\Models\Masters\Brand;
use App\Models\Items\GstDetail;
use App\Models\Masters\Packing;
use App\Models\Accounts\Account;
use App\Models\Yearly\TransGstDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GstDocumentDetail extends Model
{
    use Traits\UserAutoUpdate;
    use HasFactory;
    protected $table = "gst_doc_dets";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'doc_id', 's_no', 'acid_doc', 'item_id', 'item_desc', 'qty', 'rate', 'disc_prec', 'disc_amt', 'net_disc', 'amount', 'hsn_code', 'gst_id', 'gst_adj_amt', 'gst_amt', 'add_oth_amt', 'less_oth_amt', 'net_amt', 'weight','packing_id','brand_id','rate_on'
    ];
    public function gst()
    {
        return $this->belongsTo(Gst::class, 'gst_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function packing()
    {
        return $this->belongsTo(Packing::class, 'packing_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }


    public function doc_account()
    {
        return $this->belongsTo(Account::class, 'acid_doc', 'id');
    }

    public function gst_details()
    {
        return $this->hasMany(GstDetail::class, 'gst_id', 'gst_id');
    }

    public function trans_gst_details()
    {
        return $this->hasMany(TransGstDetail::class, 'det_id', 'id');
    }

    public function setDiscAmtAttribute($value)
    {
        $this->attributes['disc_amt'] = setAmountAttribute($value);
    }

    public function setGstAdjAmtAttribute($value)
    {
        $this->attributes['gst_adj_amt'] = setAmountAttribute($value);
    }

    public function setNetDiscAttribute($value)
    {
        $this->attributes['net_disc'] = setAmountAttribute($value);
    }

    public function setGstAmtAttribute($value)
    {
        $this->attributes['gst_amt'] = setAmountAttribute($value);
    }

    public function setWeightAttribute($value)
    {
        $this->attributes['weight'] = setAmountAttribute($value);
    }

    public function setLengthAttribute($value)
    {
        $this->attributes['length'] = setAmountAttribute($value);
    }
}
