

<script setup>
import { ref, onMounted, reactive, nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import globalMixin from '../../../../globalMixin';
import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import InputError from '@/Components/InputError.vue';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['parameters','permissions']);
const form = reactive( new Form({
    parameters: [],
}));

onMounted(() => {
    form.parameters = props.parameters;
});


const state = reactive({
    create_url: "parameters",
    showAccount: true
});

const getParaChoiceArray= (parameter)=>{
    return parameter.para_choice.split(',');
};


const submitForm= ()=>{
    form["postForm"](base_url.value + "/" + state.create_url)
    .then(function (response) {
        if (response.success) {
            Utilities.showPopMessage(
                "Record has been updated Successfully",
                "Success!!"
            );
        }
    })
    .catch(function () {});
};


const updateAccount= (value, index)=>{
    if (isNaN(value)) {
        form.parameters[index].para_value = 0;
        return;
    }
    form.parameters[index].para_value = value;
};

const getAccountInitals= (parameter, field='account',field_name = 'name')=>{
    var arr = [];
    if (parameter[field]) {
        arr = [
            { text: parameter[field][field_name], id: parameter[field].id },
        ];
    }
    return arr;
};
const getAccountSelected= (parameter, field='account')=>{
     var arr = [];
        if (parameter[field]) {
            arr = [parameter[field].id];
        }
        return arr;
};




</script>

<template>
    <AppLayout title="Parameters">
        <FormWrapper title="Parameters">
            <div class="flex flex-wrap items-end -mx-3">
                <div class="w-full max-w-full px-3 shrink-0 md:w-2/3 md:flex-0" v-for="(parameter, key) in form.parameters" :key="key">
                   <InputLabel for="name" :value="parameter.para_disp_name" />
                    <span v-if="parameter.para_nature == 'Normal'">
                        <TextInput v-model="parameter.para_value" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    </span>
                   <span v-if="parameter.para_nature == 'A/c Help'">
                        <account-select   v-if= "state.showAccount" :error="form.errors.get('parameters.'+key+'.para_value') ? true :false"
                            :index="key"
                            v-model="parameter.para_value"
                            :branch_applicable="false"
                            :initials= "getAccountInitals(parameter)"
                            :selected= "getAccountSelected(parameter)"
                            @updateAccount="updateAccount">
                        </account-select>
                   </span>
                      <span v-else-if="parameter.para_nature == 'Gl A/c Help'">
                        <account-select   v-if= "state.showAccount" :error="form.errors.get('parameters.'+key+'.para_value') ? true :false"
                            :index="key"
                            type ="gl"
                            v-model="parameter.para_value"
                             :branch_applicable="false"
                            :initials= "getAccountInitals(parameter)"
                            :selected= "getAccountSelected(parameter)"
                            @updateAccount="updateAccount">
                        </account-select>
                   </span>
                    <span v-else-if="parameter.para_nature == 'Packing Help'">
                        <packing-select   v-if= "state.showAccount" :error="form.errors.get('parameters.'+key+'.para_value') ? true :false"
                            :index="key"
                              v-model="parameter.para_value"
                            :initials= "getAccountInitals(parameter,'packing')"
                            :selected= "getAccountSelected(parameter,'packing')"
                            @updateAccount="updateAccount">
                        </packing-select>
                    <InputError class="mt-2" :message="form.errors.get('parameters.'+key+'.para_value')" />
                   </span>
                     <span v-else-if="parameter.para_nature == 'Item Help'">
                        <item-select   v-if= "state.showAccount" :error="form.errors.get('parameters.'+key+'.para_value') ? true :false"
                            :index="key"
                            v-model="parameter.para_value"
                            :initials= "getAccountInitals(parameter,'item','item_name')"
                            :selected= "getAccountSelected(parameter,'item')"
                            @updateAccount="updateAccount">
                        </item-select>
                    <InputError class="mt-2" :message="form.errors.get('parameters.'+key+'.para_value')" />
                   </span>
                    <span v-else-if="parameter.para_nature == 'Item Unit Help'">
                        <item-unit-select   v-if= "state.showAccount" :error="form.errors.get('parameters.'+key+'.para_value') ? true :false"
                            :index="key"
                            v-model="parameter.para_value"
                            :initials= "getAccountInitals(parameter,'item_unit','unit_name')"
                            :selected= "getAccountSelected(parameter,'item_unit')"
                            @updateAccount="updateAccount">
                        </item-unit-select>
                    <InputError class="mt-2" :message="form.errors.get('parameters.'+key+'.para_value')" />
                   </span>
                </div>
            </div>
            <div class="flex flex-wrap items-end -mx-3 mt-2">
            <div class="w-full max-w-full px-3 shrink-0 lg:w-2/3 md:w-2/3 md:flex-0">
             <div class="mb-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
        </FormWrapper>
    </AppLayout>
</template>

