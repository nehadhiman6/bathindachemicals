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
    const props = defineProps(['form_id','roles']);
    const form = reactive( new Form({
            form_id: 0,
            name:'',
            email:'',
            role_id:0,
            password:'',
            password_confirmation:''
    }));
    const data = reactive({ create_url:'users' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  User':'Add User');
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
    const getState = () =>{
        axios.get(base_url.value+'/users/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let user = response.data.user;
                form.name = user.name;
                form.email = user.email;
                if(user.roles.length>0){
                    form.role_id = user.roles[0].id;
                }
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                 <div class="mb-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput v-model="form.email" type="email" required autofocus :error="form.errors.get('email') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('email')" />
                </div>
             </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                 <div class="mb-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput v-model="form.password" type="password" required autofocus :error="form.errors.get('password') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('password')" />
                </div>
             </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                 <div class="mb-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput v-model="form.password_confirmation" type="password" required autofocus :error="form.errors.get('password_confirmation') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('password_confirmation')" />
                </div>
             </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="role_id" value="Role" />
                    <SelectInput v-model="form.role_id" :options ="roles_options" autofocus :error="form.errors.get('role_id') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('role_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
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
