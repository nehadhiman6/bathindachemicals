<script setup>
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import BillDetail from '@/Pages/ProjectComponents/Bill/BillDetail.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import { onMounted,onUnmounted ,reactive,computed  } from 'vue';


    const emit = defineEmits(['updateStatus'])
    const props = defineProps([
        'form', 'entry', 'bills', 'index', 'amount', 'debitCredit','accountName'
    ]);
    const data = reactive({'show':false});
    onMounted(()=>{
        data.show = true;
        if(props.bills.length < 1){
            changeInEnteries();
        }
    });
    onUnmounted(() => {
        data.show = false;
    });

    const getDetail = ()=>{
        return {
            id: 0,
            trans_date:'',
            dr_cr:props.entry.dr_cr,
            amount:'',
            ref:'',
            ref_date:'',
            part:'',
            ref_type:'N',
            ref_key:'',
            random:Utilities.getRandomNumber(),
        }
    }
    const changeInEnteries = (action='add')=>{
        if(action == 'add'){
            props.bills.push(getDetail());
        }
    }

    const debit = computed(() => {
        let totalDebit = 0;
        if (props.bills && props.bills.length > 0) {
            props.bills.forEach(function (bill) {
                if (bill.dr_cr === 'D') {
                    totalDebit += bill.amount * 1;
                }
            });
        }
        return totalDebit;
    });

    const credit = computed(() => {
        let totalCredit = 0;
        if (props.bills && props.bills.length > 0) {
            props.bills.forEach(function (bill) {
                if (bill.dr_cr === 'C') {
                    totalCredit += bill.amount * 1;
                }
            });
        }
        return totalCredit;
    });

    const drCr = computed(() => {
        if (credit.value * 1 > debit.value * 1) {
            return 'Cr';
        } else {
            return 'Dr';
        }
    });

    const leftAdjustment = computed(() => {
            if(!isNaN(props.amount - netAdjustment.value)){
                if(props.amount - netAdjustment.value == props.amount && debit.value == 0 && credit.value == 0){
                    return props.amount;
                }
                if(drCr.value != props.debitCredit+'r' && props.amount - netAdjustment.value == 0){
                    return (props.amount) * -1;
                }
                return props.amount - netAdjustment.value
            }
            else{
                return '0';
            }
    });

    const status = computed(() => {
        if (
            netAdjustment.value === props.amount * 1 &&
            drCr.value === props.debitCredit + 'r'
        ) {
            return true;
        } else {
            return false;
        }
    });

    const netAdjustment = computed(() => {
       if (drCr.value == 'Cr') {
          return credit.value * 1 - debit.value * 1;
        } else {
          return debit.value * 1 - credit.value * 1;
        }
    });

    const submitForm = ()=>{
        if(status.value == false){
            alert('Net Adjustment is not equal to the amount');
            return;
        }
        else{
            emit('updateStatus');
            data.show = false;
        }
    }


</script>
<template>
    <Modal :show="data.show" max-width="5xl" @close="submitForm()">
       <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                Bills
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Party Name" />
                            <TextInput v-model="props.accountName" type="text" disabled />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Total Amount" />
                            <TextInput v-model="props.amount" type="text" disabled />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Total  Debit" />
                            <TextInput v-model="debit" type="text" disabled />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Total  Credit" />
                            <TextInput v-model="credit" type="text" disabled />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Net Adjustment" />
                            <TextInput v-model="netAdjustment" type="text" disabled />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Adjustment to be Done" />
                            <TextInput v-model="leftAdjustment" type="text" disabled />
                        </div>
                    </div>
                </div>

            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" width="100%">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th width="10%" scopescope="col" class="px-6 py-3">#</th>
                    <th scope="col" class="px-6 py-3">Old/New</th>
                    <th width="15%" scopescope="col" class="px-6 py-3">Ref Key</th>
                    <th width="15%" scope="col" class="px-6 py-3">Ref</th>
                    <th >Dated</th>
                    <th >Amount</th>
                    <th >Debit/Credit</th>
                </thead>
                <tbody>
                    <bill-detail v-for="(bill,ind) in props.bills" :key="bill.random"
                        :index = "ind"
                        :form= "form"
                        :parent-index = "index"
                        :bill= "bill"
                        :bills= "bills"
                        :net-adjustment = "netAdjustment"
                        :debit = "debit"
                        :credit = "credit"
                        :amount= "amount"
                        :dr-cr= "drCr"
                        :entry = "props.entry"
                    >
                    </bill-detail>
                </tbody>
                <tfoot>
                     <ButtonComp @buttonClicked="changeInEnteries" type="save">Add</ButtonComp>
                </tfoot>
            </table>
        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <div class="mt-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('cancelStatus')" type="cancel">Cancel</ButtonComp>
            </div>
        </div>

    </Modal>
</template>
