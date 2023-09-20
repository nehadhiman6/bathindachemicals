
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
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import TransferDetail from './TransferDetail.vue';
    import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';

    const { base_url,refreshComponent } = globalMixin();
    const props = defineProps(['form_id','title','create_url','readonly']);
    const form = reactive( new Form({
        form_id: 0,
        vcode: '',
        iss_date: BCL.fyDate,
        branch_id_from: '0',
        branch_id_to: '0',
        iss_doc_no: '',
        iss_doc_no_print: '',
        rec_date: '',
        rec_doc_no: '',
        rec_doc_no_print: '',
        status: '',
        acid_tpt: '0',
        vehical_no:'',
        gr_no: '',
        gr_date: '',
        slip_no: '',
        total_wt: '',
        tare_wt: '',
        net_wt: '',
        remarks:'',
        received_by: 0,
        amount: 0,
        rec_slip_no:'',
        rec_total_wt:'',
        rec_tare_wt:'',
        rec_net_wt:'',
        uid: Utilities.getRandomNumber(),
        details: [],
    }));
    const pageTitle = computed(() => props.form_id > 0 ? 'Update ' + props.title:'Add ' + props.title);
    const emit = defineEmits(['resetForm']);
    const data = reactive({show:true,
        account: [],
        accountshow: true,
        initialTransport: [],
        selectedTransport: [],
        branchInitials: [],
        branchSelected: [],
        branchFromInitials: [],
        branchFromSelected: [],
        showSelect: true,
        showAccountsLess: true,
        showAccountsLessOther: true,
        showDetail:true,

    });

    const getNewDetail = () => {
        return {
            id: 0,
            item_id: '',
            unit_id: '',
            packing_id: '',
            qty: '0',
            weight: 0,
            rate: '0',
            rate_on: 'Q',
            rec_qty: '0',
            rec_weight: '0',
            amount: '',
            // gst_details: [],
            // trans_gst_details: [],
            item:null,
            // gst: {},
            random:Utilities.getRandomNumber()
        }
    }

    const title = computed(() => {
        if (props.create_url == "issue") {
                return "Issue"
        } else if (props.create_url == "receipt") {
            return "Receipt";
        }
    });

    onMounted(() => {
        if (props.create_url == 'issue') {
            form.status = 'I';
        } else if (props.create_url == 'receipt') {
            form.status = 'R';
        }

        if (props.form_id > 0) {
            editTransfer(props.form_id);
        } else {
            if (form.details.length < 1) {
                form.details.push(getNewDetail());
            }
        }
    });

    const getSlipWeight = ()=>{
        let slipno = '';
        if(form.status == 'I'){
            slipno = form.slip_no;
        }else{
            slipno = form.rec_slip_no;
        }
        axios.get('get-slip-weight/'+ slipno)
        .then(function(response){
            console.log(response.data);
            if(response.data.msg == ''){
                form.total_wt = response.data.item.tare_wt;
                form.tare_wt = response.data.item.tare_wt;
                form.net_wt = response.data.item.net_wt;
            }
            else{
                Utilities.showPopMessage(response.data.msg)
            }

        });
    }

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
            if(error && error.errors && error.errors.net_weight_msg)
                Utilities.showPopMessage(error.errors.net_weight_msg[0],"Invalid Data","error",'6000',true);
        });
    }


    const editTransfer = (id) =>{
        axios.get(base_url.value+'/'+props.create_url.replace(/_/g, "-")+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let transfer = response.data.transfer
                    Utilities.copyProperties(transfer,form);
                    form.form_id = transfer.id;
                    form.details = [];
                    transfer.details.forEach(function (row) {
                        let blank_detail = getNewDetail();
                        blank_detail.item = row.item;
                        blank_detail.item_unit = row.item_unit;
                        blank_detail.packing = row.packing;
                        Utilities.copyProperties(row, blank_detail);
                        form.details.push(blank_detail);
                    })

                    if (props.create_url == 'issue') {
                        form.status = 'I';
                    } else if (props.create_url == 'receipt') {
                        form.rec_date = form.iss_date;
                        form.rec_slip_no = form.slip_no;
                        form.rec_total_wt = form.total_wt;
                        form.rec_tare_wt = form.tare_wt;
                        form.rec_net_wt = form.net_wt;
                        form.status = 'R';
                    }
                    if (transfer.transport) {
                        data.initialTransport = [{
                            'id': transfer.transport.id,
                            'text': transfer.transport.name
                        }];
                        data.selectedTransport = [transfer.transport.id];
                    }

                    if (transfer.branch_to) {
                        data.branchInitials.push({
                            'id': transfer.branch_to.id,
                            'text': transfer.branch_to.name
                        });
                        data.branchSelected.push(transfer.branch_to.id);
                    }

                    if (transfer.branch_from) {
                        data.branchFromInitials.push({
                            'id': transfer.branch_from.id,
                            'text': transfer.branch_from.name
                        });
                        data.branchFromSelected.push(transfer.branch_from.id);
                    }
                refreshComponent(data,'showSelect');
                refreshComponent(data,'accountshow');


            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
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

    const calc = () => {
        if(form.status == 'I'){
            form.net_wt = Utilities.formatNumber(Utilities.round(form.total_wt,3)-Utilities.round(form.tare_wt,3),3);
        }
        else{
            form.rec_net_wt = Utilities.formatNumber(Utilities.round(form.rec_total_wt,3)-Utilities.round(form.rec_tare_wt,3),3);
        }
        form.amount = 0;
        form.details.forEach(ele => {
            let rate_on = ele.rate_on == 'Q' ?  ele.qty : ele.weight;
            ele.amount = Utilities.round(ele.rate )* Utilities.round(rate_on);
            form.amount = Utilities.round(Utilities.round(form.amount )+Utilities.round(ele.amount));
        });
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'iss_date':
                return props.readonly;
            case 'rec_date':
                return props.readonly;
            case 'acid_tpt':
                return props.readonly;
            case 'vehical_no':
                return props.readonly;
            case 'gr_no':
                return props.readonly;
            case 'gr_date':
                return props.readonly;
            case 'slip_no':
                props.readonly == true
            case 'total_wt':
                if(props.readonly == true || form.slip_no != ''){
                    return true;
                }
                else{
                    return false;
                }
            case 'tare_wt':
            if(props.readonly == true || form.slip_no != ''){
                    return true;
                }
                else{
                    return false;
                }

            case 'rec_slip_no':
                return props.readonly == true
            case 'rec_total_wt':
                if(props.readonly == true || form.rec_slip_no != ''){
                    return true;
                }
                else{
                    return false;
                }
            case 'rec_tare_wt':
            if(props.readonly == true || form.rec_slip_no != ''){
                    return true;
                }
                else{
                    return false;
                }
            case 'rec_net_wt':
                return true;
            case 'net_wt':
                return true;
            case 'amount':
                return true;
            case 'remarks':
                return props.readonly;
            case 'received_by':
                return props.readonly;
            case 'branch_id_from':
                return true;
            case 'branch_id_to':
                if(props.readonly == true || form.status == 'R'){
                    return true;
                }
                else{
                    return false;
                }
            default:
                return false;
        }
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0"  v-if="form.status == 'R'">
                <div class="mb-4" v-if="data.showSelect">
                    <InputLabel for="branch_id_from" value="From Branch"/>
                    <branch-select v-model="form.branch_id_from" :index="1" :initials="data.branchFromInitials" :selected="data.branchFromSelected" :disabled="isDisabled('branch_id_from')"></branch-select>
                    <InputError class="mt-2" :message="form.errors.get('branch_id_from')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0" v-if="form.status == 'I'">
                <div class="mb-4" v-if="data.showSelect">
                    <InputLabel for="branch_id_to" value="To Branch"/>
                    <branch-select v-model="form.branch_id_to" :index="2" :initials="data.branchInitials" :selected="data.branchSelected" :disabled="isDisabled('branch_id_to')"></branch-select>
                    <InputError class="mt-2" :message="form.errors.get('branch_id_to')" />
                    <InputError class="mt-2" :message="form.errors.get('branch_id_to_msg')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0" v-if="props.create_url == 'issue'">
                <div class="mb-4">
                    <InputLabel for="name" value="Issue Date"/>
                    <date-picker :disabled="isDisabled('iss_date')" class-name="iss_date" v-model="form.iss_date" :error="form.errors.get('iss_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('iss_date')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0" v-else>
                <div class="mb-4">
                    <InputLabel for="rec_date" value="Receipt Date"/>
                    <date-picker :disabled="isDisabled('rec_date')" class-name="rec_date" v-model="form.rec_date" :error="form.errors.get('rec_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('rec_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.accountshow">
                    <InputLabel for="acid_tpt" value="Transport" />
                    <account-select :disabled="isDisabled('acid_tpt')"
                        v-model="form.acid_tpt" index="1"
                        :error="form.errors.get('acid_tpt') ? true :false"
                        :initials="data.initialTransport"
                        :selected="data.selectedTransport"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid_tpt')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="vehical_no" value="Vehical No." />
                    <TextInput  v-model="form.vehical_no" type="text" :disabled="isDisabled('vehical_no')" required :error="form.errors.get('vehical_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vehical_no')" />
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

            <div class="w-full max-w-full px-3  md:w-1/3" v-if="form.status == 'I'">
                <div class="mb-4">
                    <InputLabel for="slip_no" value="Slip No" />
                    <TextInput v-model="form.slip_no" type="text" :error="form.errors.get('slip_no') ? true :false" @blur="getSlipWeight()"/>
                    <InputError class="mt-2" :message="form.errors.get('slip_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/3" v-if="form.status == 'R'">
                <div class="mb-4">
                    <InputLabel for="rec_slip_no" value="Slip No" />
                    <TextInput v-model="form.rec_slip_no" type="text" :error="form.errors.get('rec_slip_no') ? true :false" @blur="getSlipWeight()"/>
                    <InputError class="mt-2" :message="form.errors.get('rec_slip_no')" />
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
                            <InputLabel for="total_wt" value="Laden Weight" />
                            <TextInput v-model="form.total_wt" type="text" @change="calc" :disabled="isDisabled('total_wt')" :error="form.errors.get('total_wt') ? true :false" v-if="form.status == 'I'"/>
                            <TextInput v-model="form.rec_total_wt" type="text" @change="calc" :disabled="isDisabled('rec_total_wt')" :error="form.errors.get('rec_total_wt') ? true :false" v-if="form.status == 'R'"/>
                            <InputError class="mt-2" :message="form.errors.get('total_wt')" />
                            <InputError class="mt-2" :message="form.errors.get('rec_total_wt')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="tare_wt" value="Unladen Weight" />
                            <TextInput v-model="form.tare_wt" type="text" @change="calc" :disabled="isDisabled('tare_wt')" :error="form.errors.get('tare_wt') ? true :false" v-if="form.status == 'I'"/>
                            <TextInput v-model="form.rec_tare_wt" type="text" @change="calc" :disabled="isDisabled('rec_tare_wt')" :error="form.errors.get('rec_tare_wt') ? true :false" v-if="form.status == 'R'"/>
                            <InputError class="mt-2" :message="form.errors.get('tare_wt')" />
                            <InputError class="mt-2" :message="form.errors.get('rec_tare_wt')" />

                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="net_wt" value="Net Weight" />
                            <TextInput v-model="form.net_wt" type="text" :disabled="isDisabled('net_wt')" :error="form.errors.get('net_wt') ? true :false" v-if="form.status == 'I'"/>
                            <TextInput v-model="form.rec_net_wt" type="text" :disabled="isDisabled('rec_net_wt')" :error="form.errors.get('rec_net_wt') ? true :false" v-if="form.status == 'R'"/>
                            <InputError class="mt-2" :message="form.errors.get('net_wt')" />
                            <InputError class="mt-2" :message="form.errors.get('rec_net_wt')" />

                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/4">
                        <div class="mb-4">
                            <InputLabel for="amount" value="amount" />
                            <TextInput v-model="form.amount" type="text" :disabled="isDisabled('amount')" :error="form.errors.get('amount') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('amount')" />
                        </div>
                    </div>
                </div>
        </fieldset>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0" v-if="data.showDetail">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Details</legend>
                <TableLayout >
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                        <th style="min-width:300px;">Item</th>
                        <th>Item Unit</th>
                        <th>Packing</th>
                        <th>Qty</th>
                        <th>Weight</th>
                        <th>Rate</th>
                        <th>Rate On</th>
                        <th v-if="form.status == 'R'">Receipt Qty</th>
                        <th v-if="form.status == 'R'">Receipt Weight</th>
                        <th>Amount</th>
                        <th ></th>
                        </tr>
                    </template>
                    <transfer-detail v-for="(entry,index) in form.details"
                        v-bind:key="entry.random"
                        :index="index"
                        :detail="entry"
                        :form="form"
                        :u-id="entry.random"
                        :create_url="props.create_url"
                        :readonly="readonly"
                        @changeInDetails="changeInDetails"
                        @calc="calc()"

                     >
                    </transfer-detail>

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
