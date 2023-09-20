
<script setup>
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import globalMixin from '../../../../globalMixin';
    import InputError from '@/Components/InputError.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    const props = defineProps(['bl_detail','index','form','readonly']);
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
            case 'bl_detail':
                return props.readonly;
            case 'bl_no':
                return props.readonly;
            case 'bl_qty':
                return props.readonly;
            default:
                return false;
        }
    }

</script>

<template>
    <tr>
        <td v-text="props.index+1"></td>

         <td>
            <date-picker :class-name="props.index+'bl_detail'" :key="props.index+'bl_detail'" :disabled="isDisabled('bl_detail')" v-model="props.bl_detail.bl_date" :error="props.form.errors.get('bl_date') ? true :false"></date-picker>
             <InputError class="mt-1"   v-if="props.form.errors.get('bl_details.'+props.index+'.bl_date')" :message="props.form.errors.get('bl_details.'+props.index+'.bl_date')" />
        </td>
        <td>
             <TextInput :disabled="isDisabled('bl_no')" v-model="props.bl_detail.bl_no" ></TextInput>
              <InputError class="mt-1"  v-if="props.form.errors.get('bl_details.'+props.index+'.bl_no')" :message="props.form.errors.get('bl_details.'+props.index+'.bl_no')" />
        </td>
        <td>
            <TextInput v-model="props.bl_detail.bl_qty" :disabled="isDisabled('bl_qty')"></TextInput>
             <InputError class="mt-1"  v-if="props.form.errors.get('bl_details.'+props.index+'.bl_qty')" :message="props.form.errors.get('bl_details.'+props.index+'.bl_qty')" />
        </td>
        <td>
            <i v-if="isDisabled('button')" class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInBlDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
<style>
</style>
