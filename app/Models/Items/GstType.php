<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class GstType extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "gst_types";
    protected $connection = 'mysql';
    protected $fillable = [
        'gst_id', 'wef_date', 'gst_type'
    ];

    public function details()
    {
        return $this->hasMany(GstDetail::class, 'gst_type_id', 'id');
    }


    public function getWefDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setWefDateAttribute($date)
    {
        $this->attributes['wef_date'] = setDateAttribute($date);
    }

    public static function getGstType($gst_id, $gst_type, $date)
    {
        return static::where('gst_id', '=', $gst_id)->where('gst_type', '=', $gst_type)->where('wef_date', '<=', getDateFormat($date, 'ymd'))->orderBy('wef_date', 'desc')->first();
    }
}
