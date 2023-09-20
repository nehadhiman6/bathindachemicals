<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted, onBeforeMount,   reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';
    const props = defineProps(['detail','index','form','readonly']);
     const data = reactive({brandInitials:[],brandSelected:[],packingInitials:[],packingSelected:[],show:true});
    const emit = defineEmits(['changeInDetails']);
    onBeforeMount(() => {
        if(props.detail.brand){
            data.brandInitials = [{'id':props.detail.brand.id,'text':props.detail.brand.name}];
            data.brandSelected = [props.detail.brand.id];
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
            case 'brand_id':
                return props.readonly;
            case 'packing_id':
                return props.readonly;
            case 'weight':
                return props.readonly;
            case 'conversion':
                return props.readonly;
            case 'tin_cost':
                return props.readonly;
            case 'extra':
                return props.readonly;
            case 'divisor':
                return props.readonly;
            case 'muliplier':
                return props.readonly;
            case 'packing_cost':
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
            <brand-select :disabled="isDisabled('brand_id')" :initials="data.brandInitials" :selected="data.brandSelected" :index="'brand_id'+props.detail.random" v-model="props.detail.brand_id" :key="props.detail.random" :error="form.errors.get('packing_formula_details.'+props.index+'.brand_id') ? true :false"></brand-select>
            <InputError class="mt-1 "  v-if="form.errors.get('packing_formula_details.'+props.index+'.brand_id')" :message="form.errors.get('packing_formula_details.'+props.index+'.brand_id')" />
        </td>
        <td v-if="data.show">
            <packing-select :disabled="isDisabled('packing_id')" :initials="data.packingInitials" :selected="data.packingSelected"  :index="'packing_id'+props.detail.random" v-model="props.detail.packing_id"  :key="props.detail.random" :error="form.errors.get('packing_formula_details.'+props.index+'.packing_id') ? true :false"></packing-select>
            <InputError class="mt-1 "  v-if="form.errors.get('packing_formula_details.'+props.index+'.packing_id')" :message="form.errors.get('packing_formula_details.'+props.index+'.packing_id')" />
        </td>
          <td v-if="data.show">
             <TextInput :disabled="isDisabled('weight')" v-model="props.detail.weight" :error="form.errors.get('packing_formula_details.'+props.index+'.weight') ? true :false"/>
            <InputError class="mt-1 "  v-if="form.errors.get('packing_formula_details.'+props.index+'.weight')" :message="form.errors.get('packing_formula_details.'+props.index+'.weight')" />
        </td>
        <td >
            <TextInput :disabled="isDisabled('conversion')" v-model="props.detail.conversion" :error="form.errors.get('packing_formula_details.'+props.index+'.conversion') ? true :false"/>
            <InputError class="mt-1 "  v-if="form.errors.get('packing_formula_details.'+props.index+'.conversion')" :message="form.errors.get('packing_formula_details.'+props.index+'.conversion')" />
        </td>
         <td  >
             <TextInput :disabled="isDisabled('tin_cost')" v-model="props.detail.tin_cost" :error="form.errors.get('packing_formula_details.'+props.index+'.tin_cost') ? true :false"/>
             <InputError class="mt-1"  v-if="form.errors.get('packing_formula_details.'+props.index+'.tin_cost')" :message="form.errors.get('packing_formula_details.'+props.index+'.tin_cost')" />

        </td>
        <td>
            <TextInput :disabled="isDisabled('extra')" v-model="props.detail.extra" :error="form.errors.get('packing_formula_details.'+props.index+'.extra') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('packing_formula_details.'+props.index+'.extra')" :message="form.errors.get('packing_formula_details.'+props.index+'.extra')" />
        </td>
         <td>
            <TextInput :disabled="isDisabled('divisor')" v-model="props.detail.divisor" :error="form.errors.get('packing_formula_details.'+props.index+'.divisor') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('packing_formula_details.'+props.index+'.divisor')" :message="form.errors.get('packing_formula_details.'+props.index+'.divisor')" />
        </td>
        <td>
            <TextInput :disabled="isDisabled('muliplier')" v-model="props.detail.muliplier" :error="form.errors.get('packing_formula_details.'+props.index+'.freight') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('packing_formula_details.'+props.index+'.muliplier')" :message="form.errors.get('packing_formula_details.'+props.index+'.muliplier')" />
        </td>
         <td>
            <TextInput :disabled="isDisabled('packing_cost')" v-model="props.detail.packing_cost" :error="form.errors.get('packing_formula_details.'+props.index+'.packing_cost') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('packing_formula_details.'+props.index+'.packing_cost')" :message="form.errors.get('packing_formula_details.'+props.index+'.packing_cost')" />
        </td>
        <td>
            <TextInput :disabled="isDisabled('freight')" v-model="props.detail.freight" :error="form.errors.get('packing_formula_details.'+props.index+'.freight') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('packing_formula_details.'+props.index+'.freight')" :message="form.errors.get('packing_formula_details.'+props.index+'.freight')" />
        </td>
        <td v-if="isDisabled('remove')">
             <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
