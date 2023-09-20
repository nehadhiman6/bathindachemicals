
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import PartyCategorySelect from '@/Pages/ProjectComponents/SelectComponents/PartyCategorySelect.vue';
    import RateDifferenceDetail from '@/Pages/ProjectComponents/Masters/RateDifference/RateDifferenceDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','close_qty','default_packing','copy','readonly']);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
            form_id:0,
            item_id:'',
            party_cat_id:'',
            wef_date:BCL.today,
            date_applicable_on:'',
            uid:Utilities.getRandomNumber(),
            rate_diff_details:[]
    }));

    const getDetail = () => {
        return {
            id:0,
            rate_diff_id:'',
            rate_from:'',
            rate_to:'',
            rate_diff:'',
            random:Utilities.getRandomNumber()
        }
    }
    const data = reactive({
        create_url:'rate-differences',
        itemInitials:[],
        itemSelected:[],
        categoryInitials:[],
        categorySelected:[],
        show:true,
    });
    const pageTitle = computed(() => props.form_id > 0 && props.copy == false? 'Update  Ltr Tin Difference':'Add Ltr Tin Difference');

    onMounted(() => {
        if(props.form_id > 0){
            getRateDifference();
        }
        // else{
        //     setDefaultParameters();
        // }
        if(form.rate_diff_details.length == 0){
            form.rate_diff_details.push(getDetail());
        }
    });

    // const setDefaultParameters = () =>{
    //     if(props.default_packing){
    //         data.packingInitials=[{'id':props.default_packing.id,'text':props.default_packing.name}];
    //         data.packingSelected=[props.default_packing.id];
    //         Utilities.refreshComponent(data,'show');
    //     }
    // }

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
    const getRateDifference = () =>{
        axios.get(base_url.value+'/rate-differences/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let rate_diff = response.data.rate_difference;
                if(rate_diff.item){
                    data.itemInitials = [{'text':rate_diff.item.item_name,'id':rate_diff.item.id}];
                    data.itemSelected = [rate_diff.item.id];
                }
                if(rate_diff.party_category){
                    data.categoryInitials = [{'text':rate_diff.party_category.name,'id':rate_diff.party_category.id}];
                    data.categorySelected = [rate_diff.party_category.id];
                }
                copyProperties(rate_diff,form);

                form.rate_diff_details = [];
                rate_diff.rate_diff_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    if(props.copy == true){
                        detail.id= 0;
                    }
                    form.rate_diff_details.push(detail);
                });
                form.uid = Utilities.getRandomNumber();
                refreshComponent(data,'show');
            }
            if(props.copy == false){
                form.form_id  = props.form_id;
            }
        })
        .catch(function(error){
            console.log(error);
        });
    }
    const changeInDetails = (type= 'add',index) => {
        if(type =='remove'){
            form.rate_diff_details.splice(index,1);
        }
        else{
            form.rate_diff_details.push(getDetail());
        }
    }

    // const isDisabled = () =>{
    //     return false;
    // }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'wef_date':
                return props.readonly;
            case 'item_id':
                return props.readonly;
            case 'date_applicable_on':
                return props.readonly;
            case 'party_cat_id':
                return props.readonly;
            default:
                return false;
        }
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3">
                <div class="mb-1">
                    <InputLabel for="item_id" value="WEF Date" />
                    <date-picker  v-model="form.wef_date" :error="form.errors.get('wef_date') ? true :false" :disabled="isDisabled('wef_date')"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('wef_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-2/3 lg:w-2/3">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="item_id" value="Item" />
                    <item-select     v-model="form.item_id" index="-2" :key="-2" :error="form.errors.get('item_id') ? true :false"
                        :initials="data.itemInitials"
                        :selected="data.itemSelected"
                        :disabled="isDisabled('item_id')"
                    ></item-select>
                    <InputError class="mt-2" :message="form.errors.get('item_id')" />
                </div>
            </div>

        </div>

        <div class="flex flex-wrap items-end -mx-3">
              <div class="w-full max-w-full px-3  md:w-1/3 lg:w-1/3">
                <div class="mb-1">
                    <InputLabel for="date_applicable_on" value="Applicable On" />
                     <SelectInput  v-model="form.date_applicable_on" :options="[
                        {'id':'C','text':'Contract Date'},
                        {'id':'D','text':'Dispatched Date'},
                    ]" :error="form.errors.get('store_item') ? true :false" :disabled="isDisabled('date_applicable_on')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('date_applicable_on')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-2/3 lg:w-2/3">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="party_cat_id" value="Party Category" />
                    <party-category-select  v-model="form.party_cat_id" index="-2" :key="-2" :error="form.errors.get('party_cat_id') ? true :false"
                        :initials="data.categoryInitials"
                        :selected="data.categorySelected"
                        :disabled="isDisabled('party_cat_id')"
                    ></party-category-select>
                    <InputError class="mt-2" :message="form.errors.get('party_cat_id')" />
                </div>
            </div>

        </div>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold  text-blue px-3">Rate Difference Details</legend>
                <TableLayout>
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                            <th>Rate From</th>
                            <th>Rate To</th>
                            <th>Rate Difference</th>
                            <th v-if="isDisabled('button')"></th>
                        </tr>
                    </template>
                    <rate-difference-detail v-for="(rate_diff_detail,index) in form.rate_diff_details" :key="rate_diff_detail.random"
                        :detail = "rate_diff_detail"
                        :index = "index"
                        :form="form"
                        :readonly="props.readonly"
                        @changeInDetails="changeInDetails"
                    >
                    </rate-difference-detail>
                </TableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails" >  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('sale_contract_details')" />
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
