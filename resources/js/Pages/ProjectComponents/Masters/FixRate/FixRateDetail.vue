<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    const props = defineProps(['detail','index','form','readonly']);
    const emit = defineEmits(['changeInDetails']);
    const data = reactive({itemInitials:[],itemSelected:[],show:true});
    onMounted(() => {
        if(props.detail.item){
            data.itemInitials = [{'id':props.detail.item.id,'text':props.detail.item.item_name}];
            data.itemSelected = [props.detail.item.id];
        }
        if(props.detail.packing){
            data.packingInitials = [{'id':props.detail.packing.id,'text':props.detail.packing.name}];
            data.packingSelected = [props.detail.packing.id];
        }
        refreshComponent(data,'show');
    });
    const isDisabled=(value)=>{
        switch(value) {
            case 'remove':
                return props.readonly == false ? true :false;
            case 'item_id':
                return props.readonly;
            case 'packing_id':
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
        <td  v-if="data.show">
            <item-select :key="props.detail.random" v-model="props.detail.item_id" :index="'item_'+props.detail.random"
                :initials = "data.itemInitials"
                :selected = "data.itemSelected"
                :disabled="isDisabled('item_id')"
                :error="form.errors.get('fix_rate_details.'+props.index+'.item_id') ? true :false"
            >
            </item-select>
            <InputError class="mt-1 "  v-if="form.errors.get('fix_rate_details.'+props.index+'.item_id')" :message="form.errors.get('fix_rate_details.'+props.index+'.item_id')" />
        </td>
        <td  v-if="data.show">
             <packing-select :key="props.detail.random" v-model="props.detail.packing_id" :index="'packing_'+props.detail.random"
                :initials = "data.packingInitials"
                :selected = "data.packingSelected"
                :disabled="isDisabled('packing_id')"
                :error="form.errors.get('fix_rate_details.'+props.index+'.packing_id') ? true :false"
            >
            </packing-select>
            <InputError class="mt-1"  v-if="form.errors.get('fix_rate_details.'+props.index+'.packing_id')" :message="form.errors.get('fix_rate_details.'+props.index+'.packing_id')" />
        </td>
          <td>
            <TextInput v-model="props.detail.rate" :disabled="isDisabled('rate')" :error="form.errors.get('fix_rate_details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('fix_rate_details.'+props.index+'.rate')" :message="form.errors.get('fix_rate_details.'+props.index+'.rate')" />
        </td>
        <td v-if="isDisabled('remove')">
             <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
