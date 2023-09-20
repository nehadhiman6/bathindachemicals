
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url, copyProperties} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({ form_id: 0, name:'', days_1:'', days_2:'', days_3:'', days_4:'', percentage_1:'', percentage_2:'', percentage_3:'', percentage_4:'',
    }));
    const data = reactive({ create_url:'pay-terms' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Pay Term':'Add Pay Term');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getPartySubGroup();
        }
    });
    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getPartySubGroup = () =>{
        axios.get(base_url.value+'/pay-terms/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let payterms = response.data.pay_term;
                copyProperties(payterms,form);
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" :required="true"/>
                    <TextInput v-model="form.name" type="text"  autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="days_1" value="Days 1" :required="true"/>
                    <TextInput v-model="form.days_1" type="text"   :error="form.errors.get('days_1') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('days_1')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="days_2" value="Days 2" />
                    <TextInput v-model="form.days_2" type="text"   :error="form.errors.get('days_2') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('days_2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="days_3" value="Days 3" />
                    <TextInput v-model="form.days_3" type="text"  :error="form.errors.get('days_3') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('days_3')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="days_4" value="Days 4" />
                    <TextInput v-model="form.days_4" type="text"  :error="form.errors.get('days_4') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('days_4')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="percentage_1" value="Percetage 1" :required="true"/>
                    <TextInput v-model="form.percentage_1" type="text"  :error="form.errors.get('percentage_1') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('percentage_1')" />
                </div>
            </div>

              <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="percentage_2" value="Percentage 2" />
                    <TextInput v-model="form.percentage_2" type="text"  :error="form.errors.get('percentage_2') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('percentage_2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="percentage_3" value="Percentage 3" />
                    <TextInput v-model="form.percentage_3" type="text"  :error="form.errors.get('percentage_3') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('percentage_3')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="percentage_4" value="Percentage 4" />
                    <TextInput v-model="form.percentage_4" type="text"  :error="form.errors.get('percentage_4') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('percentage_4')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
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
