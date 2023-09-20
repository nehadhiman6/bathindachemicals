<?php

namespace App\Models\Yearly;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Items\Item;
use App\Models\Items\ItemUnit;
use App\Models\Masters\Packing;

class Parameter extends Model
{
    use HasFactory;
    protected $table = 'parameters';
    protected $connection = 'yearly_db';
    protected $fillable = ['para_name', 'para_disp_name', 'para_type', 'para_nature', 'para_length', 'para_decimals', 'para_value', 'para_choice', 'mandatory', 's_no','normal_special','input_required'];

    public static function getParaValue($para_name)
    {
        $param = static::where('para_name', $para_name)->first();
        if ($param) {
            return ($param->para_type == 'numeric') ? floatval($param->para_value) : $param->para_value;
        } else {
            return '0';
        }
    }

    public static function setParaValue($para_name,$para_value)
    {
        $parameter = Parameter::where('para_name',$para_name)->first();
        if($parameter) {
            $parameter->para_value = $para_value;
            $parameter->update();
        }
    }


    public function account()
    {
        return $this->belongsTo(Account::class, 'para_value', 'id');
    }
    public function packing()
    {
        return $this->belongsTo(Packing::class, 'para_value', 'id');
    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'para_value', 'id');
    }

    public function item_unit()
    {
        return $this->belongsTo(ItemUnit::class, 'para_value', 'id');
    }



    public static function checkParametersStatus()
    {
        $para = static::where(DB::raw("ifnull(para_value,'')"), '=', '')->first();
        if ($para) {
            return false;
        } else {
            return true;
        }
    }

    public function setParaValueAttribute($value)
    {
        $this->attributes['para_value'] = setParaAttribute($value);
    }


}
