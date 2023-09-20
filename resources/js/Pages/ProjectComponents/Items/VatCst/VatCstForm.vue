
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onBeforeMount,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent,copyProperties} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive(new Form({
            form_id: 0,
            name:'',
            vat_rate:'',
            cst_rate:'',
            sur_on_vat:'',
            sur_on_cst:'',
            vat_ac_id:'',
            cst_ac_id:'',
            sur_vat_ac_id:'',
            sur_cst_ac_id:'',
        })
    );
    const data = reactive({ create_url:'vat-cst',
        vat_selected: [],
        vat_initials: [],
        cst_selected: [],
        cst_initials: [],
        sur_vat_selected: [],
        sur_vat_initials: [],
        sur_cst_selected: [],
        sur_cst_initials: [],
        show:true
    });
    const pageTitle = computed(() =>props.form_id > 0 ? 'Update VAT/CST':'Add VAT/CST');
    const emit = defineEmits(['resetForm']);

    onBeforeMount(() => {
        if(props.form_id > 0){
            getVatCst();
        }
    });
    const submitForm = () =>{
        var url = data.create_url;
        form['postForm'](url)
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

    const setAccountsSelected = (vat_cst) =>{
        if (vat_cst.vat_account) {
            data.vat_initials = [{
                'text': vat_cst.vat_account.name,
                'id': vat_cst.vat_account.id
            }];
            data.vat_selected = [vat_cst.vat_account.id];
        }
        if (vat_cst.cst_account) {
            data.cst_initials = [{
                'text': vat_cst.cst_account.name,
                'id': vat_cst.cst_account.id
            }];
            data.cst_selected = [vat_cst.cst_account.id];
        }
        if (vat_cst.surcharge_vat_account) {
            data.sur_vat_initials = [{
                'text': vat_cst.surcharge_vat_account.name,
                'id': vat_cst.surcharge_vat_account.id
            }];
            data.sur_vat_selected = [vat_cst.surcharge_vat_account.id];
        }
        if (vat_cst.surcharge_cst_account) {
            data.sur_cst_initials = [{
                'text': vat_cst.surcharge_cst_account.name,
                'id': vat_cst.surcharge_cst_account.id
            }];
            data.sur_cst_selected = [vat_cst.surcharge_cst_account.id];
        }
        refreshComponent(data,'show');
    }

    const getVatCst = () =>{
        axios.get(base_url.value+'/vat-cst/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let vat_cst = response.data.vat_cst;
                form.name = vat_cst.name;
                setAccountsSelected(vat_cst);
                form.form_id =vat_cst.id;
                copyProperties(vat_cst,form);
            }
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="vat_rate" value="VAT Rate" />
                    <TextInput v-model="form.vat_rate" type="text" required  :error="form.errors.get('vat_rate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vat_rate')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="cst_rate" value="CST Rate" />
                    <TextInput v-model="form.cst_rate" type="text" required  :error="form.errors.get('cst_rate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('cst_rate')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sur_on_vat" value="Surcharge On VAT" />
                    <TextInput v-model="form.sur_on_vat" type="text" required  :error="form.errors.get('sur_on_vat') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sur_on_vat')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sur_on_cst" value="Surcharge on CST" />
                    <TextInput v-model="form.sur_on_cst" type="text" required  :error="form.errors.get('sur_on_cst') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sur_on_cst')" />
                </div>
            </div>
        </div>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1" >
            <legend class="text-base rounded font-semibold bg-primary text-white px-3">VAT Accounts</legend>
            <div class="flex flex-wrap  -mx-3">
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="vat_ac_id" value="VAT Account"/>
                        <account-select index="vat_ac_id" type="gl"  :error="form.errors.get('vat_ac_id') ? true :false"  v-model="form.vat_ac_id" :initials="data.vat_initials" :selected="data.vat_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('vat_ac_id')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="sur_vat_ac_id" value="VAT Surcharge Account"/>
                        <account-select index="sur_vat_ac_id" type="gl"  :error="form.errors.get('sur_vat_ac_id') ? true :false"  v-model="form.sur_vat_ac_id" :initials="data.sur_vat_initials" :selected="data.sur_vat_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('sur_vat_ac_id')" />
                    </div>
                </div>
            </div>
        </fieldset>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1" >
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">CST Accounts</legend>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                        <div class="mb-1" v-if="data.show">
                            <InputLabel for="cst_ac_id" value="CST Account"/>
                            <account-select index="cst_ac_id" type="gl"  :error="form.errors.get('cst_ac_id') ? true :false"  v-model="form.cst_ac_id" :initials="data.vat_initials" :selected="data.vat_selected"></account-select>
                            <InputError class="mt-2" :message="form.errors.get('cst_ac_id')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                        <div class="mb-1" v-if="data.show">
                            <InputLabel for="sur_cst_ac_id" value="CST Surcharge Account"/>
                            <account-select index="sur_cst_ac_id" type="gl"  :error="form.errors.get('sur_cst_ac_id') ? true :false"  v-model="form.sur_cst_ac_id" :initials="data.sur_cst_initials" :selected="data.sur_cst_selected"></account-select>
                            <InputError class="mt-2" :message="form.errors.get('sur_cst_ac_id')" />
                        </div>
                    </div>
            </div>

        </fieldset>
        <div class="flex flex-wrap items-end -mx-3">
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
