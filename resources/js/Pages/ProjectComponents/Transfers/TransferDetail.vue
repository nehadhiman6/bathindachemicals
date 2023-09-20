<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';


    const props = defineProps(['detail','index','form','create_url','readonly']);
    const emit = defineEmits(['changeInDetails','calc']);

    const data = reactive({
        itemInitials:[],itemSelected:[],
        itemUnitInitials:[],itemUnitSelected:[],
        packingInitials:[],packingSelected:[],secondary_unit:null,
        show:true
    });



    onMounted(() => {
        console.log('i am hereer',props.detail);
        if(props.detail.item){
            data.itemInitials = [{'id':props.detail.item.id,'text':props.detail.item.item_name}];
            data.itemSelected = [props.detail.item.id];

        }
        if(props.detail.item_unit){
            data.itemUnitInitials = [{'id':props.detail.item_unit.id,'text':props.detail.item_unit.unit_name}];
            data.itemUnitSelected = [props.detail.item_unit.id];
        }

        if(props.detail.packing){
            data.packingInitials = [{'id':props.detail.packing.id,'text':props.detail.packing.name}];
            data.packingSelected = [props.detail.packing.id];
        }
        refreshComponent(data,'show');
          calc();
    });
    const updateItem = (id, index, item) =>{
        if(item && item.hsn_code){
            props.detail.hsn_code = item.hsn_code;
            props.detail.item = item;
        }
        calc();
    }
    const calc=()=>{
      emit('calc');
    }


    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_id':
                if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }

            case 'unit_id':
               if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            case 'packing_id':
               if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            case 'qty':
                if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            case 'weight':
                if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            case 'rate_on':
                if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            case 'rate':
                if(props.readonly == true || props.form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            case 'rec_qty':
                return props.readonly;
            case 'rec_weight':
                return props.readonly;
            case 'amount':
                return true;
            case 'remove':
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
                :disabled ="isDisabled('item_id')"
                @updateItem="updateItem"
                :error="props.form.errors.get('details.'+props.index+'.item_id') ? true :false"
            >
            </item-select>
            <InputError class="mt-1 "  v-if="props.form.errors.get('details.'+props.index+'.item_id')" :message="props.form.errors.get('details.'+props.index+'.item_id')" />
        </td>
         <td  v-if="data.show" style="min-width:150px;max-width: 170px;">
            <item-unit-select :key="props.detail.random" v-model="props.detail.unit_id" :index="'items_unit'+props.detail.random"
                :initials = "data.itemUnitInitials"
                :selected = "data.itemUnitSelected"
                :disabled ="isDisabled('unit_id')"
                :error="props.form.errors.get('details.'+props.index+'.unit_id') ? true :false"
            >
            </item-unit-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.unit_id')" :message="props.form.errors.get('details.'+props.index+'.unit_id')" />

        </td>
         <td  v-if="data.show" style="min-width: 120px; max-width: 150px;">
            <packing-select :key="props.detail.random" v-model="props.detail.packing_id" :index="'items_unit'+props.detail.random"
                :initials = "data.packingInitials"
                :selected = "data.packingSelected"
                :disabled ="isDisabled('packing_id')"
                :error="props.form.errors.get('details.'+props.index+'.packing_id') ? true :false"
            >
            </packing-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.packing_id')" :message="props.form.errors.get('details.'+props.index+'.packing_id')" />
        </td>
        <td style="min-width: 150px;">
            <TextInput v-model="props.detail.qty" :disabled ="isDisabled('qty')"   @blur="calc" :error="props.form.errors.get('details.'+props.index+'.qty') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.qty')" :message="props.form.errors.get('details.'+props.index+'.qty')" />
        </td>
         <td  style="min-width: 150px;">
            <TextInput v-model="props.detail.weight" :disabled ="isDisabled('weight')" @blur="calc" :error="props.form.errors.get('details.'+props.index+'.weight') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.weight')" :message="props.form.errors.get('details.'+props.index+'.weight')" />
        </td>
        <td  style="min-width: 150px;">
           <TextInput v-model="props.detail.rate" :disabled ="isDisabled('rate')" @blur="calc" :error="props.form.errors.get('details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.rate')" :message="props.form.errors.get('details.'+props.index+'.rate')" />
        </td>
         <td  style="min-width: 150px;">
           <SelectInput  v-model="props.detail.rate_on" :disabled ="isDisabled('rate_on')"   @change="calc" :options="[{'id':'W','text':'Weight'},{'id':'Q','text':'Quantity'}]" :error="props.form.errors.get('sale_order_details.'+props.index+'.rate_on') ? true :false"> </SelectInput>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.bargain_price_diff')" :message="props.form.errors.get('details.'+props.index+'.rate_on')" />
        </td>
         <td  style="min-width: 150px;" v-if="props.form.status == 'R'">
           <TextInput v-model="props.detail.rec_qty"  :disabled ="isDisabled('rec_qty')" :error="props.form.errors.get('details.'+props.index+'.rec_qty') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.rec_qty')" :message="props.form.errors.get('details.'+props.index+'.rec_qty')" />
        </td>
         <td  style="min-width: 150px;" v-if="props.form.status == 'R'">
           <TextInput v-model="props.detail.rec_weight" :disabled ="isDisabled('rec_weight')" :error="props.form.errors.get('details.'+props.index+'.rec_weight') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.rec_weight')" :message="props.form.errors.get('details.'+props.index+'.rec_weight')" />
        </td>
        <td  style="min-width: 150px;">
            <TextInput v-model="props.detail.amount" :disabled ="isDisabled('amount')" :error="props.form.errors.get('details.'+props.index+'.amount') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.amount')" :message="props.form.errors.get('details.'+props.index+'.amount')" />
        </td>
        <td>
             <i v-if="isDisabled('remove')" class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
