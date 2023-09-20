
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
    const props = defineProps(['company_id','company_name','form_id']);
    const form = reactive( new Form({
            form_id: 0,
            type:'N',
            name:'',
            print_name:'',
            address_1:'',
            address_2:'',
            address_3:'',
            city_id:'',
            pincode:'',
            phone:'',
            fax:'',
            email:'',
            tan:'',
            tin:'',
            vat:'',
            fssai:'',
            account_id:'',
            remarks:'',
            benificiary_name:'',
            bank_account_number:'',
            ifsc_id:'',

            company_id:props.company_id
    }));
    const data = reactive({ create_url:'branches',cityInitials:[],citySelected:[],ifscInitials:[],ifscSelected:[] ,showCity:true,showIfsc:true,showAccount:true});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Branch':'Add Branch');
    // const district_options = computed(() => {
    //     let array = (props.branches);
    //     array.forEach(arr => {
    //         arr.text = arr.name;
    //     });
    //     return array;
    // });

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        form.form_id = props.form_id;
        if(props.form_id > 0){
            getBranch();
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
    const getBranch = () =>{
        axios.get(base_url.value+'/'+data.create_url+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let branch = response.data.branch;
                form.name = branch.name;
                copyProperties(branch,form);
                if(branch.city){
                    data.cityInitials=[{'id':branch.city.id, 'text':branch.city.name}];
                    data.citySelected=[branch.city.id];
                }
                if(branch.ifsc){
                    data.ifscInitials=[{'id':branch.ifsc.id, 'text':branch.ifsc.ifsc_code}];
                    data.ifscSelected=[branch.ifsc.id];
                }
                if(branch.account){
                    data.accountInitials=[{'id':branch.account.id, 'text':branch.account.name}];
                    data.accountSelected=[branch.account.id];
                }
                refreshComponent(data,'showCity');
                refreshComponent(data,'showIfsc');
                refreshComponent(data,'showAccount');

                // form.state_code = state.state_code;
                // form.country_id = state.country_id;
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-2/5 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" :required="true"/>
                    <TextInput v-model="form.name" type="text"  autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-2/5 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="print_name" value="Print Name" />
                    <TextInput v-model="form.print_name" type="text"   :error="form.errors.get('print_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('print_name')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="type" value="Type" :required="true"/>
                     <SelectInput v-model="form.type" :options ="[
                        {'id':'N','text':'Normal'},
                        {'id':'D','text':'Distillery'},
                     ]" autofocus :error="form.errors.get('type') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="address_1" value="Address line 1" />
                    <TextAreaInput v-model="form.address_1" type="text"  :error="form.errors.get('address_1') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('address_1')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="address_2" value="Address line 2" />
                    <TextAreaInput v-model="form.address_2" type="text"  :error="form.errors.get('address_2') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('address_2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="address_3" value="Address line 3" />
                    <TextAreaInput v-model="form.address_3" type="text"  :error="form.errors.get('address_3') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('address_3')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                 <div class="mb-4" v-if="data.showCity" >
                    <InputLabel for="city_id" value="City" />
                    <city-select  v-model="form.city_id" :key="-1" :initials="data.cityInitials" :selected="data.citySelected" @updateCity="updateCity"></city-select>
                    <InputError class="mt-2" :message="form.errors.get('city_id')" />
                </div>
             </div>

            <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="city_id" value="City" />
                    <TextInput v-model="form.city_id" type="text"  :error="form.errors.get('city_id') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('city_id')" />
                </div>
            </div> -->
            <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="district_id" value="District" />
                    <TextInput v-model="form.district_id" type="text"  :error="form.errors.get('district_id') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('district_id')" />
                </div>
            </div> -->
            <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="state_id" value="State" />
                    <TextInput v-model="form.district_id" type="text"  :error="form.errors.get('state_id') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('state_id')" />
                </div>
            </div> -->
            <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="country_id" value="Country" />
                    <TextInput v-model="form.country_id" type="text"  :error="form.errors.get('country_id') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('country_id')" />
                </div>
            </div> -->

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="pincode" value="PIN Code" />
                    <TextInput v-model="form.pincode" type="text"  :error="form.errors.get('pincode') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('pincode')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="phone" value="Phone" />
                    <TextInput v-model="form.phone" type="text"  :error="form.errors.get('phone') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('phone')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="fax" value="Fax" />
                    <TextInput v-model="form.fax" type="text"  :error="form.errors.get('fax') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('fax')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput v-model="form.email" type="text"  :error="form.errors.get('email') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('email')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="tan" value="TAN Number" />
                    <TextInput v-model="form.tan" type="text"  :error="form.errors.get('tan') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('tan')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="tin" value="TIN Number" />
                    <TextInput v-model="form.tin" type="text"  :error="form.errors.get('tin') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('tin')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="vat" value="VAT Number" />
                    <TextInput v-model="form.vat" type="text"  :error="form.errors.get('vat') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vat')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="fssai" value="FSSAI Number" />
                    <TextInput v-model="form.fssai" type="text"  :error="form.errors.get('fssai') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('tin')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                <div class="mb-4" v-if="data.showAccount">
                    <InputLabel for="account_id" value="Account" />
                    <account-select v-model="form.account_id" :initials="data.accountInitials" :selected="data.accountSelected" :error="form.errors.get('account_id') ? true :false" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('account_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="remarks" value="Remarks" />
                    <TextInput v-model="form.remarks" type="text"  :error="form.errors.get('remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="benificiary_name" value="Benificiary Name" />
                    <TextInput v-model="form.benificiary_name" type="text"  :error="form.errors.get('benificiary_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('benificiary_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="bank_account_number" value="Bank Account Number" />
                    <TextInput v-model="form.bank_account_number" type="text"  :error="form.errors.get('bank_account_number') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('bank_account_number')" />
                </div>
            </div>
            <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="ifsc_id" value="IFSC Code" />
                    <TextInput v-model="form.ifsc_id" type="text"  :error="form.errors.get('ifsc_id') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('ifsc_id')" />
                </div>
            </div> -->

             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                 <div class="mb-4" v-if="data.showIfsc">
                    <InputLabel for="ifsc_id" value="Ifsc" />
                    <ifsc-select  v-model="form.ifsc_id" :key="-1" :initials="data.ifscInitials" :selected="data.ifscSelected" @updateIfsc="updateIfsc"></ifsc-select>
                    <InputError class="mt-2" :message="form.errors.get('ifsc_id')" />
                </div>
             </div>



            <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="district_id" value="District" />
                    <SelectInput v-model="form.district_id" :options ="district_options" autofocus :error="form.errors.get('district_id') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('district_id')" />
                </div>
            </div> -->

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
