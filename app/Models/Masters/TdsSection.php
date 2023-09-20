<?php

namespace App\Models\Masters;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class TdsSection extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "tds_sections";
    protected $connection = 'mysql';
    protected $fillable = [
        'section', 'rate1','ac_id1',
        'rate2','ac_id2','rate3','ac_id3',
        'non_pan_rate','ac_id4','higher_rate','ac_id5'
    ];

    public function account1()
    {
        return  $this->belongsTo(Account::class, 'ac_id1');
    }

    public function account2()
    {
        return  $this->belongsTo(Account::class, 'ac_id2');
    }

    public function account3()
    {
        return  $this->belongsTo(Account::class, 'ac_id3');
    }

    public function account4()
    {
        return  $this->belongsTo(Account::class, 'ac_id4');
    }

    public function account5()
    {
        return  $this->belongsTo(Account::class, 'ac_id5');
    }

    public static function getAcId($section_id,$rate) {
        $section = static::where('id',$section_id)->first();
        $acid = 0;
        if($section) {
            for($i=1;$i<=5;$i++) {
                $rate_var = 'rate'.$i;
                $acc_var = 'ac_id'.$i;
                if($section->$rate_var == $rate) {
                    $acid = $section->$acc_var;
                }
            }
        }
        return $acid;
    }
}
