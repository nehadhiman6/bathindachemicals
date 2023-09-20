
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin';

    const { base_url} = globalMixin();
    const props = defineProps(['form_id','delExtended']);
    const form = reactive( new Form({ form_id: 0,del_extended_date:""}));
    const data = reactive({ create_url:'delivery-extended'});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Delivery Extended':'Add Delivery Extended');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getQty();
        }
    });
    const submitForm = () =>{
        axios.post(base_url.value+'/delivery-extended/',form)
        .then(function(response){
            console.log(response);
            if(response.data.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getQty = () =>{
        form.del_extended_date = props.delExtended;
        form.form_id  = props.form_id;

    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="del_extended_date" value="Delivery Extended" />
                    <date-picker  class-name="del_extended_date" v-model="form.del_extended_date" :error="form.errors.get('del_extended_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('del_extended_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/3 md:w-1/3 md:flex-0">
             <div class="mb-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
