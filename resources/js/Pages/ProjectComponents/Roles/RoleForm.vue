<script setup>
    import {   ref,  computed,  onMounted,    reactive} from 'vue';

    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';

    import InputError from '@/Components/InputError.vue';
    import globalMixin from '../../../globalMixin';


    const { base_url} = globalMixin();
    const props = defineProps(['form_id','roles','role_id','form']);

    const data = reactive({ create_url:'users' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Role':'Add ROle');
    const roles_options = computed(() => {
        let array = JSON.parse(JSON.stringify(props.roles));
        array.forEach(arr => {
            arr.text = arr.name;
        });
        return array;
    });

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if(props.form_id > 0){
            getState();
        }
    });
    // const submitForm = () =>{
    //     form['postForm'](data.create_url)
    //     .then(function(response){
    //         console.log(response);
    //         if(response.success){
    //             emit('resetForm');
    //         }
    //     })
    //     .catch(function(error){
    //     });
    // }

</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
                <div class="mb-4">
            <slot/>
                </div>

            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
             <!-- <div class="mb-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div> -->
            </div>
        </div>
    </FormWrapper>
</div>
</template>

<style>

</style>
