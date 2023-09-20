
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import CitySelect from '@/Pages/ProjectComponents/SelectComponents/CitySelect.vue';
    import IfscSelect from '@/Pages/ProjectComponents/SelectComponents/IfscSelect.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['branch_id','branch','form_id']);
    const form = reactive( new Form({
            form_id: 0,
            branch_id:props.branch_id,
            benificiary_name:'',
            bank_account_number:'',
            ifsc_id:'',
    }));
    const data = reactive({ create_url:'branch-bank-accounts',ifscInitials:[],ifscSelected:[],showIfsc:true});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Bank Account':'Add Bank Account');


    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        form.form_id = props.form_id;
        if(props.form_id > 0){
            getBankAccount();
        }
    });
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
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
    const getBankAccount = () =>{
        axios.get(base_url.value+'/'+data.create_url+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let branch_bank_account = response.data.branch_bank_account;
                copyProperties(branch_bank_account,form);
                if(branch_bank_account.ifsc){
                    data.ifscInitials=[{'id':branch_bank_account.ifsc.id, 'text':branch_bank_account.ifsc.ifsc_code}];
                    data.ifscSelected=[branch_bank_account.ifsc.id];
                }
                refreshComponent(data,'showIfsc');
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
            console.log(error);
        });
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="bank_account_number" value="Account Number" :required="true"/>
                    <TextInput v-model="form.bank_account_number" type="text"  autofocus :error="form.errors.get('bank_account_number') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('bank_account_number')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="benificiary_name" value="Benificiary Name"  :required="true"/>
                    <TextInput v-model="form.benificiary_name" type="text"   :error="form.errors.get('benificiary_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('benificiary_name')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                 <div class="mb-4" v-if="data.showIfsc" >
                    <InputLabel for="ifsc_id" value="Ifsc"  :required="true"/>
                    <ifsc-select  v-model="form.ifsc_id" :key="-1" :initials="data.ifscInitials" :selected="data.ifscSelected"></ifsc-select>
                    <InputError class="mt-2" :message="form.errors.get('ifsc_id')" />
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
