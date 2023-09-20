<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    const props = defineProps(['detail','index','form','readonly']);
    const emit = defineEmits(['changeInDetails']);
    onMounted(() => {
    });

    const isDisabled=(value)=>{
        switch(value) {
            case 'remove':
                return props.readonly == false ? true :false;
            case 'rate_from':
                return props.readonly;
            case 'rate_to':
                return props.readonly;
            case 'rate_diff':
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
        <td >
            <TextInput v-model="props.detail.rate_from" :disabled="isDisabled('rate_from')" :error="form.errors.get('rate_diff_details.'+props.index+'.rate_from') ? true :false"/>
            <InputError class="mt-1 "  v-if="form.errors.get('rate_diff_details.'+props.index+'.rate_from')" :message="form.errors.get('rate_diff_details.'+props.index+'.rate_from')" />
        </td>
         <td  >
             <TextInput v-model="props.detail.rate_to" :disabled="isDisabled('rate_to')" :error="form.errors.get('rate_diff_details.'+props.index+'.rate_to') ? true :false"/>
             <InputError class="mt-1"  v-if="form.errors.get('rate_diff_details.'+props.index+'.rate_to')" :message="form.errors.get('rate_diff_details.'+props.index+'.rate_to')" />

        </td>
        <td>
            <TextInput v-model="props.detail.rate_diff" :disabled="isDisabled('rate_diff')" :error="form.errors.get('rate_diff_details.'+props.index+'.rate_diff') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('rate_diff_details.'+props.index+'.rate_diff')" :message="form.errors.get('rate_diff_details.'+props.index+'.rate_diff')" />
        </td>
        <td v-if="isDisabled('remove')">
             <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
