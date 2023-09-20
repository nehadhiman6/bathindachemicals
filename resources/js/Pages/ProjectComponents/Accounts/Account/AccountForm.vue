
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import AccountSubGroupSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSubGroupSelect.vue';
    import TypeMasterSelect from '@/Pages/ProjectComponents/SelectComponents/TypeMasterSelect.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import PayTermSelect from '@/Pages/ProjectComponents/SelectComponents/PayTermSelect.vue';
    import CitySelect from '@/Pages/ProjectComponents/SelectComponents/CitySelect.vue';
    import IfscSelect from '@/Pages/ProjectComponents/SelectComponents/IfscSelect.vue';
    import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
    import PartyCategorySelect from '@/Pages/ProjectComponents/SelectComponents/PartyCategorySelect.vue';

    import {    ref,  computed,  onBeforeMount,    reactive, nextTick} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','type','readonly']);
    const form = reactive( new Form({
        form_id: 0,
        name:"" ,
        print_name:"",
        party_cat_id:0,
        ac_sub_group_id:"",
        sub_group_id1:"",
        sub_group_id2:"",
        bill_wise:"Y",
        remarks:"",
        ac_code:'',
        add1:'',
        add2:'',
        add3:'',
        city_id:'',
        pincode:'',
        phone_no:'',
        email:'',
        pan_no:'',
        party_gst_status:'',
        gst_no:'',
        contact_person:'',
        contact_per_phone:'',
        trade_name:'',
        payment_term_id:'',
        credit_limit:'',
        tds_tcs:'',
        client_id:'',
        vendor_id:'',
        vat_no:'',
        cst_no:'',
        ifsc_id:'',
        account_no:'',
        beneficiary_name:'',
        msme_type:'',
        e_invoice_applicable:'',
        bank_name :'',
        branch_name:'',
        type:props.type,
        ledger_ac_id:0,
        active:'Y',
        branches:[]
    }));
    const data = reactive({ create_url:'accounts',accountSubGroupInitials:[],accountSubGroupSelected:[],
        partySubGroupInitials1:[],partySubGroupSelected1:[],partySubGroupInitials2:[],partySubGroupSelected2:[],
        cityInitials:[],citySelected:[],paymentTermInitials:[],paymentTermSelected:[],
        clientInitials:[],clientSelected:[],vendorInitials:[],vendorSelected:[],
        branchInitials:[],branchSelected:[],ifscInitials:[],ifscSelected:[], show:true,
        initialAccount:[], selectedAccount:[],selected_sub_group_name:'',partyCatInitials:[],partyCatSelected:[]

    });
    const pageTitle = computed(() => props.form_id > 0 ? (props.type == 'party'? 'Update  Account':'Update GL Account'): (props.type == 'party'? 'Add  Account':'Add GL Account'));
    const emit = defineEmits(['resetForm']);

    onBeforeMount(() => {
        if(props.form_id > 0){
            getAccount();
        }
    });
    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getAccount = () =>{
        axios.get(base_url.value+'/accounts/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let account = response.data.account;
                form.name = account.name;
                var account_detail = account.account_detail ? account.account_detail :null;
                copyProperties(account,form);
                if(props.type == 'party'){
                    copyProperties(account_detail,form);
                }
                form.form_id  = account.id;
                setSelectData(account, account_detail);
            }
        })
        .catch(function(error){
            console.log(error.response)
        });
    }
    const updateIfsc = (id, index, data) =>{
        if(data){
            form.bank_name = data.bank_name;
            form.branch_name = data.branch;
        }
        else{
            form.bank_name = "";
            form.branch_name = "";
        }

    }
    const updateBranch = (id,index,data) =>{
        form.branches = id;
    }

    const getUrl =()=>{
        if(props.type == 'other'){
            return 'account-sub-groups-others/filtered'
        }
        return  'account-sub-groups/filtered'
    }

    const setSelectData = (account, account_detail) =>{
        try{
            if(account.account_branches){
                account.account_branches.forEach(element => {
                    if(element.branch){
                        data.branchInitials.push({'text':element.branch.name,'id':element.branch.id});
                        data.branchSelected.push(element.branch.id);
                        form.branches.push(element.branch.id);
                    }
                });
            }
            if(account.account_sub_group){
                data.accountSubGroupInitials=[{'text':account.account_sub_group.sub_group_name,'id':account.account_sub_group.id}];
                data.accountSubGroupSelected =[account.account_sub_group.id];
            }
            if(account.sub_group_one){
                data.partySubGroupInitials1=[{'text':account.sub_group_one.name,'id':account.sub_group_one.id}];
                data.partySubGroupSelected1 =[account.sub_group_one.id];
            }
            if(account.sub_group_two){
                data.partySubGroupInitials2=[{'text':account.sub_group_two.name,'id':account.sub_group_two.id}];
                data.partySubGroupSelected2 =[account.sub_group_two.id];
            }
            if(account_detail && account_detail.city){
                data.cityInitials=[{'text':account_detail.city.name,'id':account_detail.city.id}];
                data.citySelected =[account_detail.city.id];
            }
            if(account_detail && account_detail.pay_term){
                data.paymentTermInitials=[{'text':account_detail.pay_term.name,'id':account_detail.pay_term.id}];
                data.paymentTermSelected =[account_detail.pay_term.id];
            }
            if(account_detail && account_detail.vendor){
                data.vendorInitials=[{'text':account_detail.vendor.name,'id':account_detail.vendor.id}];
                data.vendorSelected =[account_detail.vendor.id];
            }
            if(account_detail && account_detail.client){
                data.clientInitials=[{'text':account_detail.client.name,'id':account_detail.client.id}];
                data.clientSelected =[account_detail.client.id];
            }
            if(account_detail && account_detail.ifsc){
                data.ifscInitials=[{'text':account_detail.ifsc.ifsc_code,'id':account_detail.ifsc.id}];
                data.ifscSelected =[account_detail.ifsc.id];
                if(account_detail.ifsc.bank){
                    form.bank_name = account_detail.ifsc.bank.name;
                    form.branch = account_detail.ifsc.branch;
                }
            }
            if(account_detail && account_detail.account_ledger){
                data.initialAccount = [{'text':account_detail.account_ledger.name,'id':account_detail.account_ledger.id}];
                data.selectedAccount =[account_detail.account_ledger.id];
            }
             if(account_detail && account_detail.party_category){
                data.partyCatInitials = [{'text':account_detail.party_category.name,'id':account_detail.party_category.id}];
                data.partyCatSelected =[account_detail.party_category.id];
            }
            nextTick(()=>{
                refreshComponent(data,'show');
            })
        }
        catch(error){
            console.log(error);
        }

    }

    const updateAccountGroup = (id,index,ac_data)=>{
       if(ac_data){
            data.selected_sub_group_name = ac_data.sub_group_name;
        }
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'name':
                return props.readonly;
            case 'print_name':
                return props.readonly;
            case 'ac_sub_group_id':
                return props.readonly;
            case 'sub_group_id1':
                return props.readonly;
            case 'sub_group_id2':
                return props.readonly;
            case 'add1':
                return props.readonly;
            case 'add2':
                return props.readonly;
            case 'add3':
                return props.readonly;
            case 'city_id':
                return props.readonly;
            case 'pincode':
                return props.readonly;
            case 'phone_no':
                return props.readonly;
            case 'email':
                return props.readonly;
            case 'pan_no':
                return props.readonly;
            case 'party_gst_status':
                return props.readonly;
            case 'gst_no':
                return props.readonly;
            case 'contact_person':
                return props.readonly;
            case 'contact_per_phone':
                return props.readonly;
            case 'trade_name':
                return props.readonly;
            case 'payment_term_id':
                return props.readonly;
            case 'credit_limit':
                return props.readonly;
            case 'tds_tcs':
                return props.readonly;
            case 'ledger_ac_id':
                return props.readonly;
            case 'vendor_id':
                return props.readonly;
            case 'branches':
                return props.readonly;
            case 'vat_no':
                return props.readonly;
            case 'cst_no':
                return props.readonly;
            case 'msme_type':
                return props.readonly;
            case 'bill_wise':
                return props.readonly;
            case 'e_invoice_applicable':
                return props.readonly;
            case 'active':
                return props.readonly;
            case 'party_cat_id':
                return props.readonly;
            case 'remarks':
                return props.readonly;
            case 'beneficiary_name':
                return props.readonly;
            case 'account_no':
                return props.readonly;
            case 'ifsc_id':
                return props.readonly;
            case 'client_id':
                return props.readonly;
            case 'bank_name':
                return true;
            case 'branch_name':
                return true;
            default:
                return false;
        }
    }
</script>
<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div :class="props.type == 'party' ? 'w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0' : 'w-full max-w-full px-3 shrink-0 md:w-12/12 md:flex-0'">
                <div class="mb-1">
                    <InputLabel for="name" value="Name" :required="true"/>
                    <TextInput v-model="form.name" type="text" :disabled="isDisabled('name')" required  autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="print_name" value="Print Name"  />
                    <TextInput v-model="form.print_name" type="text" :disabled="isDisabled('print_name')" required  :error="form.errors.get('print_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('print_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                <div class="mb-1" v-if="data.show">
                    <!-- Sub groups where Parties applicable is Yes  -->
                    <InputLabel for="name" value="Account Sub Group" :required="true"/>
                    <account-sub-group-select :disabled="isDisabled('ac_sub_group_id')" @updateAccountGroup="updateAccountGroup"  :initials="data.accountSubGroupInitials" :selected="data.accountSubGroupSelected" v-model="form.ac_sub_group_id" :party_applicable="props.type =='other'?'N':'Y'" :error="form.errors.get('ac_sub_group_id') ? true :false" ></account-sub-group-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_sub_group_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="name" value="Party Sub Group 1 "/>
                    <type-master-select :disabled="isDisabled('sub_group_id1')" :initials="data.partySubGroupInitials1" :selected="data.partySubGroupSelected1" :type="props.type == 'party' ? 'party_sub_groups':'gl_sub_groups'" placeholder="Party Sub Group" v-model="form.sub_group_id1" :error="form.errors.get('sub_group_id1') ? true :false"></type-master-select>
                    <InputError class="mt-2" :message="form.errors.get('sub_group_id1')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="name" value="Party  Sub Group 2" />
                    <type-master-select :disabled="isDisabled('sub_group_id2')" :initials="data.partySubGroupInitials2" :selected="data.partySubGroupSelected2"  :type="props.type == 'party' ?'party_sub_groups' :'gl_sub_groups'"  placeholder="Party Sub Group" v-model="form.sub_group_id2" :error="form.errors.get('sub_group_id2') ? true :false"></type-master-select>
                    <InputError class="mt-2" :message="form.errors.get('sub_group_id2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="add1" value="Address 1" :required="true"/>
                    <TextAreaInput v-model="form.add1" :disabled="isDisabled('add1')" type="text" required  :error="form.errors.get('add1') ? true :false"></TextAreaInput>
                    <InputError class="mt-2" :message="form.errors.get('add1')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="add2" value="Address 2"/>
                    <TextAreaInput v-model="form.add2" type="text"  :disabled="isDisabled('add2')" required  :error="form.errors.get('add2') ? true :false"></TextAreaInput>
                    <InputError class="mt-2" :message="form.errors.get('add2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="add3" value="Address 3"/>
                    <TextAreaInput v-model="form.add3" type="text" required :disabled="isDisabled('add3')" :error="form.errors.get('add3') ? true :false"></TextAreaInput>
                    <InputError class="mt-2" :message="form.errors.get('add3')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-3" v-if="data.show">
                    <InputLabel for="city_id" value="City" :required="true"/>
                    <city-select :initials="data.cityInitials" :selected="data.citySelected" :disabled="isDisabled('add3')" v-model="form.city_id" index="1"  :error="form.errors.get('ac_sub_group_id') ? true :false"></city-select>
                    <!-- <TextAreaInput v-model="form.city_id" type="text" required  :error="form.errors.get('city_id') ? true :false"></TextAreaInput> -->
                    <InputError class="mt-2" :message="form.errors.get('city_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="pincode" value="Pincode" :required="true"/>
                    <TextInput v-model="form.pincode" type="text" required :disabled="isDisabled('pincode')"  :error="form.errors.get('pincode') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('pincode')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="phone_no" value="Phone No." />
                    <TextInput v-model="form.phone_no" type="text" required :disabled="isDisabled('phone_no')"  :error="form.errors.get('phone_no') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('phone_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="email" value="Email" />
                    <TextInput v-model="form.email" type="text" :disabled="isDisabled('email')"  :error="form.errors.get('email') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('email')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="pan_no" value="PAN no." />
                    <TextInput v-model="form.pan_no" type="text" :disabled="isDisabled('pan_no')"  :error="form.errors.get('pan_no') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('pan_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="party_gst_status" value="Party GST Status" :required="true"/>
                    <SelectInput  v-model="form.party_gst_status" :options="[
                        {'id':'Registerd','text':'Registerd'},
                        {'id':'Unregistered','text':'Unregistered'},
                        {'id':'Composition','text':'Composition'},
                    ]" :error="form.errors.get('party_gst_status') ? true :false" :disabled="isDisabled('party_gst_status')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('party_gst_status')" />
                </div>
            </div>


            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="gst_no" value="GST No."/>
                    <TextInput v-model="form.gst_no" type="text" :disabled="isDisabled('gst_no')"  :error="form.errors.get('gst_no') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('gst_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="contact_person" value="Contact Person"/>
                    <TextInput v-model="form.contact_person" type="text" required :disabled="isDisabled('contact_person')" :error="form.errors.get('contact_person') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('contact_person')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="contact_per_phone" value="Contact Person Phone"/>
                    <TextInput v-model="form.contact_per_phone" type="text" :disabled="isDisabled('contact_per_phone')" required  :error="form.errors.get('contact_per_phone') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('contact_per_phone')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="trade_name" value="Trade Name"/>
                    <TextInput v-model="form.trade_name" type="text" required :disabled="isDisabled('trade_name')" :error="form.errors.get('trade_name') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('trade_name')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="payment_term_id" value="Payment Term"/>
                    <pay-term-select :initials="data.paymentTermInitials" :selected="data.paymentTermSelected" :disabled="isDisabled('payment_term_id')" v-model="form.payment_term_id"  :error="form.errors.get('payment_term_id') ? true :false"> </pay-term-select>
                    <InputError class="mt-2" :message="form.errors.get('payment_term_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0 " v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="credit_limit" value="Credit Limit" :required="data.selected_sub_group_name == 'Trade Receivables' ? true:false"/>
                    <TextInput v-model="form.credit_limit" type="text" :disabled="isDisabled('credit_limit')" required  :error="form.errors.get('credit_limit') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('credit_limit')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="tds_tcs" value="TDS /TCS Applicability"/>
                      <SelectInput v-model="form.tds_tcs" :options ="[{'id':'TDS','text':'TDS'},{'id':'TCS','text':'TCS'},{'id':'Higher TCS','text':'Higher TCS'},{'id':'Higher TDS','text':'Higher TDS'}]"  :error="form.errors.get('tds_tcs') ? true :false" :disabled="isDisabled('tds_tcs')" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('tds_tcs')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="ledger_ac_id" value="Ledger Account"/>
                    <account-select type="party" index="ledger_ac" :disabled="isDisabled('ledger_ac_id')" v-model="form.ledger_ac_id" :initials="data.initialAccount" :selected="data.selectedAccount"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('ledger_ac_id')" />
                </div>
            </div>

              <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="client_id" value="Client"/>
                     <type-master-select :initials="data.clientInitials" :disabled="isDisabled('client_id')"  :selected="data.clientSelected"  type="clients" :index="'client_-1'" v-model="form.client_id" :error="form.errors.get('client_id') ? true :false"></type-master-select>
                    <InputError class="mt-2" :message="form.errors.get('client_id')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="vendor_id" value="Vendor"/>
                     <type-master-select :initials="data.vendorInitials" :selected="data.vendorSelected" :disabled="isDisabled('vendor_id')"  type="vendors" :index="'vendor_-1'" placeholder="Select Vendor" v-model="form.vendor_id" :error="form.errors.get('vendor_id') ? true :false"></type-master-select>
                    <InputError class="mt-2" :message="form.errors.get('vendor_id')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-3/4 md:flex-0" >
                <div class="mb-1" v-if="data.show">
                    <InputLabel for="branches" value="Branches"/>
                    <branch-select :initials="data.branchInitials" :selected="data.branchSelected" :disabled="isDisabled('branches')" :multiple="true" @updateBranch="updateBranch"></branch-select>
                     <!-- <type-master-select type="vendors" :index="'vendor_-1'" placeholder="Select Vendor" v-model="form.branches" :error="form.errors.get('branches') ? true :false"></type-master-select> -->
                    <InputError class="mt-2" :message="form.errors.get('branches')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1" >
                    <InputLabel for="vat_no" value="VAT No."/>
                    <TextInput v-model="form.vat_no" type="text" :disabled="isDisabled('vat_no')" required  :error="form.errors.get('vat_no') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('vat_no')" />
                </div>
            </div>
               <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="cst_no" value="CST No."/>
                    <TextInput v-model="form.cst_no" type="text" required :disabled="isDisabled('cst_no')" :error="form.errors.get('cst_no') ? true :false"></TextInput>
                    <InputError class="mt-2" :message="form.errors.get('cst_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="msme_type" value="MSME Type"/>
                    <SelectInput v-model="form.msme_type" type="text" :disabled="isDisabled('msme_type')" required :options="[ {'id':'','text':'Select'},{'id':'micro','text':'Micro'},{'id':'small','text':'Small'},{'id':'medium','text':'Medium'}]" :error="form.errors.get('msme_type') ? true :false"></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('msme_type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="bill_wise" value="Bill-Wise" :required="true"/>
                    <SelectInput  v-model="form.bill_wise" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('bill_wise') ? true :false" :disabled="isDisabled('bill_wise')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('bill_wise')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party'">
                <div class="mb-1">
                    <InputLabel for="e_invoice_applicable" value="E-Invoice Applicable" :required="true"/>
                    <SelectInput  v-model="form.e_invoice_applicable" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('e_invoice_applicable') ? true :false" :disabled="isDisabled('e_invoice_applicable')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('e_invoice_applicable')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="active" value="Status" :required="true"/>
                    <SelectInput  v-model="form.active" :options="[
                        {'id':'Y','text':'Active'},
                        {'id':'N','text':'Inactive'},
                    ]" :error="form.errors.get('active') ? true :false" :disabled="isDisabled('active')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('active')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0" v-if="props.type == 'party' ">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="party_cat_id" value="Party Category"/>
                    <party-category-select :initials="data.partyCatInitials" :disabled="isDisabled('party_cat_id')"  :selected="data.partyCatSelected" v-model="form.party_cat_id"  :error="form.errors.get('party_cat_id') ? true :false"></party-category-select>
                    <InputError class="mt-2" :message="form.errors.get('party_cat_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-2/4 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="remarks" value="Remarks" />
                    <TextAreaInput v-model="form.remarks" type="text" :disabled="isDisabled('remarks')" required  :error="form.errors.get('remarks') ? true :false"></TextAreaInput>
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
            <!-- bank details -->
        </div>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1" v-if="props.type == 'party'">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Bank Details</legend>
                <div class="flex flex-wrap -mx-3">
                     <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="beneficiary_name" value="Beneficiary Name"/>
                        <TextInput v-model="form.beneficiary_name" type="text" required :disabled="isDisabled('beneficiary_name')" :error="form.errors.get('beneficiary_name') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('beneficiary_name')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="account_no" value="Account No."/>
                        <TextInput v-model="form.account_no" type="text" required  :error="form.errors.get('account_no') ? true :false" :disabled="isDisabled('account_no')"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('account_no')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                    <div class="mb-1" v-if="data.show">
                        <InputLabel for="ifsc_id" value="IFSC"/>
                        <ifsc-select  :initials="data.ifscInitials" :selected="data.ifscSelected" v-model="form.ifsc_id" :disabled="isDisabled('ifsc_id')" :error="form.errors.get('ifsc_id') ? true :false" @updateIfsc="updateIfsc"></ifsc-select>
                        <InputError class="mt-2" :message="form.errors.get('ifsc_id')" />
                    </div>
                </div>
                  <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="bank_name" value="Bank"/>
                        <TextInput v-model="form.bank_name" type="text" :disabled="isDisabled('bank_name')"   :error="form.errors.get('bank_name') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('bank_name')" />
                    </div>
                </div>
                   <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                    <div class="mb-1">
                        <InputLabel for="branch_name" value="Branch"/>
                        <TextInput v-model="form.branch_name" type="text" disabled  :error="form.errors.get('branch_name') ? true :false"></TextInput>
                        <InputError class="mt-2" :message="form.errors.get('branch_name')" />
                    </div>
                </div>
                </div>

        </fieldset>
         <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                <div class="mb-1">
                    <ButtonComp @buttonClicked="submitForm" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                    <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
                </div>
            </div>
         </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
