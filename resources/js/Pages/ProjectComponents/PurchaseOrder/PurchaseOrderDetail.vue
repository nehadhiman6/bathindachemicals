<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';


    const props = defineProps(['detail','index','form','create_url','readonly']);
    const emit = defineEmits(['changeInDetails']);

    const data = reactive({
        itemInitials:[],itemSelected:[],
        itemUnitInitials:[],itemUnitSelected:[],
        packingInitials:[],packingSelected:[],secondary_unit:null,
        show:true
    });



    onMounted(() => {
        if(props.detail.item){
            data.itemInitials = [{'id':props.detail.item.id,'text':props.detail.item.item_name}];
            data.itemSelected = [props.detail.item.id];

        }
        if(props.detail.item_unit){
            data.itemUnitInitials = [{'id':props.detail.item_unit.id,'text':props.detail.item_unit.unit_name}];
            data.itemUnitSelected = [props.detail.item_unit.id];
        }

        refreshComponent(data,'show');
        //   calc();
    });
    const updateItem = (id, index, item) =>{
        if(item && item.hsn_code){
            props.detail.hsn_code = item.hsn_code;
            props.detail.item = item;
        }
        // calc();
    }
    // const calc=()=>{
    //   emit('calc');
    // }


    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_id':
                return props.readonly;
            case 'unit_id':
                return props.readonly;
            case 'qty_from':
                return props.readonly;
            case 'qty_to':
                return props.readonly;
            case 'rate':
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
        <td style="min-width: 150px;">
            <TextInput v-model="props.detail.qty_from" :disabled ="isDisabled('qty_from')"   :error="props.form.errors.get('details.'+props.index+'.qty_from') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.qty_from')" :message="props.form.errors.get('details.'+props.index+'.qty_from')" />
        </td>
        <td style="min-width: 150px;">
            <TextInput v-model="props.detail.qty_to" :disabled ="isDisabled('qty_to')"   :error="props.form.errors.get('details.'+props.index+'.qty_to') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.qty_to')" :message="props.form.errors.get('details.'+props.index+'.qty_to')" />
        </td>
        <td  style="min-width: 150px;">
           <TextInput v-model="props.detail.rate" :disabled ="isDisabled('rate')" :error="props.form.errors.get('details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.rate')" :message="props.form.errors.get('details.'+props.index+'.rate')" />
        </td>
        <td>
             <i v-if="isDisabled('remove')" class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
