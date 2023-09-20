<?php

namespace App\Models\Yearly;

class GetParameters
{
    protected $para = null;

    public function __construct() {
        $this->para = Parameter::all()->groupBy('para_name')->toArray();
        // $this->para = $this->para->groupBy('para_name')->toArray();
    }

    public function getParaValue($para_name)
    {
        $param = array_key_exists($para_name,$this->para) ? $this->para[$para_name][0]:null;
        if ($param) {
            return ($param['para_type'] == 'numeric') ? floatval($param['para_value']) : $param['para_value'];
        } else {
            return '0';
        }
    }
}
