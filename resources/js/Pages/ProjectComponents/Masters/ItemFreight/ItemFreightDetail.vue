<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import CitySelect from '@/Pages/ProjectComponents/SelectComponents/CitySelect.vue';
    const props = defineProps(['detail','index','form','close_qty','readonly']);
    const emit = defineEmits(['changeInDetails']);
    const data = reactive({cityInitials:[],citySelected:[],show:true});
    onMounted(() => {
        console.log(props.detail.city);
        if(props.detail.city){
            data.cityInitials = [{'id':props.detail.city.id,'text':props.detail.city.name}];
            data.citySelected = [props.detail.city.id];
        }
        refreshComponent(data,'show');
    });

    const isDisabled=(value)=>{
        switch(value) {
            case 'remove':
                return props.readonly == false ? true :false;
            case 'city_id':
                return props.readonly;
            case 'freight':
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
            <city-select :key="props.detail.random" v-model="props.detail.city_id" :index="'city_'+props.detail.random"
                :initials = "data.cityInitials"
                :selected = "data.citySelected"
                :disabled="isDisabled('city_id')"
                :error="form.errors.get('item_freight_details.'+props.index+'.city_id') ? true :false"
            >
            </city-select>
            <InputError class="mt-1 "  v-if="form.errors.get('item_freight_details.'+props.index+'.city_id')" :message="form.errors.get('item_freight_details.'+props.index+'.city_id')" />
        </td>
        <td>
            <TextInput :disabled="isDisabled('freight')" v-model="props.detail.freight" :error="form.errors.get('item_freight_details.'+props.index+'.freight') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('item_freight_details.'+props.index+'.freight')" :message="form.errors.get('item_freight_details.'+props.index+'.freight')" />
        </td>
        <td v-if="isDisabled('remove')">
             <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
