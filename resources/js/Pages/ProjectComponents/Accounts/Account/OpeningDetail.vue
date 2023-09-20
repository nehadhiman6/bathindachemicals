<script setup>
    import { ref } from 'vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import BillForm from '@/Pages/ProjectComponents/Bill/BillForm.vue';
    const props = defineProps([
        'form','opening','account','index'
    ]);
    const showBill = ref(false);

    const updateStatus=()=>{
        showBill.value = false;
    }
</script>

<template>
    <tr >
        <td v-text ="index+1"></td>
        <td v-text ="opening.branch_name"></td>
        <td><SelectInput v-model="opening.dr_cr" :options ="[{'id':'C','text':'Credit'},{'id':'D','text':'Debit'}]" :error="form.errors.get('openings.'+index+'.dr_cr') ? true :false" ></SelectInput></td>
        <td><TextInput v-model="opening.opening_amount" type="text" required  :error="form.errors.get('openings.'+index+'.opening_amount') ? true :false" /></td>
        <td v-if="props.account.bill_wise == 'Y' && opening.opening_amount > 0 && opening.dr_cr !=''" >
            <span class="ml-2">
                <ButtonComp type="save" size="sm" @click="showBill= true">Bill</ButtonComp>
            </span>
        </td>
    </tr>
    <tr  v-if="form.errors.get('openings.'+index+'.amountmismatch')">
        <td colspan="5"><InputError class="mt-2" :message="form.errors.get('openings.'+index+'.amountmismatch')" /></td>
    </tr>
    <bill-form
        v-if="showBill"
        :form="form"
        :entry="opening"
        :bills="opening.bills"
        :index ="index"
        :account-name ="account.name"
        :amount = "opening.opening_amount"
        :debit-credit = "opening.dr_cr"
        :key = "opening.random"
        @updateStatus = "updateStatus"
       >
    </bill-form>

</template>
