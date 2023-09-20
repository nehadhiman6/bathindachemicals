<?php

namespace App\Models\Yearly;

use App\Models\Accounts\Account;
use App\Models\Masters\Branch;
use App\Models\Masters\BranchBankAccount;
use App\Models\Masters\Brand;
use App\Models\Masters\Ifsc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class Invoice extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "invoices";
    protected $connection = 'yearly_db';
    protected $fillable = [
        'vcode',
        'branch_id',
        'sale_order_id',
        'invoice_no',
        'invoice_no_part',
        'invoice_date',
        'due_date',
        'invoice_type',
        'client_id',
        'bill_party_id',
        'ship_party_id',
        'client_po_no',
        'client_po_date',
        'sap_po_no',
        'sap_po_date',
        'transport_type',
        'transport_id',
        'vehical_no',
        'gr_lr_no',
        'gr_lr_date',
        'permit_no',
        'permit_date',
        'excise_pass_no',
        'excise_pass_date',
        'l_38_no',
        'l_38_date',
        'delivery_terms',
        'cash_cr',
        'pay_term_id',
        'supply_type',
        'd20_no',
        'd20_date',
        'remarks',
        'remarks_2',
        'benificiary_name',
        'bank_account_number',
        'ifsc_id',
        'bar_code_fees',
        'freight_per_case',
        'freight_amount',
        'court_fee',
        'reverse_charge_applicable',
        'add_excise_per_pl',
        'add_excise_amount',
        'less_excise_per_pl',
        'less_excise_amount',
        'freight',
        'export_fee',
        'gst_vat_amt',
        'surcharge_amt',
        'discount_amt',
        'basic_amount',
        'tcs_per',
        'tcs_amount',
        'round_off',
        'net_amt',
        'vessel_name',
        'is_liqour',
        'packed_loose',
        'local_outside',
        'ethanol_bill',
        'ethanol_category',
        'excise_certificate',
        'denaturant',
        'crotonaldehde',
        'denatonium_saccaride',
        'denatonium_benzoate',
        'tanker_seal_no',
        'uid'
    ];

    protected $appends = ['l_38_date'];


    public function setInvoiceDateAttribute($date)
    {
        $this->attributes['invoice_date'] = setDateAttribute($date);
    }

    public function getInvoiceDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setDueDateAttribute($date)
    {
        $this->attributes['due_date'] = setDateAttribute($date);
    }

    public function getDueDateAttribute($date)
    {
        return getDateAttribute($date);
    }


    public function setClientPoDateAttribute($date)
    {
        $this->attributes['client_po_date'] = setDateAttribute($date);
    }

    public function getClientPoDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setSapPoDateAttribute($date)
    {
        $this->attributes['sap_po_date'] = setDateAttribute($date);
    }

    public function getSapPoDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setGrLrDateAttribute($date)
    {
        $this->attributes['gr_lr_date'] = setDateAttribute($date);
    }

    public function getGrLrDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setPermitDateAttribute($date)
    {
        $this->attributes['permit_date'] = setDateAttribute($date);
    }

    public function getPermitDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setExcisePassDateAttribute($date)
    {
        $this->attributes['excise_pass_date'] = setDateAttribute($date);
    }

    public function setInvoiceNoPartAttribute($inv_no)
    {
        $this->attributes['invoice_no_part'] = setAmountAttribute($inv_no);
    }


    public function getExcisePassDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function setL38DateAttribute($date)
    {
        $this->attributes['l_38_date'] = setDateAttribute($date);
    }

    public function getL38DateAttribute()
    {
        return getDateAttribute($this->attributes['l_38_date']);
    }


    public function setD20DateAttribute($date)
    {
        $this->attributes['d20_date'] = setDateAttribute($date);
    }

    public function getD20DateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function client(){
        return $this->belongsTo(Account::class,'client_id');
    }
    public function bill_party(){
        return $this->belongsTo(Account::class,'bill_party_id');
    }
    public function ship_party(){
        return $this->belongsTo(Account::class,'ship_party_id');
    }
    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }
    public function transport(){
        return $this->belongsTo(Account::class,'transport_id');
    }
    public function sale_order(){
        return $this->belongsTo(SaleOrder::class,'sale_order_id');
    }
    public function ifsc(){
        return $this->belongsTo(Ifsc::class,'ifsc_id');
    }

    public function account_number(){
        return $this->belongsTo(BranchBankAccount::class,'bank_account_number','bank_account_number');
    }

    public function invoice_details(){
        return $this->hasMany(InvoiceDetail::class,'invoice_id');
    }

    public function cash_books()
    {
        return $this->hasMany(Cashbook::class, 'vcode', 'vcode');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'vcode', 'vcode');
    }

    public function trans_gst_details()
    {
        return $this->hasMany(TransGstDetail::class, 'vcode', 'vcode');
    }

    public function trans_vat_details()
    {
        return $this->hasMany(TransVatDetail::class, 'vcode', 'vcode');
    }


    public function bl_details()
    {
        return $this->hasMany(BlDetail::class, 'invoice_id');
    }


}
