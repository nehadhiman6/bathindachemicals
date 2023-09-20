
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import PartyCategorySelect from '@/Pages/ProjectComponents/SelectComponents/PartyCategorySelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import PackingFormulaDetail from '@/Pages/ProjectComponents/Masters/PackingFormula/PackingFormulaDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','copy','default_packing','readonly']);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
            form_id:0,
            item_id:'',
            party_cat_id:'',
            packing_id:'',
            date_applicable_on:'',  //C,D /CONTRACT DISPATCHED
            wef_date:BCL.today,
            uid:Utilities.getRandomNumber(),
            packing_formula_details:[]
    }));

    const getDetail = () => {
        return {
            id:0,
            pack_formula_id:'',
            brand_id:'',
            packing_id:'',
            conversion:'',
            tin_cost:'',
            extra:'',
            divisor:'',
            muliplier:'',
            packing_cost:'',
            freight:'',
            brand:null,
            packing:null,
            weight:'',
            random:Utilities.getRandomNumber()
        }
    }
    const data = reactive({
        create_url:'packing-formulas',
        itemInitials:[],
        itemSelected:[],
        categoryInitials:[],
        categorySelected:[],
        packingInitials:[],
        packingSelected:[],
        show:true,
    });
    const pageTitle = computed(() => props.form_id > 0 && props.copy == false? 'Update  Packing Formula':'Add Packing Formula');

    onMounted(() => {
        if(props.form_id > 0){
            getPackingFormula();
        }
        // else{
        //     setDefaultParameters();
        // }
        if(form.packing_formula_details.length == 0){
            form.packing_formula_details.push(getDetail());
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
    const getPackingFormula = () =>{
        axios.get(base_url.value+'/packing-formulas/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let packing_formula = response.data.packing_formula;
                if(packing_formula.item){
                    data.itemInitials = [{'text':packing_formula.item.item_name,'id':packing_formula.item.id}];
                    data.itemSelected = [packing_formula.item.id];
                }
                if(packing_formula.party_category){
                    data.categoryInitials = [{'text':packing_formula.party_category.name,'id':packing_formula.party_category.id}];
                    data.categorySelected = [packing_formula.party_category.id];
                }
                if(packing_formula.packing){
                    data.packingInitials = [{'text':packing_formula.packing.name,'id':packing_formula.packing.id}];
                    data.packingSelected = [packing_formula.packing.id];
                }
                copyProperties(packing_formula,form);

                form.packing_formula_details = [];
                packing_formula.packing_formula_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                     if(props.copy == true){
                        detail.id= 0;
                    }
                    detail.brand = element.brand;
                    detail.packing = element.packing;
                    form.packing_formula_details.push(detail);
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
            form.packing_formula_details.splice(index,1);
        }
        else{
            form.packing_formula_details.push(getDetail());
        }
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'wef_date':
                return props.readonly;
            case 'item_id':
                return props.readonly;
            case 'party_cat_id':
                return props.readonly;
            case 'packing_id':
                return props.readonly;
            case 'date_applicable_on':
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
                    <date-picker  v-model="form.wef_date" :disabled="isDisabled('wef_date')" :error="form.errors.get('wef_date') ? true :false"></date-picker>
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
            <div class="w-full max-w-full px-3  md:w-1/3 lg:w-1/3">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="packing_id" value="Packing" />
                    <packing-select  v-model="form.packing_id"
                        type="O"
                        index="-2" :key="-2" :error="form.errors.get('packing_id') ? true :false"
                        :initials="data.packingInitials"
                        :selected="data.packingSelected"
                        :disabled="isDisabled('packing_id')"
                    ></packing-select>
                    <InputError class="mt-2" :message="form.errors.get('packing_id')" />
                </div>
            </div>
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
        </div>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold  text-blue px-3">Packing Formula Details</legend>
                <TableLayout>
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                            <th>Brand</th>
                            <th>Packing</th>
                            <th>Weight</th>
                            <th>Conversion</th>
                            <th>Tin Cost</th>
                            <th>Extra</th>
                            <th>Divisor</th>
                            <th>Muliplier</th>
                            <th>Packing Cost</th>
                            <th>Freight</th>
                            <th v-if="isDisabled('button')">Action</th>
                        </tr>
                    </template>
                    <packing-formula-detail v-for="(packing_formula_detail,index) in form.packing_formula_details" :key="packing_formula_detail.random"
                        :detail = "packing_formula_detail"
                        :index = "index"
                        :form="form"
                         type="O"
                        :readonly="props.readonly"
                        @changeInDetails="changeInDetails"
                    >
                    </packing-formula-detail>
                </TableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
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
