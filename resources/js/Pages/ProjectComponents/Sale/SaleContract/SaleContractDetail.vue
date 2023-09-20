<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    const props = defineProps(['detail','index','form','close_qty','brokerage_parameters','readonly']);
    const emit = defineEmits(['changeInDetails']);
    const data = reactive({itemInitials:[],itemSelected:[],itemUnitInitials:[],itemUnitSelected:[],show:true,freight:false});
    onMounted(() => {
        console.log(props.detail.item);
        if(props.detail.item){
            data.itemInitials = [{'id':props.detail.item.id,'text':props.detail.item.item_name}];
            data.itemSelected = [props.detail.item.id];
        }
        if(props.detail.item_unit){
            data.itemUnitInitials = [{'id':props.detail.item_unit.id,'text':props.detail.item_unit.unit_name}];
            data.itemUnitSelected = [props.detail.item_unit.id];
        }
        refreshComponent(data,'show');
    });

    const updateItem = () =>{
        if(props.detail.item_id > 0 && props.form.ac_id >0){
            axios.post(base_url.value+'/item-freight',{
                'item_id': props.detail.item_id,
                'ac_id':props.form.ac_id
            })
            .then(function(response){
                console.log(response);
                if(response.data.item_freight){
                    data.freight = true;
                    props.detail.rate = response.data.item_freight.freight;
                }
                else{
                    data.freight = false;
                    props.detail.rate = '';

                }
                if(response.data.item){
                    props.detail.item = response.data.item;
                    if(props.form.broker_id > 0){
                        setBrokerRate(response.data.item);
                    }
                }
            })
            .catch(function(error){
                console.log(error);
            })
        }



    }

    const updateUnit = (id,index,unit) =>{
        props.detail.item_unit = unit;
        updateItem();
    }

    const setBrokerRate= (item)=>{
        if(props.index == 0){
            if(item.sub_group){
                if(item.sub_group.oil == "N"){
                    props.form.brokerage_rate = item.brokerage_per_unit;
                }
                else{
                    if(props.brokerage_parameters.length > 0){
                        if(props.form.packed_loose == 'packed'){
                            let brokerage_per_nag = props.brokerage_parameters.find(arr=> arr.para_name == 'brokerage_per_nag');
                            props.form.brokerage_rate = brokerage_per_nag.para_value;
                        }else if(props.form.packed_loose == 'loose' && props.detail.item_unit){
                            let unit_name =  props.detail.item_unit['unit_name'];
                            let unit_para = props.brokerage_parameters.find(arr=> arr.para_value == unit_name);
                            console.log("unit_para");
                            console.log(unit_para);
                            if(unit_para && unit_para['para_name'] == 'brokerage_per_quintal_unit'){
                                let brokerage_per_quintal = props.brokerage_parameters.find(arr=> arr.para_name == 'brokerage_per_quintal');
                                   console.log("brokerage_per_quintal");
                                    console.log(brokerage_per_quintal);
                                props.form.brokerage_rate = brokerage_per_quintal.para_value;
                            }
                            else if(unit_para && unit_para['para_name'] == 'brokerage_per_drum_unit'){
                                let brokerage_per_drum = props.brokerage_parameters.find(arr=> arr.para_name == 'brokerage_per_drum');
                                props.form.brokerage_rate = brokerage_per_drum.para_value;
                            }
                        }
                    }
                }
                // else if(props.form.packed_loose == 'packed' && props.brokerage_parameters){
                //     props.form.brokerage_rate = item.brokerage_per_unit;
                // }
                // else if(props.form.packed_loose == 'loose'  && props.brokerage_parameters){
                //       props.form.brokerage_rate = item.brokerage_per_unit;
                // }
            }
        }
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'remove':
                return props.readonly == false ? true :false;
            case 'item_id':
                return props.readonly;
            case 'unit_id':
                return props.readonly;
            case 'qty':
                return props.readonly;
            case 'rate':
                if(props.readonly == true || data.freight == true){
                    return true;
                }
            case 'bargain_price_diff':
                return props.readonly;
            case 'tolerance_per':
                if(props.readonly == true || props.form.packed_loose =='packed'){
                    return true;
                }
            case 'close_qty':
                return props.readonly;

            default:
                return false;
        }
    }
</script>

<template>
    <tr>
        <td v-text="props.index +1">
        </td>
        <td  v-if="data.show" style=" max-width: 200px;">
            <item-select :key="props.detail.random" v-model="props.detail.item_id" :index="'items_'+props.detail.random"
                :initials = "data.itemInitials"
                :selected = "data.itemSelected"
                :disabled="isDisabled('item_id')"
                @updateItem ="updateItem"
                :error="form.errors.get('sale_contract_details.'+props.index+'.item_id') ? true :false"
            >
            </item-select>
            <InputError class="mt-1 "  v-if="form.errors.get('sale_contract_details.'+props.index+'.item_id')" :message="form.errors.get('sale_contract_details.'+props.index+'.item_id')" />
        </td>
         <td  v-if="data.show" style=" max-width: 150px;">
            <item-unit-select :key="props.detail.random" v-model="props.detail.unit_id" :index="'items_unit'+props.detail.random"
                :initials = "data.itemUnitInitials"
                :selected = "data.itemUnitSelected"
                :disabled="isDisabled('unit_id')"
                @updateItemUnit ="updateUnit"
                :error="form.errors.get('sale_contract_details.'+props.index+'.unit_id') ? true :false"
            >
            </item-unit-select>
             <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.unit_id')" :message="form.errors.get('sale_contract_details.'+props.index+'.unit_id')" />

        </td>
        <td>
            <TextInput v-model="props.detail.qty" :disabled="isDisabled('qty')" :error="form.errors.get('sale_contract_details.'+props.index+'.qty') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.qty')" :message="form.errors.get('sale_contract_details.'+props.index+'.qty')" />
        </td>
         <td>
            <!-- :disabled="data.freight == true" -->
            <TextInput v-model="props.detail.rate" :disabled="isDisabled('rate')" :error="form.errors.get('sale_contract_details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.rate')" :message="form.errors.get('sale_contract_details.'+props.index+'.rate')" />
        </td>
         <td>
            <TextInput v-model="props.detail.bargain_price_diff" :disabled="isDisabled('bargain_price_diff')" :error="form.errors.get('sale_contract_details.'+props.index+'.bargain_price_diff') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.bargain_price_diff')" :message="form.errors.get('sale_contract_details.'+props.index+'.bargain_price_diff')" />
        </td>
        <!-- <td>
            <SelectInput v-model="props.detail.packed_loose" :error="form.errors.get('sale_contract_details.'+props.index+'.packed_loose') ? true :false" :options ="[{'id':'packed','text':'Packed'},{'id':'loose','text':'Loose'}]"  ></SelectInput>
            <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.packed_loose')" :message="form.errors.get('sale_contract_details.'+props.index+'.packed_loose')" />
        </td> -->
        <td>
           <TextInput v-model="props.detail.tolerance_per" :error="form.errors.get('sale_contract_details.'+props.index+'.tolerance_per') ? true :false" :disabled="isDisabled('tolerance_per')" />
        <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.tolerance_per')" :message="form.errors.get('sale_contract_details.'+props.index+'.tolerance_per')" />
        </td>

        <td v-if="props.close_qty">
            <TextInput v-model="props.detail.close_qty" :disabled="isDisabled('close_qty')" :error="form.errors.get('sale_contract_details.'+props.index+'.close_qty') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_contract_details.'+props.index+'.close_qty')" :message="form.errors.get('sale_contract_details.'+props.index+'.close_qty')" />
        </td>
        <td v-if="isDisabled('remove')">
             <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
