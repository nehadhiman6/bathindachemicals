<script setup>
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import BillReferenceKey from '@/Pages/ProjectComponents/SelectComponents/BillReferenceKey.vue';
    import globalMixin from '../../../globalMixin';
    import { onMounted,onUnmounted ,reactive,computed  } from 'vue';

    const { base_url,refreshComponent} = globalMixin();
    const data = reactive({'initials':[],'selected':[],'drawComponent':true});

    const props = defineProps([
        'form', 'parent', 'bill', 'bills', 'net', 'debit', 'credit', 'amount', 'drCr', 'entry','index','uId'
    ]);

    onMounted(() => {
        const { bill, index, initials, selected } = props;
        bill.sno = index;
            if (bill.ref_key !== '') {
                data.initials.push({ id: 0, text: bill.ref_key });
                data.selected.push(0);
                refreshComponent(data,'drawComponent');
            }
        });

    const creditDebit = computed(() => {
        return props.entry.dr_cr;
    });

    const amountSelected = computed(() => {
        return props.entry.amount;
    });

    const ignoreRef = computed(() => {
        let arr = [];
        props.bills.forEach((ele, index) => {
            if (index !== props.index && ele.ref_key !== '') {
                arr.push(ele.ref_key);
            }
        });
        return arr;
    });

    const ignoreVcode = computed(() => {
        if (props.form.form_id > 0) {
            return props.form.vcode;
        }
        return '';
    });

    const getRefUrl = ()=>{
        return base_url.value +'/bill-reference-keys/'+props.entry.ac_id +'/filtered';
    }

    const updateKey = (reference) => {
        props.bill.ref_key = reference.ref_key;
        const isValidFormat = moment(reference.ref_date, 'DD-MM-YYYY', true).isValid();
        if(isValidFormat == false) {
            props.bill.ref_date = moment(reference.ref_date, 'YYYY-MM-DD').format('DD-MM-YYYY');
        } else {
            props.bill.ref_date = reference.ref_date;
        }
        props.bill.ref = reference.ref;
        props.bill.amount = reference.amount;
        props.bill.dr_cr = reference.dr_cr == 'Dr' ? 'D' : 'C';
    };

    const changeRefType = () => {
        if(props.bill.ref_type == "Y") {
            props.bill.ref_key = "";
           refreshComponent(data,'drawComponent');
        }
    };

</script>
<template>
    <tr>
        <td>{{props.index+1}}</td>
        <td>
            <SelectInput v-model="bill.ref_type" :options ="[{'id':'Y','text':'New'},{'id':'N','text':'Old'}]"></SelectInput>
        </td>
        <td ><bill-reference-key v-if="data.drawComponent"
                :key="props.uId"
                :disabled="bill.ref_type != 'N'"
                :initials="data.initials"
                :selected="data.selected"
                :index="props.uId"
                :url="getRefUrl"
                :ignore-vcode="ignoreVcode.value"
                :ignore-ref="ignoreRef.value"
                @updateKey="updateKey"
            ></bill-reference-key>
        </td>
        <td>
              <TextInput v-model="bill.ref" type="text" :disabled="bill.ref_type == 'N'"/>
        </td>
        <td>
            <!-- <div inline-datepicker data-date="02/25/2022"></div> -->
            <date-picker v-model="bill.ref_date" :class-name="'picker_bill_det_'+index" :disabled="bill.ref_type == 'N'"></date-picker>
              <!-- <TextInput v-model="bill.ref_date" type="text" placeholder="DD-MM-YYYY" :disabled="bill.ref_type == 'N'"/> -->
        </td>
        <td>
            <TextInput v-model="bill.amount"  type="text" />
        </td>
        <td>
            <SelectInput v-model="bill.dr_cr" :options ="[{'id':'C','text':'Credit'},{'id':'D','text':'Debit'}]"></SelectInput>
        </td>
        <td><span v-if="index != 0" class="fe fe-x-circle" title="Remove Row" @click="removeEntryRow"></span></td>
    </tr>
</template>
