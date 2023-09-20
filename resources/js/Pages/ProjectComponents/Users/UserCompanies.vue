<script setup>
    import {   ref,  computed,  onMounted,    reactive} from 'vue';
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import CompanySelect from '@/Pages/ProjectComponents/SelectComponents/CompanySelect.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import globalMixin from '../../../globalMixin';
    import AppLayout from '@/Layouts/AppLayout.vue';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['user_id']);
    const form = reactive( new Form({
            user_id: props.user_id,
            companies:[]
    }));
    const data = reactive({ create_url:'user-companies',show:false,user_companies:[],companyInitials:[],
companySelected:[],showSelect:true});
    const pageTitle = computed(() => data.user_companies.length > 0 ? 'Update User Companies':'Add User Companies');

    const emit = defineEmits(['resetForm']);
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    onMounted(() => {
        data.show = true;
        getUserCompanies();
    });


    const updateCompany= (ids,index,value)=>{
        form.companies = ids;
    }

    const setCompanySelect= ()=>{
        data.companyInitials = [];
        data.companySelected = [];
        data.user_companies.forEach(user_comp => {
            data.companyInitials.push({'id':user_comp.company_id,'text':user_comp.company.company_name});
            data.companySelected.push(user_comp.company_id);
        });
        refreshComponent(data,'showSelect');
    }


    const getUserCompanies= ()=>{
        axios.get(base_url.value+'/user-companies/'+props.user_id)
        .then(function(response){
            console.log(response);
            if(response.data.user_companies){
                data.user_companies = response.data.user_companies;
                setCompanySelect();
            }
        })
        .catch(function(error){
            console.log(error);
        })
    }
</script>


<template>
<div>
    <Modal  max-width="5xl" :show="data.show" @close="submitForm()">
       <div class="px-6 py-4">
            <div class="mt-4 text-sm text-gray-600">
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0">
                        <div class="mb-2" v-if="data.showSelect">
                            <InputLabel for="name" value="Companies" />
                            <company-select :multiple="true"   :initials="data.companyInitials" :selected="data.companySelected" @updateCompany="updateCompany"></company-select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <div class="mt-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
            </div>
        </div>

    </Modal>

</div>
</template>

<style>

</style>
