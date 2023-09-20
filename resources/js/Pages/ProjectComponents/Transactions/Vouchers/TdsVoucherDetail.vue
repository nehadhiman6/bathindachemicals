<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
      import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import TdsSectionSelect from '@/Pages/ProjectComponents/SelectComponents/TdsSectionSelect.vue';
    import { onMounted,onUnmounted ,reactive,computed  } from 'vue';
    import globalMixin from '../../../../globalMixin';
    const { base_url,refreshComponent} = globalMixin();
    const emit = defineEmits(['submitForm'])
    const props = defineProps(['index','form','entry','show'])
    const data = reactive({
       sectionShow:true,
       rates:[],
       sectionInitials:[],
       sectionSelected:[]
    });

    const rate_options = computed(() => {
        var arr =[];
        let array = JSON.parse(JSON.stringify(data.rates));
        array.forEach(element => {
            console.log(element);
            for(const el in element) {
                if(element[el] != null){
                    arr.push({'id':element[el],'text':element[el]})
                }

            }

        });
        return arr;
    });
    onMounted(() => {
        if(props.entry.section){
            data.sectionInitials = [{'id':props.entry.section.id,'text':props.entry.section.section}];
            data.sectionSelected = [props.entry.section.id];
            getRates(props.entry.section);
        }
        refreshComponent(data, 'sectionShow');

    });

    const getRates=(selected_data)=>{
        let row = {}
        row={
            rate1:selected_data.rate1,
        }
        data.rates.push(row);
        row={
            rate2:selected_data.rate2,
        }
        data.rates.push(row);
        row={
            rate3:selected_data.rate3,
        }
        data.rates.push(row);
        row={
            non_pan_rate:selected_data.non_pan_rate,
        }
        data.rates.push(row);
        row={
            higher_rate:selected_data.higher_rate,
        }
        data.rates.push(row);
    }
    const updateSection=(v,i,selected_data)=>{
        props.entry.section = selected_data;
        getRates(selected_data);
    }

    const calc=()=>{
        let amount = Utilities.round(props.entry.tds_on*props.entry.rate/100);
        props.entry.tds_amt = Utilities.round(Utilities.round(amount)+Utilities.round(props.entry.tds_adj));
    }

</script>
<template>
    <Modal :show="props.show" max-width="3xl" @close="emit('submitForm')">
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                TDS DETAILS
            </div>
            <div class="mt-4 text-sm text-gray-600">
                    <div class="flex flex-wrap items-end -mx-3">
                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1" v-if="data.sectionShow">
                                <InputLabel for="tds_sec_id" value="Section" class="required" />
                                <tds-section-select
                                    v-model="props.entry.section_id"
                                    index="1"
                                    :error="form.errors.get('voucher_details.'+props.index+'.section_id') ? true :false"
                                    :initials="data.sectionInitials"
                                    :selected="data.sectionSelected"
                                    @updateSection="updateSection"
                                    >
                                </tds-section-select>
                                <InputError class="mt-2" :message="form.errors.get('voucher_details.'+props.index+'.section_id')" />
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="rate" value="Rate" />
                                <SelectInput v-model="props.entry.rate" :options ="rate_options" :error="props.form.errors.get('voucher_details.'+props.index+'.rate') ? true :false" @blur="calc"></SelectInput>
                                <!-- <TextInput  v-model="props.entry.rate" type="text" :error="props.form.errors.get('voucher_details.'+props.index+'.rate') ? true :false" /> -->
                                <InputError class="mt-2" :message="props.form.errors.get('voucher_details.'+props.index+'.rate')" />
                            </div>
                        </div>

                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="tds_on" value="TDS On" />
                                <TextInput  v-model="props.entry.tds_on" type="text" :error="props.form.errors.get('voucher_details.'+props.index+'.tds_on') ? true :false" @blur="calc"/>
                                <InputError class="mt-2" :message="props.form.errors.get('voucher_details.'+props.index+'.tds_on')" />
                            </div>
                        </div>

                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="tds_adj" value="TDS Adjustment" />
                                <TextInput  v-model="props.entry.tds_adj" type="text" :error="props.form.errors.get('voucher_details.'+props.index+'.tds_adj') ? true :false" @blur="calc"/>
                                <InputError class="mt-2" :message="props.form.errors.get('voucher_details.'+props.index+'.tds_adj')" />
                            </div>
                        </div>

                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="tds_amt" value="TDS Amount" />
                                <TextInput  v-model="props.entry.tds_amt" disabled type="text" :error="props.form.errors.get('voucher_details.'+props.index+'.tds_amt') ? true :false" />
                                <InputError class="mt-2" :message="props.form.errors.get('voucher_details.'+props.index+'.tds_amt')" />
                            </div>
                        </div>

                        <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                            <div class="mb-1">
                                <InputLabel for="tds_part" value="TDS Particular" />
                                <TextInput  v-model="props.entry.tds_part" type="text" :error="props.form.errors.get('voucher_details.'+props.index+'.tds_part') ? true :false" />
                                <InputError class="mt-2" :message="props.form.errors.get('voucher_details.'+props.index+'.tds_part')" />
                            </div>
                        </div>

                    </div>
                </div>
            <div class="mt-4">
                <ButtonComp @buttonClicked="emit('submitForm')" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('submitForm')" type="cancel">Cancel</ButtonComp>
            </div>
        </div>
    </Modal>
</template>
