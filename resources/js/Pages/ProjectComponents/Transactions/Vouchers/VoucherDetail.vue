<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {ref,computed,onMounted,reactive,watch} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import InputError from '@/Components/InputError.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import BillForm from '@/Pages/ProjectComponents/Bill/BillForm.vue';
    import TdsVoucherDetail from './TdsVoucherDetail.vue';
    import IconButton from '@/Pages/CustomComponents/Buttons/IconButton.vue';
    const props = defineProps(['detail','index','form']);
    const emit = defineEmits(['changeInDetails','calc']);
    const data = reactive({accountInitials:[],accountSelected:[],show:true,bill_wise:false,showTdsDetail:false});
    const showBill = ref(false);

    const updateStatus=()=>{
       calc();
        showBill.value = false;
    }

    const cancelStatus=()=>{
        showBill.value = false;
    }

    onMounted(() => {
        if(props.detail.account){
            data.accountInitials = [{'id':props.detail.account.id,'text':props.detail.account.name}];
            data.accountSelected = [props.detail.account.id];
            props.detail.acname = props.detail.account.name;
            props.detail.bill_wise = props.detail.account.bill_wise;
        }
        refreshComponent(data,'show');
    });

    const calc =()=>{
        emit('calc');
    }
    const setAccountBalance = () =>{
        axios.get('get-account-balance',{ params: {ac_id: props.detail.ac_id }})
        .then(function(response){
            props.detail.acc_bal = response.data.balance;
        })
        .catch(function(error){
            console.log('voucher form:', error);
        });
    }

    const updateAccount=(id,index,account)=>{
        // console.log('i amherer',account);
        props.detail.acname = account.name;
        props.detail.bill_wise = account.bill_wise;
        if(props.form.voucher_type=='P' && props.form.tr_type=='B'){
            props.detail.beneficiary_name = account.beneficiary_name;
        }
        setAccountBalance();
    }

    const hideConversionModal = () =>{
        data.showTdsDetail = false;
    }

    watch(props.form,(newValue,oldValue) => {
        console.log(newValue);
        if(newValue.voucher_type == "R"){
            props.detail.drcr = 'C' ;
        }
        else if(newValue.voucher_type == "P"){
            props.detail.drcr = 'D' ;
        }
        // else if(newValue.vchr_type == "Interest"){
        //     if(newValue.tr_type == "A") {
        //         self.entry.drcr = 'D' ;
        //     } else {
        //         self.entry.drcr = 'C' ;
        //     }
        // }
        else if(newValue.voucher_type == "T"){
            if(newValue.tr_type == "Debit")
                props.detail.drcr = 'C' ;
            else if(newValue.tr_type == "Credit")
                props.detail.drcr = 'D' ;
            else
                props.detail.drcr = 0 ;
        }
    })

</script>

<template>
    <tr>
        <td v-text="props.index +1">
        </td>
        <td  v-if="data.show" style=" max-width: 200px;">
            <account-select :key="props.detail.random" v-model="props.detail.ac_id" :index="'accounts_'+props.detail.random"
                :initials = "data.accountInitials"
                :selected = "data.accountSelected"
                :type="'all'"
                @updateAccount ="updateAccount"
                :error="form.errors.get('vouchers.'+props.index+'.ac_id') ? true :false"
            >
            </account-select>
            <InputError class="mt-1 "  v-if="form.errors.get('voucher_details.'+props.index+'.item_id')" :message="form.errors.get('voucher_details.'+props.index+'.item_id')" />
        </td>
        <td>
            <TextInput v-model="props.detail.acc_bal" :disabled="true" :error="form.errors.get('voucher_details.'+props.index+'.acc_bal') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.acc_bal')" :message="form.errors.get('voucher_details.'+props.index+'.acc_bal')" />
        </td>
         <td>
              <SelectInput v-model="props.detail.drcr" :disabled="props.form.voucher_type != 'J'" :error="form.errors.get('voucher_details.'+props.index+'.drcr') ? true :false" :options ="[{'id':'C','text':'Credit'},{'id':'D','text':'Debit'}]" @change="calc" ></SelectInput>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.drcr')" :message="form.errors.get('voucher_details.'+props.index+'.drcr')" />
        </td>
         <td>
            <TextInput v-model="props.detail.part" :error="form.errors.get('voucher_details.'+props.index+'.part') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.part')" :message="form.errors.get('voucher_details.'+props.index+'.part')" @blur="calc"/>
        </td>
        <td>
           <TextInput v-model="props.detail.cheque_no" :error="form.errors.get('voucher_details.'+props.index+'.cheque_no') ? true :false" :disabled="(props.form.vchr_type=='Payment') && props.form.tr_type=='C'"/>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.cheque_no')" :message="form.errors.get('voucher_details.'+props.index+'.cheque_no')"  @blur="calc"/>
        </td>
        <td v-if="props.form.voucher_type=='P' && props.form.tr_type=='B'">
           <TextInput v-model="props.detail.beneficiary_name" :error="form.errors.get('voucher_details.'+props.index+'.beneficiary_name') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.beneficiary_name')" :message="form.errors.get('voucher_details.'+props.index+'.beneficiary_name')"  @blur="calc"/>
        </td>
        <td>
           <TextInput v-model="props.detail.weight" :error="form.errors.get('voucher_details.'+props.index+'.weight') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.weight')" :message="form.errors.get('voucher_details.'+props.index+'.weight')"/>
        </td>

        <td>
            <TextInput v-model="props.detail.amount" :error="form.errors.get('voucher_details.'+props.index+'.amount') ? true :false"  @blur="calc()"/>
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.amount')" :message="form.errors.get('voucher_details.'+props.index+'.amount')" />
        </td>
         <td v-if="form.voucher_type=='I'">
            <TextInput v-model="props.detail.disc_tds_amt" :error="form.errors.get('voucher_details.'+props.index+'.disc_tds_amt') ? true :false" @blur="calc()" />
            <InputError class="mt-1"  v-if="form.errors.get('voucher_details.'+props.index+'.disc_tds_amt')" :message="form.errors.get('voucher_details.'+props.index+'.disc_tds_amt')" />
        </td>
        <td>
            <span class="ml-2">
                <ButtonComp type="save" size="sm" @click="showBill= true" v-if="detail.ac_id >0 && props.detail.amount > 0 && detail.bill_wise =='Y'">Bill</ButtonComp>
                <IconButton @buttonClicked="data.showTdsDetail = true" icon="fa fa-plus-square">Tds Details</IconButton>

            </span>
            <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
     <bill-form
        v-if="showBill"
        :form="form"
        :entry="props.detail"
        :bills="props.detail.bills"
        :index ="index"
        :account-name ="props.detail.acname"
        :amount = "props.detail.amount"
        :debit-credit = "props.detail.drcr"
        :key = "props.detail.random"
        @updateStatus = "updateStatus"
        @cancelStatus = "cancelStatus"
       >
    </bill-form>

    <tds-voucher-detail :entry="props.detail" :index ="index" :form="form" :show="true" v-if="data.showTdsDetail" @submitForm="hideConversionModal"></tds-voucher-detail>

</template>
