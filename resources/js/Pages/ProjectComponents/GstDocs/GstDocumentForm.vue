
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin'
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import GstDocumentDetail from './GstDocumentDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';

    const { base_url,refreshComponent,copyProperties} = globalMixin();
    const props = defineProps(['form_id','title','create_url','readonly','gst_reasons','years']);
    const form = reactive( new Form({
        form_id: 0,
        vcode: '',
        doc_date: BCL.fyDate,
        doc_type: '',
        l_o_type: 'L',
        ac_gst_type: '',
        doc_no: '',
        doc_no_print: '',
        print_type: 'N',
        party_doc_no: '',
        party_doc_date: '',
        pur_id: '0',
        gst_reason_id: '0',
        cash_cr: 'R',
        pur_sale_type: 'P',
        rev_charges: 'N',
        ac_id: '',
        gr_no: '',
        gr_date: '',
        ref_no: '',
        ref_date: '',
        tpt_id: '',
        veh_no: '',
        add_less_other1_amount: 0,
        acid_add_less_other1: 0,
        add_less_other2_amount: 0,
        acid_add_less_other2: 0,
        add_oth_amt: 0,
        less_oth_amt: 0,
        remarks: '',
        gst_amt: '',
        round_off: 0,
        doc_amt: '',
        stock_entry: 'N',
        tds_per: '0',
        tds_on: '0',
        tds_amount: '0',
        tds_account: 0,
        tds_type: 'B',
        bill_no:'',
        fyear:'',
        // type: 'Other',
        uid: Utilities.getRandomNumber(),
        details: [],
        branch_id:'0',
        freight_type:'T',
        freight_amt:'0',
        eway_bill_date:'',
        eway_bill_no:'',
//
    }));
    const pageTitle = computed(() => props.form_id > 0 ? 'Update ' + props.title:'Add ' + props.title);
    const emit = defineEmits(['resetForm']);
    const data = reactive({show:true,
        account: [],
        accountshow: true,
        accountInitials: [],
        accountSelected: [],
        showAccounts: true,
        lessOneInitials: [],
        lessOneSelected: [],
        lessTwoInitials: [],
        lessTwoSelected: [],
        initialTransport: [],
        selectedTransport: [],
        tdsaccountInitials: [],
        tdsaccountSelected: [],
        showSelect: true,
        showAccountsLess: true,
        showAccountsLessOther: true,
        showDetail:true,

    });

    const getNewDetail = () => {
        return {
            id: 0,
            doc_id: '',
            s_no: '',
            acid_doc: '',
            item_id: '',
            item_desc: '',
            brand_id:'',
            packing_id:'',
            qty: '',
            weight: 0,
            length: 0,
            rate: '',
            disc_prec: '0',
            disc_amt: '0',
            net_disc: '',
            amount: '',
            hsn_code: '',
            gst_id: '',
            gst_adj_amt: '0',
            gst_amt: '',
            add_oth_amt: '',
            less_oth_amt: '',
            net_amt: '0',
            gst_name: '',
            rate_on:'Q',
            amount: '',
            gst_details: [],
            trans_gst_details: [],
            item: null,
            gst: {},
            doc_account: null,
            brand:null,
            packing:null,
            random: Utilities.getRandomNumber(),
        }
    }

    const title = computed(() => {
        if (props.create_url == "debit_note") {
                return "Debit Note"
        } else if (props.create_url == "credit_note") {
            return "Credit Note";
        }
    });

    const totalAmount = computed(() => {
        let total = 0;
        form.details.forEach(function (row) {
            total += Utilities.round(row.amount, 2);
        });
        return total;
    });

    const gstAmount = computed(() => {
        let gstTotal = 0;
        form.details.forEach(function (ele) {
            if (ele.item_id > 0 && ele.acid_doc > 0) {
                gstTotal += Utilities.round(ele.gst_amt);
            }
        });
        form.gst_amt = gstTotal;
        return Utilities.round(gstTotal);
    });

    const gst_reasons_options = computed(() => {
        let arr =[];
        let array = JSON.parse(JSON.stringify(props.gst_reasons));
        console.log(array);
        for (const key in array) {
            arr.push({'id':key,'text':array[key]})
        }
        return arr;
    });

    const years = computed(() => {
        let arr =[];
        for (const key in props.years) {
            arr.push({'id':key,'text':key})
        }
        return arr;
    });

    onMounted(() => {
        if (props.create_url == 'debit_note') {
            form.doc_type = 'DN';
            form.pur_sale_type = 'P';
        } else if (props.create_url == 'credit_note') {
            form.doc_type = 'CN';
            form.pur_sale_type = 'S';
        }

        if (props.form_id > 0) {
            editGstDocument(props.form_id);
        } else {
            if (form.details.length < 1) {
                form.details.push(getNewDetail());
            }
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
            if(error && error.errors && error.errors.msg)
                Utilities.showPopMessage(error.errors.msg[0],"Invalid Data","error",'6000',true);
        });
    }

    const tdsAccount = () => {
        updateBillAmount();
        if (form.tds_per == 0) {
            form.tds_account = 0;
        }
    }
    const getBillNo = (id) =>{
        axios.get(base_url.value+'/get-bill-no/', { params: {
            bill_no:form.bill_no,
            year:form.fyear,
            ac_id:form.ac_id
        }})
        .then(function(response){
            console.log(response);
            if(response.data.msg){
                console.log(response.data.msg);
                Utilities.showPopMessage(response.data.msg,"Invalid Data","error",3000);
                form.bill_no = '';
            }
        })
        .catch(function(error){
        });
    }
    const editGstDocument = (id) =>{
        axios.get(base_url.value+'/'+props.create_url.replace(/_/g, "-")+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let gst_doc = response.data.gst_doc
                    copyProperties(gst_doc,form);
                    if (gst_doc.account && gst_doc.account.account_yearly) {
                        if (gst_doc.account.account_yearly.local_outside == 'L') {
                            form.lst_cst = 'L';
                        } else {
                            form.lst_cst = 'C';
                        }
                    }
                    form.form_id = gst_doc.id;
                    form.details = [];
                    gst_doc.details.forEach(function (row) {
                        let blank_detail = getNewDetail();
                        copyProperties(row, blank_detail);
                        form.details.push(blank_detail);
                    })
                    if (gst_doc.transport) {
                        data.initialTransport = [{
                            'id': gst_doc.transport.id,
                            'text': gst_doc.transport.name
                        }];
                        data.selectedTransport = [gst_doc.transport.id];
                    }

                        if (gst_doc.account) {
                            data.accountInitials.push({
                                'id': gst_doc.account.id,
                                'text': gst_doc.account.name
                            });
                            data.accountSelected.push(gst_doc.account.id);
                        }
                        if (gst_doc.ac_less_two) {
                            data.lessTwoInitials.push({
                                'id': gst_doc.ac_less_two.id,
                                'text': gst_doc.ac_less_two.name
                            });
                            data.lessTwoSelected.push(gst_doc.ac_less_two.id);
                        }

                        if (gst_doc.ac_less_one) {
                            data.lessOneInitials.push({
                                'id': gst_doc.ac_less_one.id,
                                'text': gst_doc.ac_less_one.name
                            });
                            data.lessOneSelected.push(gst_doc.ac_less_one.id);
                        }
                        if (gst_doc.tds_ac) {
                            data.tdsaccountInitials.push({
                                'id': gst_doc.tds_ac.id,
                                'text': gst_doc.tds_ac.name
                            });
                            data.tdsaccountSelected.push(gst_doc.tds_ac.id);
                        }

                refreshComponent(data,'showSelect');
                refreshComponent(data,'accountshow');
                refreshComponent(data,'showAccountsLess');
                refreshComponent(data,'showAccountsLessOther');


            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }

    const resetAccountLessOne =()=> {
        if (form.add_less_other1_amount == 0) {
            console.log('reset')
            data.showAccountsLess = false;
            setTimeout(function () {
                form.acid_add_less_other1 = 0;
                data.lessOneInitials = []
                data.lessOneSelected = []
                data.showAccountsLess = true;
            }, 500);
        }
    }

    const resetAccountLessOther =()=> {
        if (form.add_less_other2_amount == 0) {
            data.showAccountsLessOther = false;
            setTimeout(function () {
                form.acid_add_less_other2 = 0;
                data.lessTwoInitials = []
                data.lessTwoSelected = []
                data.showAccountsLessOther = true;
            }, 500);
        }
    }

    const updateAddOtherAmt =()=> {
        let total = totalAmount;
        let oth_amt = Utilities.round(form.add_oth_amt);
        form.details.forEach(function (row) {
            if (Utilities.round(total, 2) == row.amount)
                row.add_oth_amt = Utilities.round(oth_amt, 2);
            else
                row.add_oth_amt = Utilities.round((row.amount * Utilities.round(form.add_oth_amt, 4)) / totalAmount, 2);
            total -= row.amount;
            total = Utilities.round(total);
            oth_amt -= row.add_oth_amt;
            oth_amt = Utilities.round(oth_amt);
        });
    }

    const updateLessOtherAmt =()=> {

        let total = totalAmount;
        let oth_amt = Utilities.round(form.less_oth_amt, 2);
        form.details.forEach(function (row) {
            if (Utilities.round(total, 2) == row.amount)
                row.less_oth_amt = Utilities.round(oth_amt, 2);
            else
                row.less_oth_amt = Utilities.round((row.amount * Utilities.round(form.less_oth_amt, 4)) / totalAmount, 2);
            total -= row.amount;
            total = Utilities.round(total);
            oth_amt -= row.less_oth_amt;
            oth_amt = Utilities.round(oth_amt);
        });
    }
    const updateGstDetails =()=> {
        form.details.forEach(function (row) {
            row.gst_details = Utilities.getGstDetails(form.doc_date, row.gst, form.l_o_type);
        });
        updateBillAmount();
    }
    const updateBill =()=> {
        updateBillAmount();
    }
    const updateBillAmount =()=> {
       updateAddOtherAmt();
       updateLessOtherAmt();
        let arr = [];
        let gst_on = 0;
        let acid_gst_type = 'I';
        form.gst_amt = 0;
        form.doc_amt = 0;
        if(form.manual_tds == 'N'){
            form.tds_on = 0;
            var tds_on_tot = 0;
            form.tds_amount = 0;
        }
        form.details.forEach(function (row) {
                gst_on = Utilities.round(Utilities.round(row.amount) + Utilities.round(row.add_oth_amt) - Utilities.round(row.less_oth_amt));
                if (form.doc_type != 'EV' && form.pur_sale_type == 'S')
                    acid_gst_type = 'O';
                if(form.ac_gst_type != 'U' || form.rev_charges != 'N'){
                    arr = Utilities.getTransGstDetails(row.gst_details, gst_on, Utilities.round(row.gst_adj_amt, 2), acid_gst_type, row.itc_required);
                    row.trans_gst_details = arr[0];
                    row.gst_amt = arr[1];
                    row.net_amt = arr[2];
                }
                else{
                    row.net_amt = gst_on;
                    row.gst_amt = 0;
                }
                // console.log(gst_on);
                if (form.rev_charges == 'Y') {
                    row.net_amt = gst_on;
                }

            form.gst_amt += Utilities.round(row.gst_amt);
            form.doc_amt += Utilities.round(row.net_amt);
            if(form.manual_tds == 'N'){
                if (form.tds_type == 'B') {
                    tds_on_tot += Utilities.round(row.amount);
                } else {
                    tds_on_tot += Utilities.round(row.net_amt);
                }
            }

        });
        // form.doc_amt += Utilities.round(form.round_off);
        form.doc_amt = Utilities.round(form.doc_amt);
        form.gst_amt = Utilities.round(form.gst_amt);
        form.round_off = Utilities.round(Utilities.round(form.doc_amt,0)-Utilities.round(form.doc_amt));
        if(form.manual_tds == 'N'){
            form.tds_on = Utilities.round(tds_on_tot);
            form.tds_amount = Utilities.round(form.tds_per) * Utilities.round(tds_on_tot) / 100;
            form.tds_amount = Utilities.round(form.tds_amount, 0);
        }
        else{
            form.tds_amount = Utilities.round(Utilities.round(form.tds_on)*Utilities.round(form.tds_per)/100,0);

        }

    }

    const updateAccount= (value, index, account)=> {
        console.log(account);
        if (index == 'ac_id') {
            form.ac_id = value;
            console.log(account);
            form.ac_gst_type = account.party_gst_status;
            form.l_o_type = account.local_outside;
            updateGstDetails();
            updateBillAmount();
            return;
        } else if (index == 'acid_add_less_other1') {
            form.acid_add_less_other1 = value;
            return;
        } else if (index == 'acid_add_less_other2') {
            form.acid_add_less_other2 = value;
            return;
        } else if (index == 'tds_account') {
            form.tds_account = value;
            return;
        }


    }

    const changeInDetails = (type= 'add',index) => {
        if(type =='remove'){
            form.details.splice(index,1);
        }
        else{
            form.details.push(getNewDetail());
        }
        calc();
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'doc_date':
                return props.readonly;
            case 'ac_id':
                return props.readonly;
            case 'l_o_type':
                return true;
            case 'cash_cr':
                return props.readonly;
            case 'pur_sale_type':
                return props.readonly;
            case 'party_doc_no':
                return props.readonly;
            case 'party_doc_date':
                return props.readonly;
            case 'veh_no':
                return props.readonly
            case 'tpt_id':
                return props.readonly
            case 'ac_gst_type':
                return true;
            case 'gst_reason_id':
                return props.readonly;
            case 'gr_no':
                return props.readonly;
            case 'gr_date':
                return props.readonly;
            case 'ref_no':
                return props.readonly;
            case 'ref_date':
                return props.readonly;
            case 'rev_charges':
                return props.readonly;
            case 'acid_add_less_other1':
                return props.readonly;
            case 'add_less_other1_amount':
                return props.readonly;
            case 'acid_add_less_other2':
                return props.readonly;
            case 'add_less_other2_amount':
                return props.readonly;
            case 'stock_entry':
                return props.readonly;
            case 'tds_type':
                return props.readonly;
            case 'tds_per':
                return props.readonly;
            case 'tds_account':
                return props.readonly;
            case 'round_off':
                return props.readonly;
            case 'add_oth_amt':
                return props.readonly;
            case 'less_oth_amt':
                return props.readonly;
            case 'gst_amt':
                return true;
            case 'doc_amt':
                return true;
            case 'tds_on':
                return props.readonly;
            case 'tds_amount':
                return true;
            case 'bill_no':
                return props.readonly;
            case 'fyear':
                return props.readonly;
            default:
                return false;
        }
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Document Date" />
                    <date-picker :disabled="isDisabled('doc_date')" class-name="doc_date" v-model="form.doc_date" :error="form.errors.get('doc_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('doc_date')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.accountshow">
                    <InputLabel for="ac_id" value="Party Name" />
                    <account-select :disabled="isDisabled('ac_id')"
                        v-model="form.ac_id"
                        index="ac_id"
                        :type="'party'"
                        :error="form.errors.get('ac_id') ? true :false"
                        :initials="data.accountInitials"
                        :selected="data.accountSelected"
                        @updateAccount = "updateAccount"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0" >
                <div class="mb-4">
                    <InputLabel for="l_o_type" value="Local/Central" />
                    <SelectInput  v-model="form.l_o_type" :options="[
                        {'id':'L','text':'Local'},
                        {'id':'C','text':'Central'},
                    ]" :error="form.errors.get('l_o_type') ? true :false" :disabled="isDisabled('l_o_type')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('l_o_type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="cash_cr" value="Cash/Credit" />
                    <SelectInput  v-model="form.cash_cr" :options="[
                        {'id':'C','text':'Cash'},
                        {'id':'R','text':'Credit'},
                    ]" :error="form.errors.get('cash_cr') ? true :false" :disabled="isDisabled('cash_cr')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('cash_cr')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="pur_sale_type" value="Purchase Sale Type" />
                    <SelectInput  v-model="form.pur_sale_type" :options="[
                        {'id':'P','text':'Purchase'},
                        {'id':'S','text':'Sale'},
                    ]" :error="form.errors.get('pur_sale_type') ? true :false" :disabled="isDisabled('pur_sale_type')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('pur_sale_type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0" v-if="form.pur_sale_type == 'S'">
                <div class="mb-4">
                    <InputLabel for="fyear" value="Financial Year" />
                    <SelectInput v-model="form.fyear" :options ="years" :disabled="isDisabled('fyear')" :error="form.errors.get('fyear') ? true :false"></SelectInput>
                    <!-- <TextInput  v-model="form.fyear" type="text" :disabled="isDisabled('fyear')" required :error="form.errors.get('fyear') ? true :false" /> -->
                    <InputError class="mt-2" :message="form.errors.get('fyear')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0" v-if="form.pur_sale_type == 'S'">
                <div class="mb-4">
                    <InputLabel for="bill_no" value="Sale Bill No" />
                    <TextInput  v-model="form.bill_no" type="text" @blur="getBillNo()" :disabled="isDisabled('bill_no')" required :error="form.errors.get('bill_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('bill_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3" v-if="form.pur_sale_type == 'P' || form.pur_sale_type == 'S'">
                <div class="mb-4">
                    <InputLabel for="party_doc_no" value="Party Document Number" />
                    <TextInput  v-model="form.party_doc_no" type="text" :disabled="isDisabled('party_doc_no')" required :error="form.errors.get('party_doc_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('party_doc_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3" v-if="form.pur_sale_type == 'P' || form.pur_sale_type == 'S'">
                <div class="mb-4">
                    <InputLabel for="party_doc_date" value="Party Document Date" />
                    <date-picker class-name="party_doc_date" :disabled="isDisabled('party_doc_date')" v-model="form.party_doc_date" :error="form.errors.get('party_doc_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('party_doc_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="veh_no" value="Vehical No." />
                    <TextInput  v-model="form.veh_no" type="text" :disabled="isDisabled('veh_no')" required :error="form.errors.get('veh_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('veh_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.accountshow">
                    <InputLabel for="tpt_id" value="Transport" />
                    <account-select :disabled="isDisabled('tpt_id')"
                        type="gl"
                        v-model="form.tpt_id" index="tpt_id"
                        :error="form.errors.get('tpt_id') ? true :false"
                        :initials="data.initialTransport"
                        :selected="data.selectedTransport"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('tpt_id')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="ac_gst_type" value="GST Type" />
                    <!-- <SelectInput  v-model="form.ac_gst_type" :options="[
                        {'id':'Registered','text':'Registered'},
                        {'id':'Unregistered','text':'Unregistered'},
                    ]" :error="form.errors.get('ac_gst_type') ? true :false" :disabled="isDisabled('ac_gst_type')"> </SelectInput> -->
                    <TextInput v-model="form.ac_gst_type" type="text" :disabled="isDisabled('ac_gst_type')"  :error="form.errors.get('ac_gst_type') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('ac_gst_type')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="gst_reason_id" value="GST Reason" />
                    <SelectInput v-model="form.gst_reason_id" :options ="gst_reasons_options" :disabled="isDisabled('gst_reason_id')" :error="form.errors.get('gst_reason_id') ? true :false"></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('gst_reason_id')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="gr_no" value="GR Number" />
                    <TextInput v-model="form.gr_no" type="text" :error="form.errors.get('gr_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('gr_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="gr_date" value="GR Date" />
                    <date-picker class-name="gr_date" :disabled="isDisabled('gr_date')" v-model="form.gr_date" :error="form.errors.get('gr_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('gr_date')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="ref_no" value="Reference Number" />
                    <TextInput v-model="form.ref_no" type="text" :error="form.errors.get('ref_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('ref_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="ref_date" value="Reference Date" />
                    <date-picker class-name="ref_date" :disabled="isDisabled('ref_date')" v-model="form.ref_date" :error="form.errors.get('ref_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('ref_date')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="rev_charges" value="Reverse Charges" />
                    <SelectInput  v-model="form.rev_charges" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('rev_charges') ? true :false" :disabled="isDisabled('rev_charges')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('rev_charges')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.showAccountsLess">
                    <InputLabel for="acid_add_less_other1" value="Less Other 1" />
                    <account-select :disabled="isDisabled('acid_add_less_other1')"
                        v-model="form.acid_add_less_other1" index="acid_add_less_other1"
                        type="gl"
                        :error="form.errors.get('acid_add_less_other1') ? true :false"
                        :initials="data.lessOneInitials"
                        :selected="data.lessOneSelected"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid_add_less_other1')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="add_less_other1_amount" value="Less Other 1 Amount" />
                    <TextInput v-model="form.add_less_other1_amount" @blur="resetAccountLessOne()" type="text" :disabled="isDisabled('add_less_other1_amount')" :error="form.errors.get('add_less_other1_amount') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('add_less_other1_amount')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.showAccountsLessOther">
                    <InputLabel for="acid_add_less_other2" value="Less Other 2" />
                    <account-select :disabled="isDisabled('acid_add_less_other2')"
                        v-model="form.acid_add_less_other2" index="acid_add_less_other2"
                        type="gl"
                        :error="form.errors.get('acid_add_less_other2') ? true :false"
                        :initials="data.lessTwoInitials"
                        :selected="data.lessTwoSelected"
                        @updateAccount = "updateAccount"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid_add_less_other2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="add_less_other2_amount" value="Less Other 2 Amount" />
                    <TextInput v-model="form.add_less_other2_amount"  @blur="resetAccountLessOther()" type="text" :disabled="isDisabled('add_less_other2_amount')" :error="form.errors.get('add_less_other2_amount') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('add_less_other2_amount')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="stock_entry" value="Stock Entry" />
                    <SelectInput  v-model="form.stock_entry" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('stock_entry') ? true :false" :disabled="isDisabled('stock_entry')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('stock_entry')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="tds_per" value="TDS Percentage" />
                    <TextInput v-model="form.tds_per" type="text" :disabled="isDisabled('tds_per')" :error="form.errors.get('tds_per') ? true :false" @blur="tdsAccount()"/>
                    <InputError class="mt-2" :message="form.errors.get('tds_per')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="tds_type" value="TDS Base/Total" />
                    <SelectInput  v-model="form.tds_type" :options="[
                        {'id':'B','text':'Base'},
                        {'id':'T','text':'Total'},
                    ]" :error="form.errors.get('tds_type') ? true :false" :disabled="isDisabled('tds_type')" @blur="updateBillAmount()"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('tds_type')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="tds_account" value="TDS Account" />
                    <account-select :disabled="isDisabled('tds_account')"
                        v-model="form.tds_account" index="tds_account"
                        type="gl"
                        :error="form.errors.get('tds_account') ? true :false"
                        :initials="data.tdsaccountInitials"
                        :selected="data.tdsaccountSelected"
                        @updateAccount = "updateAccount"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('tds_account')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="freight_type" value="Freight Type" />
                    <SelectInput  v-model="form.freight_type" :options="[
                        {'id':'T','text':'To Pay'},
                        {'id':'F','text':'FOR'},
                    ]" :error="form.errors.get('freight_type') ? true :false" :disabled="isDisabled('freight_type')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('freight_type')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="freight_amt" value="Freight Amount" />
                    <TextInput v-model="form.freight_amt" type="text" :disabled="isDisabled('freight_amt')" :error="form.errors.get('freight_amt') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('freight_amt')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="eway_bill_no" value="Eway Bill No" />
                    <TextInput v-model="form.eway_bill_no" type="text" :disabled="isDisabled('eway_bill_no')" :error="form.errors.get('eway_bill_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('eway_bill_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="eway_bill_date" value="Eway Bill Date" />
                    <date-picker :disabled="isDisabled('eway_bill_date')" v-model="form.eway_bill_date" :error="form.errors.get('eway_bill_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('eway_bill_date')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/2">
                <div class="mb-4">
                    <InputLabel for="remarks" value="Remarks" />
                    <TextAreaInput v-model="form.remarks" type="text" :disabled="isDisabled('remarks')" :error="form.errors.get('remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
        </div>

        <fieldset class="border rounded bg-gray-50 p-4 mb-1">
            <legend class="text-base rounded font-semibold bg-primary text-white px-3" v-text="'Amount'"> </legend>
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="round_off" value="Round off Amount" />
                            <TextInput v-model="form.round_off" type="text"  @blur="updateBillAmount()" :disabled="isDisabled('round_off')" :error="form.errors.get('round_off') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('round_off')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="add_oth_amt" value="Add Other Amount" />
                            <TextInput v-model="form.add_oth_amt" type="text"  @blur="updateBillAmount()" :disabled="isDisabled('add_oth_amt')" :error="form.errors.get('add_oth_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('add_oth_amt')" />
                        </div>
                    </div>


                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="less_oth_amt" value="Less other Amount" />
                            <TextInput v-model="form.less_oth_amt" type="text"  @blur="updateBillAmount()" :disabled="isDisabled('less_oth_amt')" :error="form.errors.get('less_oth_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('less_oth_amt')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="gst_amt" value="GST Amount" />
                            <TextInput v-model="gstAmount" type="text" :disabled="isDisabled('gst_amt')" :error="form.errors.get('gst_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('gst_amt')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="doc_amt" value="Document Amount" />
                            <TextInput v-model="form.doc_amt" type="text" :disabled="isDisabled('doc_amt')" :error="form.errors.get('doc_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('doc_amt')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="tds_on" value="TDS ON" />
                            <TextInput v-model="form.tds_on" type="text"  :disabled="isDisabled('tds_on')"  @blur="updateBillAmount()" :error="form.errors.get('tds_on') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('tds_on')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="tds_amount" value="TDS Amount" />
                            <TextInput v-model="form.tds_amount" type="text" :disabled="isDisabled('tds_amount')" :error="form.errors.get('tds_amount') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('tds_amount')" />
                        </div>
                    </div>
                </div>
        </fieldset>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0" v-if="data.showDetail">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Details</legend>
                <TableLayout >
                    <template #thead>
                        <tr>
                            <th>S.no.</th>
                            <th style="min-width:300px;">Doc Account</th>
                            <th style="min-width:300px;">Item</th>
                            <th style="min-width:200px;">Item Description</th>
                            <th style="min-width:200px;">Brand</th>
                            <th style="min-width:200px;">Packing</th>
                            <th style="min-width:100px;">Quantity</th>
                            <th style="width:100px;">Weight</th>
                            <th style="width:100px;">Rate</th>
                            <th style="width:100px;">Rate On</th>
                            <th>Discount %</th>
                            <th>Discount Amount</th>
                            <th>Net Discount</th>
                            <th style="width:100px;">Amount</th>
                            <th style="width:100px;">HSN Code</th>
                            <th>GST </th>
                            <th>Gst Adjust Amount</th>
                            <th style="width:130px;">GST Amount</th>
                            <th style="width:130px;">Net Amount</th>
                        </tr>
                    </template>
                    <gst-document-detail v-for="(entry,index) in form.details"
                        v-bind:key="entry.random"
                        :index="index"
                        :entry="entry"
                        :form="form"
                        :u-id="entry.random"
                        :create-url="props.create_url"
                        :readonly="readonly"
                        @changeInDetails="changeInDetails"
                        @updateBill="updateBill"
                     >
                    </gst-document-detail>

                </TableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('sale_order_details')" />
        </fieldset>
        <div class="flex flex-wrap items-end -mx-3 mt-3">
            <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4 ">
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
