<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import NewRecordButton from '@/Components/CustomComponents/Buttons/NewRecordButton.vue';
import TitleComponent from '@/Components/CustomComponents/Sections/TitleComponent.vue';
import SubmitButton from '@/Components/CustomComponents/Buttons/SubmitButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import UserForm from '@/Pages/Users/UserForm.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref, onMounted, reactive } from 'vue';
import globalMixin from '../../globalMixin';
import { Inertia } from '@inertiajs/inertia';

const data = reactive( { create_url:'user-preferences' });
const props = defineProps(['data']);
const form = reactive(
    new Form({
        form_id:0,
        sidebar: props.data.sidebar,
        sidebar_position:props.data.sidebar_position,
        color_scheme:props.data.color_scheme,
        default_layout:props.data.default_layout
    })
);

const submitForm = () =>{
    form['postForm'](data.create_url)
    .then(function(response){
        console.log(response);
        Inertia.visit('user-preferences');
    })
    .catch(function(error){
        console.log(error);
    });
}

const {
    base_url
} = globalMixin();


const resetForm = () => {
    state.formOpen = false;
    state.form_id = 0;
    state.table.ajax.reload(null, false);
}
</script>

<template>
    <AppLayout title="Profile" :data="props.data">
        <template #header>
             <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                Layout Preference
            </span>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <FormSection >
                        <template #title>
                            App Layout Settings
                        </template>

                        <template #description>
                            Update your app's layout Preference.
                        </template>

                        <template #form>
                            <!-- sidebar -->



                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="sidebar_position" value="Sidebar Position" />
                                <div class="flex justify-center">
                                   <div class="form-check form-check-inline">
                                        <input class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="sidebar_position_option" id="left_sidebar" value="left" v-model="form.sidebar_position">
                                        <label class="form-check-label inline-block text-gray-800" for="left_sidebar">Left</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="sidebar_position_option" id="top_sidebar" value="top" v-model="form.sidebar_position">
                                        <label class="form-check-label inline-block text-gray-800" for="top_sidebar">Top</label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.sidebar_position" class="mt-2" />
                            </div>
                               <div class="col-span-6 sm:col-span-4" v-if="form.sidebar_position == 'left'">
                                <InputLabel for="sidebar" value="Sidebar" />
                                <div class="flex justify-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="sidebar_option" id="inlineRadio1" value="closed" v-model="form.sidebar">
                                        <label class="form-check-label inline-block text-gray-800" for="inlineRadio1">Collapsed</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="sidebar_option" id="inlineRadio2" value="open"  v-model="form.sidebar">
                                        <label class="form-check-label inline-block text-gray-800" for="inlineRadio2">Open</label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.sidebar" class="mt-2" />
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="color_scheme" value="Color Scheme"/>
                                <div class="flex justify-center">
                                   <div class="form-check form-check-inline">
                                        <input v-model="form.color_scheme" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="color_scheme" id="color_scheme_default" value="option1">
                                        <label class="form-check-label inline-block text-gray-800" for="color_scheme_default">Default</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input v-model="form.color_scheme" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="color_scheme" id="color_scheme_one" value="option2">
                                        <label class="form-check-label inline-block text-gray-800" for="color_scheme_one">One</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input v-model="form.color_scheme" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="color_scheme" id="color_scheme_two" value="option2">
                                        <label class="form-check-label inline-block text-gray-800" for="color_scheme_two">Two</label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.color_scheme" class="mt-2" />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="color_scheme" value="Default Layout"/>
                                <div class="flex justify-center">
                                   <div class="form-check form-check-inline">
                                        <input v-model="form.default_layout" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="layout" id="card_layout" value="card">
                                        <label class="form-check-label inline-block text-gray-800" for="card_layout">Card</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input v-model="form.default_layout" class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer" type="radio" name="layout" id="table_layout" value="table">
                                        <label class="form-check-label inline-block text-gray-800" for="table_layout">Table</label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.color_scheme" class="mt-2" />
                            </div>
                        </template>

                        <template #actions>
                             <SubmitButton @buttonClicked="submitForm" >
                                <span v-text="'Update'" ></span>
                            </SubmitButton>
                        </template>
                    </FormSection>
                    <SectionBorder />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
