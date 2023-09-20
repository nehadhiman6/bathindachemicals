<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
      import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import { onMounted,onUnmounted ,reactive,computed  } from 'vue';
    import globalMixin from '../../../../globalMixin';
    const { base_url,refreshComponent} = globalMixin();
    const emit = defineEmits(['submitForm'])
    const props = defineProps(['index','form','readonly'])
    const data = reactive({'show':false,
        unitInitials:[],
        unitSelected:[],itemShow:true
    });
    onMounted(()=>{
        data.show = true;
        if(props.form.secondary_unit[props.index].item_unit){
            data.unitInitials = [{'id':props.form.secondary_unit[props.index].item_unit.id,'text':props.form.secondary_unit[props.index].item_unit.unit_name}];
            data.unitSelected = [props.form.secondary_unit[props.index].item_unit.id];
        }
        refreshComponent(data, 'itemShow');
    });
    onUnmounted(() => {
        data.show = false;
    });
    const updateItemUnit = (id,index,item_unit)=>{
        console.log(item_unit);
        props.form.secondary_unit[props.index].item_unit = item_unit;
    }


    const getConversion = ()=>{
        var factor =  props.form.secondary_unit[props.index].multiplier / props.form.secondary_unit[props.index].divider;
        props.form.secondary_unit[props.index].conversion_factor =isNaN(factor) ? 0 : factor;
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_unit_id':
                return props.readonly;
            case 'multiplier':
                return props.readonly;
            case 'divider':
                return props.readonly;
            case 'conversion_factor':
                return true;
            default:
                return false;
        }
    }
</script>
<template>
    <Modal :show="data.show" max-width="3xl" @close="emit('submitForm')">
            <div class="px-6 py-4">
                <div class="text-lg font-medium text-gray-900">
                    Item Conversion (Secondary Unit)
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    <div class="flex flex-wrap items-end -mx-3">
                        <div :class="form.form_id > 0 ? 'w-full max-w-full px-3 shrink-0 md:w-1/1 lg:w-1/1 md:flex-0':'w-full max-w-full px-3 shrink-0 md:w-1/1 lg:w-1/1 md:flex-0'" >
                            <div class="mb-1" v-if="data.itemShow">
                                <InputLabel for="item_unit_id" value="Item Unit" />
                                <item-unit-select :index="'conv'+props.index" v-model="props.form.secondary_unit[props.index].item_unit_id" type="text" :initials="data.unitInitials" :selected="data.unitSelected" :error="props.form.errors.get('secondary_unit.'+props.index+'.item_unit_id') ? true :false"
                                @updateItemUnit="updateItemUnit"
                                :disabled="isDisabled('item_unit_id')"
                                ></item-unit-select>
                                <InputError class="mt-2" :message="props.form.errors.get('secondary_unit.'+props.index+'.item_unit_id')" />
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="multiplier" value="Multiplier" />
                                <TextInput  :disabled="isDisabled('multiplier')" v-model="props.form.secondary_unit[props.index].multiplier" type="text" required @blur="getConversion" :error="props.form.errors.get('secondary_unit.'+props.index+'.multiplier') ? true :false" />
                                <InputError class="mt-2" :message="props.form.errors.get('secondary_unit.'+props.index+'.multiplier')" />
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="divider" value="Divider" />
                                <TextInput v-model="props.form.secondary_unit[props.index].divider" :disabled="isDisabled('divider')" type="text" required  @blur="getConversion" :error="props.form.errors.get('secondary_unit.'+props.index+'.divider') ? true :false" />
                                <InputError class="mt-2" :message="props.form.errors.get('secondary_unit.'+props.index+'.divider')" />
                            </div>
                        </div>
                            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="conversion_factor" value="Conversion Factor" />
                                <TextInput v-model="props.form.secondary_unit[props.index].conversion_factor" :disabled="isDisabled('conversion_factor')" type="text" required :error="props.form.errors.get('secondary_unit.'+props.index+'.conversion_factor') ? true :false" />
                                <InputError class="mt-2" :message="props.form.errors.get('secondary_unit.'+props.index+'.conversion_factor')" />
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="mt-4">
                    <ButtonComp @buttonClicked="emit('submitForm')" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                    <ButtonComp @buttonClicked="emit('submitForm')" type="cancel">Cancel</ButtonComp>
                </div>
        </div>
    </Modal>
</template>
