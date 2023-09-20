<?php

namespace App\Models\Accounts;

use App\Models\Masters\City;
use App\Models\Masters\Ifsc;
use App\Models\Masters\PartyCategory;
use App\Models\Masters\State;
use App\Models\Masters\TypeMaster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class AccountDetail extends Model
{
    use HasFactory;    use Traits\UserAutoUpdate;
    public $timestamps = false;
    protected $table = "account_details";
    protected $connection = 'mysql';
    protected $fillable = [
        'ac_id',
        'print_name',
        'party_cat_id',
        'add1',
        'add2',
        'add3',
        'city_id',
        'pincode',
        'phone_no',
        'email',
        'pan_no',
        'party_gst_status',
        'gst_no',
        'contact_person',
        'contact_per_phone',
        'trade_name',
        'payment_term_id',
        'credit_limit',
        'tds_tcs',
        'ledger_ac_id',
        'client_id',
        'vendor_id',
        'vat_no',
        'cst_no',
        'ifsc_id',
        'account_no',
        'beneficiary_name',
        'msme_type',
        'e_invoice_applicable',
        'ledger_ac_id'
    ];

    public function account_ledger(){
        return $this->belongsTo(Account::class,'ledger_ac_id');
    }
    public function party_category(){
        return $this->belongsTo(PartyCategory::class,'party_cat_id');
    }
    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }

    public function pay_term(){
        return $this->belongsTo(PayTerm::class,'payment_term_id');
    }

    public function vendor(){
        return $this->belongsTo(TypeMaster::class,'vendor_id');
    }

    public function client(){
        return $this->belongsTo(TypeMaster::class,'client_id');
    }

    public function ifsc(){
        return $this->belongsTo(Ifsc::class,'ifsc_id');
    }

    public function getLocalorOutsideValue(){
        $local_outside = "C";
        $ac_id = $this->id;
        $comp = getCom();
        if($this->gst_no != null && $this->gst_no != '' && substr($this->party_gst_status,0,1) != 'U'){
            if ($comp['gst_number']  && substr($comp['gst_number'], 0, 2) === substr($this->gst_no,0,2)) {
                $local_outside = 'L';
            }
        }
        else{
            $city = City::findOrFail($this->city_id);
            if($city){
                $state = State::findOrFail($city['state_id']);
                if($state){
                    if ($comp['gst_number'] && substr($comp['gst_number'], 0, 2) === $state->state_code) {
                        $local_outside = 'L';
                    }
                }
            }
        }
        return $local_outside;
     }

}
