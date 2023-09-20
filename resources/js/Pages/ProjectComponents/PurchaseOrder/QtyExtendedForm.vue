
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin';

    const { base_url} = globalMixin();
    const props = defineProps(['form_id','qty','rate']);
    const form = reactive( new Form({ form_id: 0,qty_extended:"0",rate_extended:'0'}));
    const data = reactive({ create_url:'qty-extended'});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Qty Extended':'Add Qty Extended');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getQty();
        }
    });
    const submitForm = () =>{
        axios.post(base_url.value+'/qty-extended-add/',form)
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
        form.form_id  = props.form_id;
        form.rate_extended = props.rate;
        form.qty_extended = props.qty;

    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="qty_extended" value="Qty" />
                    <TextInput v-model="form.qty_extended" type="text" required :error="form.errors.get('qty_extended') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('qty_extended')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="rate_extended" value="Rate" />
                    <TextInput v-model="form.rate_extended" type="text" required :error="form.errors.get('rate_extended') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('rate_extended')" />
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
