
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onBeforeMount,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','changeRate']);
    const form = reactive(new Form({
            form_id: 0,
            gst_id: 0,
            name: "",
            wef_date: BCL.today,
            cgst: "",
            sgst: "",
            igst: "",
            cess: "",
            cgst_account: {
                acid_output: "",
                acid_input: "",
            },
            sgst_account: {
                acid_output: "",
                acid_input: "",
            },
            igst_account: {
                acid_output: "",
                acid_input: "",
            },
            cess_account: {
                acid_output: "",
                acid_input: "",
            },
            rate_on: 0,
        })
    );
    const data = reactive({ create_url:'gsts',
        ai_cgst_selected: [],
        ai_cgst_initials: [],
        ao_cgst_selected: [],
        ao_cgst_initials: [],
        ai_sgst_selected: [],
        ai_sgst_initials: [],
        ao_sgst_selected: [],
        ao_sgst_initials: [],
        ai_igst_selected: [],
        ai_igst_initials: [],
        ao_igst_selected: [],
        ao_igst_initials: [],
        ai_cess_selected: [],
        ai_cess_initials: [],
        ao_cess_selected: [],
        ao_cess_initials: [],
        show:true
    });
    const pageTitle = computed(() => props.changeRate == true ? 'GST Change Rates' :(props.form_id > 0 ? 'Update  Gst':'Add Gst'));
    const emit = defineEmits(['resetForm']);

    onBeforeMount(() => {
        if(props.form_id > 0){
            getGst();
        }
    });
    const submitForm = () =>{
        var url = data.create_url;
        if(props.changeRate == true){
            url = data.create_url+'/'+form.gst_id+ '/rate'
        }
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

    const setAccountsSelected = (gst) =>{
        try{
            gst.types.forEach(function(element) {
                element.details.forEach(function(ele) {
                    if (ele.name == "cgst") {
                        form.cgst = ele.rate;
                        if (ele.account_input) {
                            data.ai_cgst_initials = [{
                                'text': ele.account_input.name,
                                'id': ele.account_input.id
                            }];
                            data.ai_cgst_selected = [ele.account_input.id];
                            form.cgst_account.acid_input = ele.account_input.id;
                        }
                        if (ele.account_output) {
                            data.ao_cgst_initials = [{
                                'text': ele.account_output.name,
                                'id': ele.account_output.id
                            }];
                            data.ao_cgst_selected = [ele.account_output.id];
                            form.cgst_account.acid_output = ele.account_output.id;
                        }
                    } else if (ele.name == "sgst") {
                        form.sgst = ele.rate;
                        if (ele.account_input) {
                            data.ai_sgst_initials = [{
                                'text': ele.account_input.name,
                                'id': ele.account_input.id
                            }];
                            data.ai_sgst_selected = [ele.account_input.id];
                            form.sgst_account.acid_input = ele.account_input.id;
                        }
                        if (ele.account_output) {
                            data.ao_sgst_initials = [{
                                'text': ele.account_output.name,
                                'id': ele.account_output.id
                            }];
                            data.ao_sgst_selected = [ele.account_output.id];
                            form.sgst_account.acid_output = ele.account_output.id;
                        }
                    } else if (ele.name == "igst") {
                        form.igst = ele.rate;
                        if (ele.account_input) {
                            data.ai_igst_initials = [{
                                'text': ele.account_input.name,
                                'id': ele.account_input.id
                            }];
                            data.ai_igst_selected = [ele.account_input.id];
                            form.igst_account.acid_input = ele.account_input.id;
                        }
                        if (ele.account_output) {
                            data.ao_igst_initials = [{
                                'text': ele.account_output.name,
                                'id': ele.account_output.id
                            }];
                            data.ao_igst_selected = [ele.account_output.id];
                            form.igst_account.acid_output = ele.account_output.id;
                        }
                    } else if (ele.name == "cess") {
                        if (ele.account_input) {
                            data.ai_cess_initials = [{
                                'text': ele.account_input.name,
                                'id': ele.account_input.id
                            }];
                            data.ai_cess_selected = [ele.account_input.id];
                            form.cess_account.acid_input = ele.account_input.id;
                        }
                        if (ele.account_output) {
                            data.ao_cess_initials = [{
                                'text': ele.account_output.name,
                                'id': ele.account_output.id
                            }];
                            data.ao_cess_selected = [ele.account_output.id];
                            form.cess_account.acid_output = ele.account_output.id;
                        }
                        form.cess = ele.rate;
                        form.rate_on = ele.rate_on;
                    }
                });
                form.copyData = JSON.parse(JSON.stringify(form.data()));
            });
            if (form.cess == "") {
                form.cess = 0;
            }
        }
        catch(error){
            console.log(error)
        }

        refreshComponent(data,'show');


    }
    const getGst = () =>{
        axios.get(base_url.value+'/gsts/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let gst = response.data.gst;
                form.name = gst.name;
                setAccountsSelected(gst);
                form.gst_id =gst.id;
            }
            if(props.changeRate == false){
                form.form_id  = props.form_id;
            }
        })
        .catch(function(error){
        });
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="wef_date" value="Date"/>
                    <date-picker v-model="form.wef_date" class-name="wef_date" ></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('wef_date')" />
                </div>
            </div>

        </div>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1" >
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">CGST Details</legend>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="cgst" value="CGST Rate"/>
                        <TextInput v-model="form.cgst" type="text" required  :error="form.errors.get('cgst') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('cgst')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="cgst_account.acid_input" value="Account Input"/>
                        <account-select index="cgst_account_input" type="gl"  :error="form.errors.get('cgst_account.acid_input') ? true :false"  v-model="form.cgst_account.acid_input" :initials="data.ai_cgst_initials" :selected="data.ai_cgst_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('cgst_account.acid_input')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="cgst_account.acid_output" value="Account Output"/>
                         <account-select  type="gl"  :error="form.errors.get('cgst_account.acid_output') ? true :false"  index="cgst_account_output"  v-model="form.cgst_account.acid_output" :initials="data.ao_cgst_initials" :selected="data.ao_cgst_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('cgst_account.acid_output')" />
                    </div>
                </div>
            </div>

        </fieldset>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1" >
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">SGST Details</legend>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="sgst" value="SGST Rate"/>
                        <TextInput v-model="form.sgst" type="text" required  :error="form.errors.get('sgst') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('sgst')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="sgst_account.acid_input" value="Account Input"/>
                        <account-select  type="gl"  :error="form.errors.get('sgst_account.acid_input') ? true :false"  index="sgst_account_input" v-model="form.sgst_account.acid_input" :initials="data.ai_sgst_initials" :selected="data.ai_sgst_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('sgst_account.acid_input')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="sgst_account.acid_output" value="Account Output"/>
                         <account-select  type="gl"  :error="form.errors.get('sgst_account.acid_output') ? true :false" index="sgst_account_output" v-model="form.sgst_account.acid_output" :initials="data.ao_sgst_initials" :selected="data.ao_sgst_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('sgst_account.acid_output')" />
                    </div>
                </div>
            </div>

        </fieldset>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1" >
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">IGST Details</legend>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="igst" value="IGST Rate"/>
                        <TextInput v-model="form.igst" type="text" required  :error="form.errors.get('igst') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('igst')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="igst_account.acid_input" value="Account Input"/>
                        <account-select  type="gl"  :error="form.errors.get('igst_account.acid_input') ? true :false" index="igst_account_input" v-model="form.igst_account.acid_input" :initials="data.ai_igst_initials" :selected="data.ai_igst_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('igst_account.acid_input')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="igst_account.acid_output" value="Account Output"/>
                         <account-select   type="gl"  :error="form.errors.get('igst_account.acid_output') ? true :false" index="igst_account_output" v-model="form.igst_account.acid_output" :initials="data.ao_igst_initials" :selected="data.ao_igst_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('igst_account.acid_output')" />
                    </div>
                </div>
            </div>

        </fieldset>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1" >
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">CESS Details</legend>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                        <div class="mb-1">
                            <InputLabel for="cess" value="CESS Rate"/>
                            <TextInput v-model="form.cess" type="text" required  :error="form.errors.get('cess') ? true :false"></TextInput>
                            <InputError class="mt-2" :message="form.errors.get('cess')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                        <div class="mb-1">
                            <InputLabel for="rate_on" value="CESS On"/>
                            <SelectInput  v-model="form.rate_on" :options="[
                                    {'id':'','text':'Select'},
                                    {'id':'amt','text':'AMOUNT'},
                                    {'id':'qty','text':'QUANTITY'},
                                ]" :error="form.errors.get('rate_on') ? true :false"> </SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('rate_on')" />
                        </div>
                    </div>



                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="cess_account.acid_input" value="Account Input"/>
                        <account-select  type="gl"  :error="form.errors.get('cess_account.acid_input') ? true :false" index="cess_account_input"  v-model="form.cess_account.acid_input"  :initials="data.ai_cess_initials" :selected="data.ai_cess_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('cess_account.acid_input')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="cess_account.acid_output" value="Account Output"/>
                         <account-select  type="gl"  :error="form.errors.get('cess_account.acid_output') ? true :false" index="cess_account_output" v-model="form.cess_account.acid_output"  :initials="data.ao_cess_initials" :selected="data.ao_cess_selected"></account-select>
                        <InputError class="mt-2" :message="form.errors.get('cess_account.acid_output')" />
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
