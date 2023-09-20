<?php

namespace App\Models\Yearly;

use App\Models\Accounts\Achead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
use Illuminate\Support\Facades\DB;

class TransGstDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "trans_gst_details";
    protected $connection = 'yearly_db';
    protected $fillable = [
       'branch_id', 'det_id', 'vcode', 'gst_det_id', 'gst_name', 'gst_rate', 'gst_on', 'gst_value', 'acid_gst', 'acid_pur_sale'
    ];

    public function account_gst()
    {
        return $this->belongsTo(Achead::class, 'acid_gst');
    }

    //public function purchase()
    //{
    // $this->belongsTo(Purchase::class, 'vcode', 'vcode');
    //}

    //public function pur_det()
    //{
    //return $this->belongsTo(PurchaseDetail::class, 'det_id', 'id');
    //}

    public static function gst_details($vcode)
    {
        return static::where('vcode', $vcode)->select([
            'gst_name', 'gst_rate', 'acid_gst',
            DB::raw("sum(gst_on) as gst_on,sum(gst_value) as gst_value")
        ])->groupBy(['gst_name', 'gst_rate', 'acid_gst'])->get();
    }

    public static function getGstQry($vc_type)
    {
        return static::where(DB::raw('left(vcode,2)'), '=', $vc_type)
            ->selectRaw("vcode,det_id,gst_on,sum(gst_value) as gst_value,sum(case when gst_name = 'cgst' then gst_rate else 0 end) as cgst,sum(case when gst_name = 'sgst' then gst_rate else 0 end) as sgst,sum(case when gst_name = 'igst' then gst_rate else 0 end) as igst,sum(case when gst_name = 'cess' then gst_rate else 0 end) as cess,sum(case when gst_name = 'cgst' then gst_value else 0 end) as cgstamt,sum(case when gst_name = 'sgst' then gst_value else 0 end) as sgstamt,sum(case when gst_name = 'igst' then gst_value else 0 end) as igstamt,sum(case when gst_name = 'cess' then gst_value else 0 end) as cessamt")
            ->groupBy(['vcode', 'det_id', 'gst_on']);
    }

    public static function getGstQryAccWise($vc_type)
    {
        return static::where(DB::raw('left(vcode,2)'), '=', $vc_type)
            ->selectRaw("vcode,det_id,gst_on,acid_pur_sale,sum(gst_value) as gst_value,sum(case when gst_name = 'cgst' then gst_rate else 0 end) as cgst,sum(case when gst_name = 'sgst' then gst_rate else 0 end) as sgst,sum(case when gst_name = 'igst' then gst_rate else 0 end) as igst,sum(case when gst_name = 'cess' then gst_rate else 0 end) as cess,sum(case when gst_name = 'cgst' then gst_value else 0 end) as cgstamt,sum(case when gst_name = 'sgst' then gst_value else 0 end) as sgstamt,sum(case when gst_name = 'igst' then gst_value else 0 end) as igstamt,sum(case when gst_name = 'cess' then gst_value else 0 end) as cessamt")
            ->groupBy(['vcode', 'det_id', 'gst_on', 'acid_pur_sale']);
    }

    public static function getGstQryTaxWise($vc_type)
    {
        $gst_qry = static::getGstQry($vc_type);
        return static::joinSub($gst_qry, 'a1', function ($join) {
            $join->on('a1.vcode', '=', 'trans_gst_details.vcode')
                ->on('a1.det_id', '=', 'trans_gst_details.det_id');
        })->where(DB::raw('left(trans_gst_details.vcode,2)'), '=', $vc_type)
            ->selectRaw("a1.vcode,a1.cgst+a1.sgst+a1.igst+a1.cess as gst_rate,a1.cgst,a1.sgst,a1.igst,a1.cess,sum(a1.gst_on) as gst_on,sum(a1.cgstamt) as cgstamt,sum(a1.sgstamt) as sgstamt,sum(a1.igstamt) as igstamt,sum(a1.cessamt) as cessamt,sum(a1.gst_value) as gst_value")
            ->whereIn('trans_gst_details.gst_name', ['cgst', 'igst'])
            ->groupBy(DB::raw("1,2,3,4,5,6"));
    }
    public static function getGstQryBillWise($vc_type)
    {
        return static::where(DB::raw('left(vcode,2)'), '=', $vc_type)
            ->selectRaw("vcode,sum(case when gst_name = 'cgst' then gst_value else 0 end) as cgstamt,sum(case when gst_name = 'sgst' then gst_value else 0 end) as sgstamt,sum(case when gst_name = 'igst' then gst_value else 0 end) as igstamt,sum(case when gst_name = 'cess' then gst_value else 0 end) as cessamt,gst_on,sum(gst_value) as gst_value,sum(case when gst_name = 'cgst' then gst_rate else 0 end) as cgst,sum(case when gst_name = 'sgst' then gst_rate else 0 end) as sgst,sum(case when gst_name = 'igst' then gst_rate else 0 end) as igst,sum(case when gst_name = 'cess' then gst_rate else 0 end) as cess")
            ->groupBy(['vcode']);
    }
}
