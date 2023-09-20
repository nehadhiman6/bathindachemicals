
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({
        form_id: 0,
        name:"",
        api_key:"",
        criteria:"SL",
        prefix:"",
        tkt_prefix:"",
        vsiteid:"",
        url:"",
    }));
    const data = reactive({ create_url:'kanda' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Kanda':'Add Kanda');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getKanda();
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
    const getKanda = () =>{
        axios.get(base_url.value+'/kanda/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let kanda = response.data.kanda;
                form.name = kanda.name;
                form.api_key = kanda.api_key;
                form.criteria = kanda.criteria;
                form.prefix = kanda.prefix;
                form.tkt_prefix = kanda.tkt_prefix;
                form.vsiteid = kanda.vsiteid;
                form.url = kanda.url;
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="api_key" value="API Key" />
                    <TextInput v-model="form.api_key" type="text" required :error="form.errors.get('api_key') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('api_key')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="criteria" value="Criteria" />
                    <TextInput v-model="form.criteria" type="text" required :error="form.errors.get('criteria') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('criteria')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="prefix" value="Prefix" />
                    <TextInput v-model="form.prefix" type="text" required :error="form.errors.get('prefix') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('prefix')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="tkt_prefix" value="Ticket Prefix" />
                    <TextInput v-model="form.tkt_prefix" type="text" required :error="form.errors.get('tkt_prefix') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('tkt_prefix')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="vsiteid" value="Vsite Id" />
                    <TextInput v-model="form.vsiteid" type="text" required :error="form.errors.get('vsiteid') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vsiteid')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="url" value="Url" />
                    <TextInput v-model="form.url" type="text" required :error="form.errors.get('url') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('url')" />
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
