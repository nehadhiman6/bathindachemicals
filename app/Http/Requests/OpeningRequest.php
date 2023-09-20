<?php

namespace App\Http\Requests;

use App\Models\Accounts\Account;
use App\Models\Yearly\AccountOpening;
use App\Models\Yearly\Bill;
use App\Models\Yearly\Cashbook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OpeningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

     protected $account = null;
     protected $_openings = [];
     protected $bill_wise = 'N';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $this->account = Account::findOrFail($this->ac_id);
        if($this->account->bill_wise == 'Y'){
            $this->bill_wise = 'Y';
        }
        $rules = [
            'openings.*.branch_id'=>'required|exists:branches,id',
            'openings.*.ac_id'=>'required|exists:accounts,id',
            'openings.*.dr_cr'=>'required|in:D,C',
            'openings.*.opening_amount'=>'required|numeric',
        ];
        if($this->bill_wise == 'Y'){
            $rules += [
                'openings.*.bills'=>'required|Array|min:1',
            ];
        }
        $this->_openings = $this->openings;
        $dramt = 0;
        $cramt = 0;
        foreach ($this->_openings as $key => $value) {
            if ($this->bill_wise == 'Y') {
                $entry_bills = Arr::where($value['bills'], function ($value1, $key) {
                    if (is_numeric($value1['amount']) && $value1['amount'] > 0 && strpos("DC", $value1['dr_cr']) !== false) {
                        return $value1;
                    }
                });
                $amt = 0;
                foreach ($entry_bills as $bill_key => $val) {
                    $rules["openings.$key.bills.$bill_key.ref_date"] = 'required';
                    $rules["openings.$key.bills.$bill_key.ref"] = 'required';
                    if ($val['dr_cr'] == $value['dr_cr']) {
                        $amt += $val['amount'];
                    } else {
                        $amt -= $val['amount'];
                    }
                }
                if ($value['opening_amount'] != $amt) {
                    $rules["openings.$key.amountmismatch"] = 'required';
                }
                $value['bills'] = $entry_bills;
            }
            if ($value['dr_cr'] == 'D') {
                $dramt += $value['opening_amount'];
            } else {
                $cramt += $value['opening_amount'];
            }
            $this->_openings[$key] = $value;
        };
        return     $rules;
    }

    public function messages()
    {
        $msgs = [
            'openings.*.amountmismatch' => "Bill Wise details amount Mismatch! Please check bill details.",
            "openings.*.bills.*.ref_date.required"=> 'Please enter reference date in bills',
            "openings.*.bills.*.ref_key.required"=> 'Please enter reference key in bills',
        ];
        return $msgs;
    }

    public function save()
    {
        $detids = $this->account->account_openings->pluck('id')->toArray();
        $cbids = $this->account->cashbooks->pluck('id')->toArray();
        $billids = $this->account->bills->pluck('id')->toArray();

        $account_openings = [];
        $bills = [];
        $cashbooks = [];

        foreach ($this->_openings as $key => $entry) {
            $detid = array_splice($detids, 0, 1);
            if (count($detid) > 0) {
                $id = $detid[array_keys($detid)[0]];
                $account_opening = AccountOpening::findOrFail($id);
            } else {
                $account_opening = new  AccountOpening();
            }
            $account_opening->fill($entry);
            $account_openings[$key] = $account_opening;
            if($this->bill_wise == 'Y'){
                // 'vcode','trans_date','det_id','dr_cr','ref','ref_date','ref_type','ref_key','ac_id','amount'
                foreach ($entry['bills'] as $billval) {
                    $arr = getResultModel($billids, Bill::class, ['trans_date' => getToday('d-m-Y'), 'ac_id' => $entry['ac_id'], 'vcode' => 'OP','branch_id'=>$entry['branch_id']] + $billval);
                    $bills[$key][] = $arr[0];
                    $billids = $arr[1];
                }
            }


            $arr = getResultModel($cbids, Cashbook::class, ['trans_date' => getToday('d-m-Y'), 'ac_id' => $entry['ac_id'],'part'=>' ',
            'dr_cr' => $entry['dr_cr'], 'cash_cr' => 'T', 'amount' => $entry['opening_amount'],'vcode'=>'OP' ,'branch_id'=>$entry['branch_id']]);
            $cashbooks[] = $arr[0];
            $cbids = $arr[1];

            // CASHBOOK
        }

        foreach ($bills as $val) {
            foreach ($val as $val1) {
                $val1->ac_id = $this->account->id;
            }
        }


        DB::connection(getYearlyDbConn())->beginTransaction();
        foreach ($account_openings as $key => $opening) {
            $opening->ac_id = $this->account->id;
            $opening->save();
            if($this->bill_wise == 'Y'){
                foreach($bills[$key] as $bill){
                    $bill->det_id = $opening['id'];
                }
                $opening->bills()->saveMany($bills[$key]);
            }
        }
        $this->account->cashbooks()->saveMany($cashbooks);
        if ($this->form_id != 0) {
            AccountOpening::whereIn('id', $detids)->delete();
            Cashbook::whereIn('id', $cbids)->delete();
            Bill::whereIn('id', $billids)->delete();
        }
        DB::connection(getYearlyDbConn())->commit();
        return reply(true, ['account' => $this->account]);
    }
}
