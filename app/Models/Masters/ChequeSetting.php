<?php

namespace App\Models\Masters;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class ChequeSetting extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "cheque_setting";
    protected $connection = 'mysql';
    protected $fillable = [
        'acid',
        'width',
        'height',
        'date_top',
        'date_left',
        'name_top',
        'name_left',
        'name_char',
        'amt_w1_top',
        'amt_w1_left',
        'amt_w1_char',
        'amt_w2_top',
        'amt_w2_left',
        'amt_w2_char',
        'amt_f_top',
        'amt_f_left',
        'for_top',
        'for_left',
        'for_char',
        'for_flag',
        'sign1_top',
        'sign1_left',
        'sign1_flag',
        'sign1',
        'sign2_top',
        'sign2_left',
        'sign2_flag',
        'sign2',
        'sign3_top',
        'sign3_left',
        'sign3_flag',
        'sign3',
        'print_mode',
        'date_font',
        'name_font',
        'amt_w1_font',
        'amt_w2_font',
        'amt_f_font',
        'tds_flag',
        'tds_top',
        'tds_left',
        'tds_char',
        'tds_font',
    ];

    public function account()
    {
        return  $this->belongsTo(Account::class, 'acid');
    }

    public function setTdsFlagAttribute($value)
    {
        $this->attributes['tds_flag'] = setAmountAttribute($value);
    }

    public function setTdsTopAttribute($value)
    {
        $this->attributes['tds_top'] = setAmountAttribute($value);
    }

    public function setTdsLeftAttribute($value)
    {
        $this->attributes['tds_left'] = setAmountAttribute($value);
    }

    public function setTdsCharAttribute($value)
    {
        $this->attributes['tds_char'] = setAmountAttribute($value);
    }
    public function setTdsFontAttribute($value)
    {
        $this->attributes['tds_font'] = setAmountAttribute($value);
    }
    public function setAmtW1FontAttribute($value)
    {
        $this->attributes['amt_w1_font'] = setAmountAttribute($value);
    }
    public function setAmtW2FontAttribute($value)
    {
        $this->attributes['amt_w2_font'] = setAmountAttribute($value);
    }
    public function setAmtFFontAttribute($value)
    {
        $this->attributes['amt_f_font'] = setAmountAttribute($value);
    }

    public function setSign1TopAttribute($value)
    {
        $this->attributes['sign1_top'] = setAmountAttribute($value);
    }
}
