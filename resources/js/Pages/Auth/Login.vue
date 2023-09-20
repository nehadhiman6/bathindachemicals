<script setup>
import {computed, reactive,} from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ProjectLogo from '@/Pages/CustomComponents/Others/ProjectLogo.vue';
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import CompanySelect from '@/Pages/ProjectComponents/SelectComponents/CompanySelect.vue';


const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    companies:Array,
    years:Array
});

const data = reactive({showCompanies:false});

const form = useForm({
    email: '',
    password: '',
    company_id:0,
    year:'',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => {
            form.reset('password');
           location.reload();
        }
    });
};
const company_options = computed(() => {
    let array = (props.companies);
    array.forEach(arr => {
        arr.text = arr.company_name;
    });
    return array;
});
const year_options = computed(() => {
    let array = (props.years);
    array.forEach(arr => {
        let newStr = arr.year.substring(0, 4).concat(' - ').concat( arr.year.substring(4));
        arr.id = arr.year;
        arr.text = newStr;
    });
    return array;
});

const isValidEmail = () => {
  return /^[^@]+@\w+(\.\w+)+\w$/.test(form.email);
};

const validateEmailShowCompanies = () => {
    if(isValidEmail()){
        data.showCompanies =true;
    }
};

</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <!-- <AuthenticationCardLogo /> -->
            <ProjectLogo/>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                    @blur="validateEmailShowCompanies"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4" v-if="data.showCompanies">
                <InputLabel for="company_id" value="Company" />
                <company-select v-model="form.company_id"  :email="form.email" url="login-companies/filtered"></company-select>
                <InputError class="mt-2" :message="form.errors.company_id" />
            </div>

            <div class="mt-4">
                <InputLabel for="year" value="Year" />
                <SelectInput v-model="form.year" :options ="year_options" ></SelectInput>
                <InputError class="mt-2" :message="form.errors.year" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Forgot your password?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
