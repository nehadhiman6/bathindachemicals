
<script setup>
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import globalMixin from '../../../../globalMixin';
    const props = defineProps(['pack_detail','pack_index','form','readonly']);
    const emit = defineEmits(['changeInPackDetails','calc']);
    const data = reactive({show:false,initialPacking:[],selectedPacking:[]});
    const { base_url,copyProperties,refreshComponent} = globalMixin();

    const calc = () =>{
        try{
            emit('calc');
        }
        catch(error){
            console.log(error);
        }
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'packing_name':
                return true;
            case 'brand_name':
                return true;
            case 'qty':
                return props.readonly;
            case 'weight':
                return true;
            case 'discount':
                return props.readonly;
            case 'final_rate':
                return true;
            case 'net_amt':
                return true;
            default:
                return false;
        }
    }

</script>

<template>
    <tr>
        <td v-text="props.pack_index+1"></td>
        <td >
             <TextInput v-model="props.pack_detail.packing_name" :disabled="isDisabled('packing_name')"></TextInput>
        </td>
         <td>
            <TextInput v-model="props.pack_detail.brand_name" :disabled="isDisabled('brand_name')"></TextInput>
        </td>
        <td>
            <TextInput v-model="props.pack_detail.qty" :disabled="isDisabled('qty')" @blur="calc"></TextInput>
        </td>
        <td>
            <TextInput v-model="pack_detail.weight"  :disabled="isDisabled('weight')"></TextInput>
        </td>
        <td>
            <TextInput v-model="props.pack_detail.discount" :disabled="isDisabled('discount')" @blur="calc"></TextInput>
        </td>
         <td>
            <TextInput v-model="props.pack_detail.final_rate" :disabled="isDisabled('final_rate')"></TextInput>
        </td>
        <td>
            <TextInput v-model="props.pack_detail.net_amt" :disabled="isDisabled('net_amt')" ></TextInput>
        </td>
    </tr>
</template>
<style>
</style>
