<?php

namespace App\Http\Requests;

use App\Models\Accounts\Account;
use App\Models\Accounts\AccountBranch;
use App\Models\Accounts\AccountDetail;
use App\Models\Accounts\AccountGroup;
use App\Models\Accounts\AccountSubGroup;
use App\Models\Yearly\AccountYearly;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class AccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
        $rules = [
            'name' =>'required',
            'ac_sub_group_id'=>'required|exists:ac_sub_groups,id',
            // 'sub_group_id1'=>'required|exists:type_masters,id',
            'remarks'=>'nullable|max:400'
        ];
        if($this->type == 'party'){
            $rules+=[
                'city_id'=>'required|exists:cities,id',
                'add1'=>'required|max:1000',
                'add2'=>'nullable|max:1000',
                'add3'=>'nullable|max:1000',
                'pincode'=>'required',
                'phone_no'=>'nullable|numeric',
                'email'=>'nullable|email',
                'party_gst_status'=>'required',
                'pan_no'=>'nullable|size:10',

                'contact_person' =>'nullable|max:191',
                'contact_per_phone' =>'nullable|digits:10',
                'trade_name'=>'required|max:191',
                'payment_term_id'=>'nullable|exists:pay_terms,id',
                // 'credit_limit' =>'nullable|numeric',
                'tds_tcs' =>'nullable|in:TDS,TCS,Higher TCS,Higher TDS',
                'client_id'=>'nullable|exists:type_masters,id',
                'vendor_id'=>'nullable|exists:type_masters,id',
                'ifsc_id'=>'nullable|exists:ifscs,id',
                'vat_no'=>'nullable|max:191',
                'cst_no'=>'nullable|max:191',
                'account_no'=>'nullable|numeric',
                'e_invoice_applicable'=>'required|in:Y,N'
            ];
        }

        if($this->ac_sub_group_id > 0 ){
            $account_group = AccountSubGroup::findOrFail($this->ac_sub_group_id);
            if($account_group && $account_group['sub_group_name'] =='Trade Receivables' ){
                $rules +=['credit_limit' =>'required'];
                $rules +=['pincode' =>'required'];
                $rules += ['party_cat_id'=>'required'];
            }
        }
        else{
            $rules +=['credit_limit' =>'numeric|nullable'];
        }

        if($this->party_gst_status == 'Composition' || $this->party_gst_status == 'Registerd' ){
            $rules +=[
                'gst_no'=>'required_if:party_gst_status,Composition,Registerd'
            ];
        }
        return $rules;
    }

    public function save()
    {
        $account = Account::findOrNew($this->form_id);
        $account->fill($this->all());
        $old_ac_branch_ids = $account->account_branches()->pluck('id')->toArray();
        $acc_branches = new Collection();

        foreach($this->branches as $acbranch){
            $ac_branch = new AccountBranch();
            $ac_branch->branch_id = $acbranch;
            $acc_branches->add($ac_branch);
        }

        $new_ac_branch_ids = $acc_branches->pluck('id')->toArray();
        $detach = array_diff($old_ac_branch_ids,$new_ac_branch_ids);

        $account_yearly = AccountYearly::firstOrNew(['ac_id'=>$this->form_id]);
        if($this->type =='party'){
            $account_detail = AccountDetail::firstOrNew(['ac_id'=>$this->form_id]);
            $account_detail->fill($this->all());
        }

        DB::beginTransaction();
            $account->save();
            if($this->form_id == 0){
                $account->ac_code = 'AC'.$account['id'];
                $account->save();
            }
            if($this->type =='party'){
                $local_outside = $account_detail->getLocalorOutsideValue();
                $account_yearly->local_outside = $local_outside;
                $account_detail->ac_id = $account['id'];
                $account_detail->save();
            }
            $account_yearly->ac_id = $account['id'];
            $account_yearly->save();
            $account->account_branches()->saveMany($acc_branches);
            AccountBranch::whereIn('id',$detach)->delete();
        DB::commit();

        return reply(true,[
            'account' =>$account
        ]);
    }
}
