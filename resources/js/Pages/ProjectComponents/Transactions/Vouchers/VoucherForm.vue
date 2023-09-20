
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import PayTermSelect from '@/Pages/ProjectComponents/SelectComponents/PayTermSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import VoucherDetail from '@/Pages/ProjectComponents/Transactions/Vouchers/VoucherDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','voucher_types']);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
        form_id:0,
        branch_id:'',
        vcode:'',
        voucher_no:'',
        voucher_no_part:'',
        voucher_date:'',
        voucher_type:'',
        tr_type:'',
        acid_other:'',
        acid_tds:'',
        approved:'N',
        approved_by:'',
        approved_at:'',
        total_debit:'',
        total_credit:'',
        total_diff:'',
        uid:Utilities.getRandomNumber(),
        voucher_details:[]
    }));
    const data = reactive({
        create_url:'vouchers',
        otherAcountInitials:[],
        otherAcountSelected:[],
        tdsAcountInitials:[],
        tdsAcountSelected:[],
        show:true,
        cash_in_hand:'',
        account_label:"",
        valid_record:true
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Voucher':'Add Voucher');
    const tr_Types = computed(() =>{
        var trType = [];

        if(form.voucher_type  == 'I'){
            trType = [];
            trType.push({'id': 'C', 'text': 'Complete'});
            trType.push({'id': 'T', 'text': 'TDS Only'});
            trType.push({'id': 'A', 'text': 'Advance'});
        }
        else if(form.voucher_type == "P" || form.voucher_type == "R"){
            trType = [];
            trType.push({'id': 'B', 'text': 'Bank'});
            trType.push({'id': 'C', 'text': 'Cash'});
        }
        else if(form.voucher_type == "T"){
            trType = [];
            trType.push({'key': 'D', 'text': 'Debit'});
            trType.push({'key': 'C', 'text': 'Credit'});
        }
        return trType;
    });

    const isCashVchr = computed(() =>{
        return form.tr_type == 'C' && (form.voucher_type == 'R' || form.voucher_type == 'P') ? 'Y':'N';
    });


    onMounted(() => {
        if(props.form_id > 0){
            getVoucher();
        } else {
            form.voucher_date = BCL.today;
        }
        if(form.voucher_details.length == 0){
            form.voucher_details.push(getDetail());
        }
        getCashInHandAmount();
    });

    const getDetail = () => {
    return {
        id:0,
        bill_wise:'N',
        voucher_id:'',
        drcr:'',
        sno:'',
        ac_id:'',
        amount:'',
        disc_tds_amt:'',
        part:'',
        cheque_no:'',
        acc_bal:'',
        acname:'',
        weight:'',
        section_id:'',
        rate:'',
        tds_on:'',
        tds_adj:'',
        tds_amt:'',
        acid_tds:'',
        tds_part:'',
        beneficiary_name:'',
        bills:[],
        random:Utilities.getRandomNumber()
    }
}


    function getBillDetail(){
        return {
            id: 0,
            branch_id:0,
            trans_date:'',
            dr_cr:'',
            amount:'',
            ref:'',
            ref_date:'',
            ref_type:'N',
            ref_key:'',
            random:Utilities.getRandomNumber(),
        }
    }

    const calc = () =>{
        data.valid_record = true;
        if(form.voucher_type == 'I')
            data.account_label =  'Nature of Account';
        if(form.voucher_type == 'T') {
            data.account_label = form.tr_type == 'C' ? 'Credit Account' : 'Debit Account';
        }
        if(form.tr_type == 'B')
            data.account_label =  'Bank Account';
        if(form.tr_type == 'C')
            data.account_label =  'Cash Account';
        var cr_total = 0;
        var dr_total = 0;
        var total_diff = 0;
        var total_d = 0;
        var total_c = 0;
        var same_tot = (form.voucher_type == 'T' || (form.tr_type == 'B' && (form.voucher_type == 'R' || form.voucher_type == 'P'))) ? 'Y':'N';
        form.voucher_details.forEach(function(voucher){
            if(voucher.drcr == 'D'){
                dr_total += Utilities.round(voucher.amount);
            }
            else{
                cr_total += Utilities.round(voucher.amount);
            }
            if(voucher.drcr == 'D' || same_tot == 'Y'){
                total_d += Utilities.round(voucher.amount);
            }
            if(voucher.drcr == 'C' || same_tot == 'Y'){
                total_c += Utilities.round(voucher.amount);
            }


            //CHECK VALID RECORD
            if(voucher.ac_id > 0 && (voucher.amount*1) > 0 && voucher.drcr !=0 && voucher.bill_wise == 'Y'){
                var billcredit = 0;
                var billdebit = 0;
                var billdrcr = '';
                var netAdjustment = '';
                var crdr = voucher.drcr;
                voucher.bills.forEach(function(bill){
                    if((bill.amount*1) > 0 && bill.dr_cr != 0){
                        if(bill.dr_cr == 'D'){
                            billdebit += (bill.amount*1);
                        }
                        else{
                            billcredit += (bill.amount*1);
                        }
                    }
                });
                if(billcredit*1 > billdebit*1){
                    billdrcr = "C";
                    netAdjustment = (billcredit* 1 ) - billdebit * 1;
                }
                else {
                    billdrcr = "D";
                    netAdjustment = (billdebit * 1 ) - billcredit * 1;
                }
                if(netAdjustment != voucher.amount || billdrcr != crdr){
                    data.valid_record = false;
                }
            }

        });
        var value = 'Dr';
        if(cr_total > dr_total){
            value = 'Cr';
        }
        else{
            value = 'Dr'
        }
        var total_diff = Utilities.round(dr_total) - Utilities.round(cr_total);
        form.total_diff= Utilities.formatNumber(Math.abs(total_diff),2)+' '+value;
        form.total_debit =  Utilities.round(total_d);
        form.total_credit =  Utilities.round(total_c);
    }


    const submitForm = () =>{
        var self = this;
        calc();
        // if(data.valid_record == false){
        //     Utilities.showPopMessage('Incorrect Bills Data','Please Check Bills','warning');
        //     return;
        // }
        form['postForm'](data.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getVoucher = () =>{
        axios.get(base_url.value+'/vouchers/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let voucher = response.data.voucher;
                if(voucher.account_tds){
                    data.tdsAcountInitials = [{'text':voucher.account_tds.name,'id':voucher.account_tds.id}];
                    data.tdsAcountSelected = [voucher.account_tds.id];
                }
                if(voucher.account_other){
                    data.otherAcountInitials = [{'text':voucher.account_other.name,'id':voucher.account_other.id}];
                    data.otherAcountSelected = [voucher.account_other.id];
                }

                copyProperties(voucher,form);

                form.voucher_details = [];
                voucher.voucher_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    detail.account = element.account;
                    detail.section = element.section;
                     if(element.hasOwnProperty('bills')) {
                        detail.bills = [];
                        element.bills.forEach(function(bill){
                            var blank_bill_detail = getBillDetail();
                            Utilities.copyProperties(bill, blank_bill_detail);
                            detail.bills.push(blank_bill_detail);
                        });
                    }
                    form.voucher_details.push(detail);
                });
                refreshComponent(data,'show');
                refreshComponent(data,'showPacking');
                calc();
            }
            form.form_id  = props.form_id;
            form.uid = Utilities.getRandomNumber();
        })
        .catch(function(error){
            console.log(error);
        });
    }
    const changeInDetails = (type= 'add',index) => {
        calc();
        if(type =='remove'){
            form.voucher_details.splice(index,1);
        }
        else{
            form.voucher_details.push(getDetail());
        }
    }


    const isDisabled = () =>{
        return false;
    }

    const  getCashInHandAmount =()=>{
        axios.get('get-amount/',{params:{'voucher_date': form.voucher_date}})
        .then(function(response){
            data.cash_in_hand = response.data
        });
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-2">
            <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6">
                <div class="mb-1">
                    <InputLabel for="voucher_date" value="Voucher Date" />
                    <date-picker v-model="form.voucher_date" :error="form.errors.get('voucher_date') ? true :false" @change="getCashInHandAmount"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('voucher_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6" v-if="form.form_id >0">
                <div class="mb-1">
                    <InputLabel for="voucher_no" value="Voucher No." />
                    <TextInput v-model="form.voucher_no" type="text" disabled  :error="form.errors.get('voucher_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('voucher_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6">
               <div class="mb-1">
                    <InputLabel for="voucher_type" value="Voucher Type" />
                    <SelectInput  v-model="form.voucher_type" :options="props.voucher_types" :error="form.errors.get('voucher_type') ? true :false" @change="form.tr_type = '';calc();"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('voucher_type')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6" v-if="form.voucher_type != 'J'">
                <div class="mb-1">
                    <InputLabel for="tr_type" value="TR Type" />
                    <SelectInput   v-model="form.tr_type" :options="tr_Types" :error="form.errors.get('tr_type') ? true :false" @change="calc" > </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('tr_type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6">
               <div class="mb-1">
                    <InputLabel for="voucher_type" value="CASH in Hand" />
                    <TextInput v-model="data.cash_in_hand" type="text" disabled />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3" >
            <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2" v-show="form.voucher_type != 'J' && isCashVchr == 'N'">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="acid_other" :value="data.account_label" />
                    <account-select  v-model="form.acid_other" :type="['P','R'].includes(form.voucher_type) ? 'bank':'all'" :key="-1" index="acid_other" :initials="data.otherAcountInitials" :selected="data.otherAcountSelected" @updateAccount="calc"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid_other')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2" v-show="form.voucher_type == 'I'" >
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="acid_tds" value="TDS Account" />
                    <account-select  v-model="form.acid_tds" :type="'all'" :key="-1" index="acid_tds" :initials="data.tdsAcountInitials" :selected="data.tdsAcountSelected" @updateAccount="calc" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid_tds')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3" >
            <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3">
                <div class="mb-1">
                    <InputLabel for="total_debit" value="Total Debit" />
                    <TextInput v-model="form.total_debit" type="text" disabled  :error="form.errors.get('total_debit') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('total_debit')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3">
                <div class="mb-1">
                    <InputLabel for="total_credit" value="Total credit" />
                    <TextInput v-model="form.total_credit" type="text" disabled  :error="form.errors.get('total_credit') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('total_credit')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3">
                <div class="mb-1">
                    <InputLabel for="total_diff" value="Total Difference" />
                    <TextInput v-model="form.total_diff" type="text" disabled  :error="form.errors.get('total_diff') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('total_diff')" />
                </div>
            </div>
        </div>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Sale Contract Details</legend>
                <tableLayout >
                    <template #thead>
                        <tr>
                            <th>S.no.</th>
                            <th>A/C Name</th>
                            <!-- <th v-if="voucher_type =='J' ">Voucher Type</th> -->
                            <th>Balance</th>
                            <th>Debit/Credit</th>
                            <th>Narration</th>
                            <th>Cheque No</th>
                            <th v-if="form.voucher_type=='P' && form.tr_type=='B'">Beneficiary Name</th>
                            <th>Weight</th>
                            <th style="width:130px;">Amount</th>
                            <th  v-if="form.voucher_type=='I'" >TDS Amount</th>
                            <th></th>
                        </tr>
                    </template>
                    <voucher-detail v-for="(voucher,index) in form.voucher_details" :key="voucher.random"
                        :detail = "voucher"
                        :index = "index"
                        :form="form"
                        @changeInDetails="changeInDetails"
                        @calc="calc"
                    >
                    </voucher-detail>

                </tableLayout>
                <div class="mt-3">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('voucher_details')" />
        </fieldset>

        <div class="flex flex-wrap items-end -mx-3">
           <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/3">
             <div class="mb-1">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
