
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','title','create_url']);
    const form = reactive( new Form({ form_id: 0,name:"" ,type:"",limit:"",accounts:[]}));
    const data = reactive({accountInitials:[],accountSelected:[],show:true});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update ' + props.title:'Add ' + props.title);
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        form.type = props.create_url;
        if(props.form_id > 0){
            getTypeMaster();
        }
    });
    const submitForm = () =>{
        form['postForm'](props.create_url.replace(/_/g, "-"))
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
    const getTypeMaster = () =>{
        axios.get(base_url.value+'/'+props.create_url.replace(/_/g, "-")+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let type_master = response.data.type_master;
                form.name = type_master.name;
                form.limit = type_master.limit;
                type_master.limit_accounts.forEach(element => {
                    if(element.account){
                        form.accounts.push(element.account.id);
                        data.accountInitials.push({'id':element.account.id,'text':element.account.name});
                        data.accountSelected.push(element.account.id);
                    }
                });
                refreshComponent(data,'show');
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }

    const updateAccount = (ids)=>{
        form.accounts = ids;
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
             <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0" v-if="props.create_url == 'limit_sub_groups'">
                <div class="mb-4">
                    <InputLabel for="limit" value="Limit" />
                    <TextInput v-model="form.limit" type="text" required  :error="form.errors.get('limit') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('limit')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0" v-if="props.create_url == 'limit_sub_groups'">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="limit" value="Accounts" />
                    <account-select :multiple="true" url="limit-accounts/filtered" type="party" :initials="data.accountInitials" :selected="data.accountSelected" @updateAccount="updateAccount" :error="form.errors.get('accounts') ? true :false" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('accounts')" />
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
