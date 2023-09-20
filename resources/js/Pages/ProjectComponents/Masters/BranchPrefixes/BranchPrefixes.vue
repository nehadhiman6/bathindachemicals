

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
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import InputError from '@/Components/InputError.vue';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['branches','prefixes','branch_prefixes']);
const form = reactive( new Form({
    branch_prefixes: [],
}));

const getDetail = ()=>{
    return {
        id:0,
        branch_id:0,
        prefix_id:0,
        prefix_value:'',
        prefix_label:''
    }
}

const getBranch = ()=>{
    return {
        branch_id:0,
        branch_name:'',
        prefixes:[]
    }
}


onMounted(() => {
    props.branches.forEach(element => {
        var branch_obj = getBranch();
        branch_obj.branch_name = element.name;
        branch_obj.branch_id = element.id;
        var prefix_arr = [];
        props.prefixes.forEach(ele => {
            var det = getDetail();
            det.branch_id = element.id;
            det.prefix_id = ele.id;
            det.prefix_label = ele.label;
            var selected_value =props.branch_prefixes.find(arr => arr.branch_id ==element.id && arr.prefix_id ==ele.id);
            det.prefix_value = selected_value ? selected_value['prefix_value']:'';
            prefix_arr.push(det);
        });
        branch_obj.prefixes = prefix_arr;
        form.branch_prefixes.push(branch_obj);
    });
});


const state = reactive({
    create_url: "branch-prefixes",
});




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




</script>

<template>
    <AppLayout title="Branch Prefixes">
        <FormWrapper title="Branch Prefixes">
           <fieldset class="border rounded bg-gray-50 p-4 mb-1"  v-for="(branch_prefix, key) in form.branch_prefixes" :key="key">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3" v-text="branch_prefix.branch_name"> </legend>
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3 md:w-1/4 sm:w-1/1" v-for="(prefix, p_key) in branch_prefix.prefixes" :key="p_key">
                        <InputLabel for="prefix_value" :value="prefix.prefix_label"/>
                        <TextInput v-model="prefix.prefix_value" type="text" required  :error="form.errors.get('branch_prefixes.'+key+'.prefixes.'+p_key+'.prefix_value') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('branch_prefixes.'+key+'.prefixes.'+p_key+'.prefix_value')" />
                    </div>
                </div>
           </fieldset>

            <div class="flex flex-wrap items-end -mx-3 mt-2">
                <div class="w-full max-w-full px-3 lg:w-2/3 md:w-2/3">
                <div class="mb-4">
                    <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                </div>
                </div>
            </div>
        </FormWrapper>
    </AppLayout>
</template>

