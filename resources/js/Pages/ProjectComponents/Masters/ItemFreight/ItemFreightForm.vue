
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import ItemFreightDetail from '@/Pages/ProjectComponents/Masters/ItemFreight/ItemFreightDetail.vue';
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
        item_freight_details:[]
    }));

    const data = reactive({
        create_url:'item-freights',
        itemInitials:[],
        itemSelected:[],
        show:true,
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Item Freight':'Add Item Freight');

    onMounted(() => {
        if(props.form_id > 0){
            getItemFreight();
        }
        if(form.item_freight_details.length == 0){
            form.item_freight_details.push(getDetail());
        }
    });

    const getDetail = () => {
    return {
        id:0,
        item_freight_id:'',
        city_id:'',
        freight:'',
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
        axios.get(base_url.value+'/item-freights/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let item_freight = response.data.item_freight;
                if(item_freight.item){
                    data.itemInitials = [{'text':item_freight.item.item_name,'id':item_freight.item.id}];
                    data.itemSelected = [item_freight.item.id];
                }
                copyProperties(item_freight,form);

                form.item_freight_details = [];
                item_freight.item_freight_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    detail.city = element.city;
                    form.item_freight_details.push(detail);
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
            form.item_freight_details.splice(index,1);
        }
        else{
            form.item_freight_details.push(getDetail());
        }
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'wef_date':
                return true;
            case 'item_id':
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="wef_date" value="Contract Date" />
                    <date-picker :disabled="isDisabled('wef_date')" v-model="form.wef_date" :error="form.errors.get('wef_date') ? true :false" ></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('wef_date')" />
                </div>
            </div>
            <div :class="form.form_id >0 ? 'w-full max-w-full px-3 shrink-0 md:w-2/4 lg:w-2/4 md:flex-0':'w-full max-w-full px-3 shrink-0 md:w-3/4 lg:w-3/4 md:flex-0'">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="ac_id" value="Item" />
                    <item-select v-model="form.item_id" index="-2" :key="-2" :error="form.errors.get('item_id') ? true :false"
                        :initials="data.itemInitials"
                        :selected="data.itemSelected"
                        @updateAccount = "updateAccount"
                        :disabled="isDisabled('item_id')"
                    ></item-select>
                    <InputError class="mt-2" :message="form.errors.get('item_id')" />
                </div>
            </div>
        </div>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Sale Contract Details</legend>
                <tableLayout >
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                            <th >City</th>
                            <th>Freight</th>
                            <th v-if="isDisabled('button')"></th>
                        </tr>
                    </template>
                    <item-freight-detail v-for="(item_freight,index) in form.item_freight_details" :key="item_freight.random"
                        :detail = "item_freight"
                        :index = "index"
                        :form="form"
                        :readonly = "props.readonly"
                        @changeInDetails="changeInDetails"
                    >
                    </item-freight-detail>

                </tableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('item_freight_details')" />
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
