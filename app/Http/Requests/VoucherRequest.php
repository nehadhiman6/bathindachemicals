<?php

namespace App\Http\Requests;

use App\Models\Accounts\Account;
use App\Models\Masters\TdsSection;
use App\Models\Yearly\AccountYearly;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Cashbook;
use App\Models\Yearly\Voucher;
use App\Models\Yearly\VoucherDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoucherRequest extends FormRequest
{
    protected $_details = null;
    protected $_cashcr = 'T';
    protected $_voucher_type = '';
    protected $cash_books = [];
    protected $current_branch_id;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'voucher_date' => "required|date_format:d-m-Y",
            'voucher_type' => 'required|in:R,P,J,T,I',
            'acid_other' => 'required_unless:voucher_type,J',
            'acid_tds' => 'required_if:voucher_type,I',
            'voucher_details.*.amount' => 'required|numeric|min:0.01',
            // 'voucher_details.*.cheque_no' => 'nullable|min:6|max:6',
            'voucher_details.*.tds_chl_no' => 'max:25',
            'voucher_details.*.chl_date' => 'max:25',
            'voucher_details.*.ac_id' => 'required|exists:' . getYearlyDbConn() . '.accounts,ac_id',
            'uid' => 'nullable|unique:' . getYearlyDbConn() . '.vouchers,uid,' . $this->form_id,
        ];
        $acid_other_req = 'Y';
        if ($this->voucher_type == 'J') {
            $acid_other_req = 'N';
        } else {
            if (in_array($this->voucher_type, ['P', 'R']) && $this->tr_type == 'C') {
                $acid_other_req = 'N';
            }
        }
        if ($this->voucher_type == 'I') {
            $acid_other_req = 'Y';
            $rules['voucher_details.*.disc_tds_amt'] = 'required';
        }
        if ($acid_other_req == 'Y') {
            $rules['acid_other'] = 'required|integer|min:1';
        }

        if ($this->voucher_type != 'J') {
            $rules['tr_type'] = 'required';
        }
        if ($this->voucher_type == 'J') {
            $rules['voucher_details.*.drcr'] = 'required';
        }

        $this->_voucher_type = substr($this->voucher_type, 0, 1);
        $this->_details = Arr::where($this->voucher_details, function ($value, $key) {
            if (is_numeric($value['amount']) && $value['amount'] > 0) {
                return $value;
            }
        });
        $dramt = 0;
        $cramt = 0;
        //dd($this->_details);
        foreach ($this->_details as $key => $value) {
            $value['bill_wise'] = ($account = Account::whereId($value['ac_id'])->first()) ? $account->bill_wise : 'N';
            if (in_array($this->_voucher_type, ['R', 'I'])) {
                if($this->tr_type == 'A') {
                    $value['drcr'] = 'D';
                } else {
                    $value['drcr'] = 'C';
                }
            } elseif ($this->_voucher_type == 'P') {
                $value['drcr'] = 'D';
            } elseif ($this->_voucher_type == 'T') {
                if ($this->tr_type == 'D') {
                    $value['drcr'] = 'C';
                } else {
                    $value['drcr'] = 'D';
                }
            }
            if ($value['bill_wise'] == 'Y' && getBillWiseConcept() == "Y") {
                $entry_bills = Arr::where($value['bills'], function ($value1, $key) {
                    if (is_numeric($value1['amount']) && $value1['amount'] > 0 && strpos("DC", $value1['dr_cr']) !== false) {
                        return $value1;
                    }
                });
                $amt = 0;
                foreach ($entry_bills as $val) {
                    if ($val['dr_cr'] == $value['drcr']) {
                        $amt += $val['amount'];
                    } else {
                        $amt -= $val['amount'];
                    }
                }
                // if ($value['amount'] != $amt) {
                //     $rules["voucher_details.$key.amountmismatch"] = 'required';
                // }
                $value['bills'] = $entry_bills;
            }
            if ($value['drcr'] == 'D') {
                $dramt += $value['amount'];
            } else if ($value['drcr'] == 'C') {
                $cramt += $value['amount'];
            }
            $this->_details[$key] = $value;
            $this->_details[$key]['disc_tds_amt'] = floatval($value['disc_tds_amt']);
        };
        if (strpos("PR", $this->_voucher_type) !== false && $this->tr_type == 'C') {
            $this->_cashcr = 'C';
        }
        if ($this->_voucher_type == 'J') {
            if (round($dramt, 2) != round($cramt, 2)) {
                $rules['gen_msg'] = 'required';
            }
        }

        $permissions = getPermissions();
        return $rules;
    }

    public function messages()
    {
        $msgs = [
            'voucher_details.*.amountmismatch' => "Bill Wise Details Amount Mismatch!",
            'voucher_date.required' => 'Voucher Date is required',
            'voucher_type.required' => 'Voucher Type is required',
            'voucher_type.in' => 'Voucher Type is not valid',
            'acid_other.required_unless' => 'Account is required',
            'acid_tds.required_unless' => 'Account is required',
            'gen_msg.required' => "Debit/Credit Total Mismatch",
            'tr_type.required' => "The TR type field is required.",
            'voucher_details.*.drcr.required' => "DrCr field is required.",
            'voucher_details.*.disc_tds_amt.required' => "TDS Amount is required.",
            'date_msg.required' => "You don't have permission to update this form !!",
            'acid_other.min' => "Account can not be Blank !!",
            'msg_date.required' => "You can't modify This Entry ",
        ];
        return $msgs;
    }


    public function save()
    {
        $this->current_branch_id = getCurrentBranchId();
        validateBranch();
        $voucher = Voucher::findOrNew($this->form_id);
        $old_prefix = $voucher->prefix;
        $old_date = $voucher->voucher_date;
        $detids = $voucher->voucher_details->pluck('id')->toArray();
        $cbids = $voucher->cashbooks->pluck('id')->toArray();
        $billids = $voucher->bills->pluck('id')->toArray();
        $voucher->fill($this->all());
        if($this->form_id == 0) {
            $voucher->branch_id = $this->current_branch_id;
        }
        $voucher->setPrefix($this->voucher_date);
        $new_voucher = 'Y';
        if($voucher->prefix == $old_prefix) {
            $new_voucher = 'N';
        }
        $voucher->fill(['acid_tds' => intval($this->acid_tds), 'acid_other' => intval($this->acid_other), 'approved' => $this->approved]);
        // if ($this->approved == 'N') {
        //     $voucher->approved_by = 0;
        //     $voucher->approved_at = null;
        // }

        $voucher_details = [];
        $bills = [];
        $sno =1;
        foreach ($this->_details as $key => $entry) {
            $detid = array_splice($detids, 0, 1);
            if (count($detid) > 0) {
                $id = $detid[array_keys($detid)[0]];
                $voucher_detail = VoucherDetail::findOrFail($id);
            } else {
                $voucher_detail = new VoucherDetail();
            }
            $voucher_detail->fill($entry);
            $voucher_detail->sno = $sno;
            $voucher_details[$key] = $voucher_detail;
            if($entry['section_id'] > 0) {
                $voucher_detail->acid_tds = TdsSection::getAcId($entry['section_id'],$entry['rate']);
            }
            // dd($entry['bill_wise'] == 'Y' && getBillWiseConcept() == "Y");
            if ($entry['bill_wise'] == 'Y' && getBillWiseConcept() == "Y") {
                foreach ($entry['bills'] as $billval) {
                    $arr = getResultModel($billids, Bill::class, ['branch_id'=>$this->current_branch_id,'trans_date' => $this->voucher_date, 'ac_id' => $entry['ac_id'], 'vcode' => $voucher->vcode] + $billval);
                    $bills[$key][] = $arr[0];
                    $billids = $arr[1];
                }
            }
            $sno++;
        }
        $cbids = $this->getCashCbookDetails($voucher_details,$cbids,$voucher->vcode);
        DB::connection(getYearlyDbConn())->beginTransaction();
        $uid = addUid($voucher->uid);
        if ($this->form_id == 0 || $new_voucher == 'Y') {
            $generated_no = getYearlyNextEntryNumberFront($voucher->prefix);
            $voucher->voucher_no_part = $generated_no;
            $voucher->voucher_no = substr($voucher->prefix,0,2) . $generated_no;
        }
        $voucher->vcode = 'VC';
        $voucher->save();
        $voucher->vcode = 'VC' . $voucher->id;
        $voucher->save();
        if ($this->form_id == 0) {
            foreach ($bills as $val) {
                foreach ($val as $val1) {
                    $val1->vcode = $voucher->vcode;
                }
            }
        }
        $this->setPart($voucher->voucher_no);
        foreach ($voucher_details as $key => $val) {
            $val->voucher_id = $voucher->id;
            $val->save();
            if (getBillWiseConcept() == 'Y' && $this->_details[$key]['bill_wise'] == 'Y') {
                $val->bills()->saveMany($bills[$key]);
            }
        }
        $voucher->cashbooks()->saveMany($this->cash_books);
        if ($this->form_id != 0) {
            VoucherDetail::whereIn('id', $detids)->delete();
            Cashbook::whereIn('id', $cbids)->delete();
            Bill::whereIn('id', $billids)->delete();
        }
        // if($this->_cashcr == 'C' && ($this->_voucher_type == 'P' || intval($this->form_id) == 0)) {
        //     $msg = checkNegativeCash($this->voucher_date,$old_date);
        //     if ($msg) {
        //         DB::connection(getYearlyDbConn())->rollback();
        //         throw ValidationException::withMessages(['gen_msg' => $msg]);
        //     }
        // }
        DB::connection(getYearlyDbConn())->commit();
        return reply(true, ['voucher' => $voucher]);
    }

    private function getCashCbookDetails($vdets,$cbids,$vcode) {
        $cbooks = [];
        $bank_name = '';
        // if($this->_voucher_type != 'J' && !in_array($this->_voucher_type.$this->tr_type,['PC','RC'])) {
        //     $bank_name = Account::getName(intval($this->acid_other));
        // }
        // drcr, sno, ac_id, amount, disc_tds_amt, part, cheque_no, weight, section_id, rate, tds_on, tds_adj, tds_amt, acid_tds, tds_part
        foreach($vdets as $entry) {
            $acc = Account::find($entry->ac_id);
            $acnm = $acc ? '  ' . $acc->name : '  ';
            $chq_no = $entry->cheque_no ? ' chq No.' . $entry->cheque_no . ' ' : '';
            if ($this->_voucher_type != 'I' || $this->tr_type != 'T') {
                if($this->_voucher_type == 'I' && $this->tr_type == 'A') {
                    $amt = $entry->amount-floatval($entry->disc_tds_amt);
                } else {
                    $amt = $entry->amount;
                }
                $adv = '';
                if($this->_voucher_type == 'P' && $this->tr_type == 'B') {
                    $bal = Cashbook::getBalance($entry->ac_id,$this->voucher_date,-1,'v',$vcode);
                    $bal += $amt;
                    if($bal > 0) {
                        $adv = 'Adv ';
                    }
                }
                $cbooks[] = ['ac_id' => $entry->ac_id, 'dr_cr' => $entry->drcr,'amount' => $amt, 'part' => $adv.$bank_name .' '.strval($entry->part) . $chq_no,'s_no' => $entry->sno,'weight' => $entry->weight];
            }
            if ($this->_voucher_type == 'I') {
                $cbooks[] = ['ac_id' => $entry->ac_id, 'dr_cr' => 'D','amount' => $entry->disc_tds_amt, 'part' =>$bank_name.' '.strval($entry->part)];
                $cbooks[] = ['ac_id' => $this->acid_tds, 'dr_cr' => 'C','amount' => $entry->disc_tds_amt, 'part' => strval($acnm .' '. $entry->part)];
            }
            if(intval($entry->acid_tds) != 0) {
                $prt = 'Tds on '.$entry->tds_on.' @ '.$entry->rate.' % '.$entry->tds_part;
                $cbooks[] = ['ac_id' => $entry->ac_id, 'dr_cr' => 'D','amount' => $entry->tds_amt, 'part' => $prt];
                $cbooks[] = ['ac_id' => $entry->acid_tds, 'dr_cr' => 'C','amount' => $entry->tds_amt, 'part' => $prt.' '.$acnm];
            }
            if ($this->_cashcr == 'T' && $this->tr_type != 'T' && $this->_voucher_type != 'J') {
                if($this->_voucher_type == 'I' && $this->tr_type == 'A') {
                    $amt = $entry->amount-floatval($entry->disc_tds_amt);
                } else {
                    $amt = $entry->amount;
                }
                $cbooks[] = ['ac_id' => $this->acid_other,'dr_cr' => getDrCrRev($entry->drcr),'amount' => $amt, 'part' => strval($acnm.' '.$entry->part. $chq_no),'s_no' => $entry->sno];
            }
        }
        $fix_values = ['branch_id'=>$this->current_branch_id,'trans_date' => $this->voucher_date,'cash_cr' => $this->_cashcr];
        foreach($cbooks as $cb) {
            $arr = getResultModel($cbids,Cashbook::class,$cb+$fix_values);
            $cbids = $arr[1];
            $this->cash_books[] = $arr[0];
        }
        return $cbids;
    }

    private function setPart($vchr_no) {
        foreach($this->cash_books as $cb) {
            $cb->vchr_no = $vchr_no;
        }
    }
}


