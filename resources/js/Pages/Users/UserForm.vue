<script setup>
    import { ref, onMounted ,reactive,computed } from 'vue';
    import FormComponent from '@/Components/CustomComponents/Form/FormComponent.vue';
    import SubmitButton from '@/Components/CustomComponents/Buttons/SubmitButton.vue';
    import CancelButton from '@/Components/CustomComponents/Buttons/CancelButton.vue';
    import SelectInput from '@/Components/CustomComponents/Inputs/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Inertia } from '@inertiajs/inertia';
    import globalMixin from '../../globalMixin';

    const props = defineProps(['form_id']);

    const emit = defineEmits(['formReset']);

    const form = reactive( new Form({ form_id: 0,name:"",email:"",password:"",password_confirmation:"",role_id:0 }));
    const data = reactive( {
        create_url:'users',
        roles:[]
    });
    const {base_url} = globalMixin();

    onMounted(() => {
        getRoles();
        if(props.form_id > 0){
            getUser();
        }
    });



    const getRoles = ()=>{
        axios.get('roles-list')
        .then(function(response){
            console.log(response);
            data.roles = response.data.roles;
        }).catch(function(error){

        });
    }




    // a computed ref
    const role_options = computed(() => {
        let array = JSON.parse(JSON.stringify(data.roles));
        array.forEach(arr => {
            arr.text = arr.name;
        });
        return array;
    });


    const submitForm = () =>{
        form['postForm'](data.create_url)
            .then(function(response){
                console.log(response);
                if(response.success){
                    emit('resetForm');
                }
            })
            .catch(function(error){
            });
    }

    const getUser = () =>{
        axios.get(base_url.value+'/users/'+props.form_id)
        .then(function(response){
            if(response.data.success){
                let  user = response.data.user;
                form.name = user.name;
                form.email = user.email;
                if(user.roles.length > 0){
                    form.role_id = user.roles[0].role_id;
                }
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }
</script>
<template>
    <FormComponent>
        <template #title>
            <span v-if="form.form_id > 0">Update</span>
            <span v-if="form.form_id == 0">New</span>
             User
        </template>
        <template #right-content>
            This is added by User
        </template>
        <template #form>
            <div class="flex flex-wrap -mx-3 mb-6">
               <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
                <div class="w-full md:w-1/2 px-3">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-2" :message="form.errors.get('email')" />
                </div>
            </div>
            <div class="flex flex-wrap -mx-3 mb-6">
               <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-2" :message="form.errors.get('password')" />
                </div>
                <div class="w-full md:w-1/3 px-3">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-2" :message="form.errors.get('password_confirmation')" />
                </div>
                <div class="w-full md:w-1/3 px-3">
                    <InputLabel for="role_id" value="Role" />
                    <SelectInput :options="role_options" v-model="form.role_id" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('role_id')"/>
                </div>
            </div>
        </template>
        <template #actions>
            <SubmitButton @buttonClicked="submitForm" >
                <span v-text="form.form_id >0 ? 'Update':'Add'" ></span>
            </SubmitButton>
             <CancelButton @buttonClicked=" emit('resetForm');" >
                Cancel
            </CancelButton>
        </template>
    </FormComponent>
</template>

<style>

</style>
