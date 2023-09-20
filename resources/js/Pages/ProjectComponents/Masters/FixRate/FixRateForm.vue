
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import FixRateDetail from '@/Pages/ProjectComponents/Masters/FixRate/FixRateDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','close_qty','default_packing','readonly']);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
        form_id:0,
        item_id:'',
        wef_date:BCL.today,
        uid:Utilities.getRandomNumber(),
        fix_rate_details:[]
    }));

    const data = reactive({
        create_url:'fix-rates',
        itemInitials:[],
        itemSelected:[],
        show:true,
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Fix Rate':'Add Fix Rate');

    onMounted(() => {
        if(props.form_id > 0){
            getItemFreight();
        }
        if(form.fix_rate_details.length == 0){
            form.fix_rate_details.push(getDetail());
        }
    });

    const getDetail = () => {
    return {
        id:0,
        fix_rate_id:'',
        packing_id:'',
        item_id:'',
        rate:'',
        random:Utilities.getRandomNumber()
    }
}
    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getItemFreight = () =>{
        axios.get(base_url.value+'/fix-rates/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let fix_rate = response.data.fix_rate;
                if(fix_rate.item){
                    data.itemInitials = [{'text':fix_rate.item.item_name,'id':fix_rate.item.id}];
                    data.itemSelected = [fix_rate.item.id];
                }
                copyProperties(fix_rate,form);

                form.fix_rate_details = [];
                fix_rate.fix_rate_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    detail.packing = element.packing;
                    detail.item = element.item;
                    form.fix_rate_details.push(detail);
                });
                refreshComponent(data,'show');
            }
            form.form_id  = props.form_id;
            form.uid = Utilities.getRandomNumber();
        })
        .catch(function(error){
            console.log(error);
        });
    }
    const changeInDetails = (type= 'add',index) => {
        if(type =='remove'){
            form.fix_rate_details.splice(index,1);
        }
        else{
            form.fix_rate_details.push(getDetail());
        }
    }
    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'wef_date':
                return true;
            default:
                return false;
        }
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="wef_date" value="WEF Date" />
                    <date-picker  v-model="form.wef_date" :disabled="isDisabled('wef_date')" :error="form.errors.get('wef_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('wef_date')" />
                </div>
            </div>
        </div>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Fix Rate Details</legend>
                <tableLayout >
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                            <th>Item</th>
                            <th>Packing</th>
                            <th>Rate</th>
                            <th v-if="isDisabled('button')"></th>
                        </tr>
                    </template>
                    <fix-rate-detail v-for="(fix_rate,index) in form.fix_rate_details" :key="fix_rate.random"
                        :detail = "fix_rate"
                        :index = "index"
                        :form="form"
                        :readonly = "props.readonly"
                        @changeInDetails="changeInDetails"
                    >
                    </fix-rate-detail>

                </tableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('fix_rate_details')" />
        </fieldset>

        <div class="flex flex-wrap items-end -mx-3">
           <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
             <div class="mb-1">
                <ButtonComp @buttonClicked="submitForm" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
