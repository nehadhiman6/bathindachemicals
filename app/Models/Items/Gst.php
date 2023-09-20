<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits;
class Gst extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $connection = 'mysql';
    protected $table = "gst";
    protected $fillable = ['name', 'wef_date'];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = ucwords(strtolower(setNameAttribute($name)));
    }

    public function types()
    {
        return $this->hasMany(GstType::class, 'gst_id', 'id')
            ->join('gst', 'gst.id', '=', 'gst_types.gst_id')
            ->where('gst_types.wef_date', '=', DB::raw('gst.wef_date'))
            ->select('gst_types.*');
    }

    public function gst_types()
    {
        return $this->hasMany(GstType::class, 'gst_id', 'id');
    }

    public function getWefDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setWefDateAttribute($date)
    {
        $this->attributes['wef_date'] = setDateAttribute($date);
    }
}
