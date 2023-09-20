
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import PayTermSelect from '@/Pages/ProjectComponents/SelectComponents/PayTermSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import SaleContractDetail from '@/Pages/ProjectComponents/Sale/SaleContract/SaleContractDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const childs = ref([]);
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','close_qty','default_packing','brokerage_parameters','readonly']);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
            form_id:0,
            branch_id:'',
            contract_date:BCL.today,
            contract_no:'',
            ac_id:'',
            valid_from_date:'',
            valid_to_date:'',
            valid_extended_upto:'',
            pay_term_id:'',
            broker_id:'',
            brokerage_rate:'',
            gst_terms:'',
            bargain_no:'',
            bargain_date:'',
            cust_po_no:'',
            cust_po_date:'',
            broker_type:'',
            remarks:'',
            vcode:'SC',
            delivery_terms:'',
            packing_id:'',
            sap_po_no:'',
            sap_po_date:'',
            close_qty:false,
            packed_loose:'',
            status:'O',
            uid:Utilities.getRandomNumber(),
            sale_contract_details:[]
    }));
    const data = reactive({
        create_url:'sale-contracts',
        clientInitials:[],
        clientSelected:[],
        brokerInitials:[],
        brokerSelected:[],
        payTermInitials:[],
        payTermSelected:[],
        packingInitials:[],
        packingSelected:[],
        show:true,
        showPacking:true,
        showPayTerm:true
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Sale Contract':'Add Sale Contract');

    onMounted(() => {
        if(props.form_id > 0){
            getSaleContract();
        }
        else{
            setDefaultParameters();
        }
        if(props.close_qty){
            form.close_qty = true;
        }
        if(form.sale_contract_details.length == 0){
            form.sale_contract_details.push(getDetail());
        }
    });

    const setDefaultParameters = () =>{
        if(props.default_packing){
            data.packingInitials=[{'id':props.default_packing.id,'text':props.default_packing.name}];
            data.packingSelected=[props.default_packing.id];
            Utilities.refreshComponent(data,'show');
        }
    }
    const getDetail = () => {
    return {
        id:0,
        sale_contract_id:'',
        item_id:'',
        qty:'',
        unit_id:'',
        rate:'',
        bargain_price_diff:'',
        tolerance_per:'',
        remarks:'',
        close_qty:'',
        random:Utilities.getRandomNumber()
    }
}
    const submitForm = () =>{
        calc();
        form['postForm'](data.create_url)
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
    const getSaleContract = () =>{
        axios.get(base_url.value+'/sale-contracts/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let sale_contract = response.data.sale_contract;
                if(sale_contract.account){
                    data.clientInitials = [{'text':sale_contract.account.name,'id':sale_contract.account.id}];
                    data.clientSelected = [sale_contract.account.id];
                }
                if(sale_contract.broker){
                    data.brokerInitials = [{'text':sale_contract.broker.name,'id':sale_contract.broker.id}];
                    data.brokerSelected = [sale_contract.broker.id];
                }
                if(sale_contract.pay_term){
                    data.payTermInitials = [{'text':sale_contract.pay_term.name,'id':sale_contract.pay_term.id}];
                    data.payTermSelected = [sale_contract.pay_term.id];
                }
                if(sale_contract.packing){
                    data.packingInitials = [{'text':sale_contract.packing.name,'id':sale_contract.packing.id}];
                    data.packingSelected = [sale_contract.packing.id];
                }
                copyProperties(sale_contract,form);

                form.sale_contract_details = [];
                sale_contract.sale_contract_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    detail.item = element.item;
                    detail.item_unit = element.item_unit;
                    form.sale_contract_details.push(detail);
                });
                refreshComponent(data,'show');
                refreshComponent(data,'showPacking');
                refreshComponent(data,'showPayTerm');
            }
            form.form_id  = props.form_id;
            form.uid = Utilities.getRandomNumber();
        })
        .catch(function(error){
            console.log(error);
        });
    }
    const changeInDetails = (type= 'add',index) => {
        if(type =='remove'){
            form.sale_contract_details.splice(index,1);
        }
        else{
            form.sale_contract_details.push(getDetail());
        }
    }

    const calc = () => {
        form.sale_contract_details.forEach(ele => {
            if(form.packed_loose == 'packed') {
                ele.tolerance_per = 0
            }
        });



        // return { roundedAmount, difference };

    }

    const updateAccount = () =>{
        axios.get('party-category-packing/'+form.ac_id)
        .then(function(response){
            if(response.data.packing){
                data.packingInitials = [{'id':response.data.packing.id,'text':response.data.packing.name}];
                data.packingSelected = [response.data.packing.id];
                refreshComponent(data,'showPacking');
                form.packing_id = response.data.packing.id;
            }
            if(response.data.pay_term){
                data.payTermInitials = [{'id':response.data.pay_term.id,'text':response.data.pay_term.name}];
                data.payTermSelected = [response.data.pay_term.id];
                refreshComponent(data,'showPayTerm');
                form.pay_term_id = response.data.pay_term.id;
            }
        })
        .catch(function(error){
            console.log(error);
        });

    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'contract_date':
                return props.readonly;
            case 'ac_id':
                return props.readonly;
            case 'valid_from_date':
                return props.readonly;
            case 'valid_to_date':
                return props.readonly;
            case 'valid_extended_upto':
                return props.readonly;
            case 'pay_term_id':
                return props.readonly;
            case 'broker_id':
                return props.readonly;
            case 'brokerage_rate':
                return props.readonly;
            case 'gst_terms':
                return props.readonly;
            case 'delivery_terms':
                return props.readonly;
            case 'bargain_no':
                return props.readonly;
            case 'bargain_date':
                return props.readonly;
            case 'cust_po_no':
                return props.readonly;
            case 'cust_po_date':
                return props.readonly;
            case 'sap_po_no':
                return props.readonly;
            case 'sap_po_date':
                return props.readonly;
            case 'broker_type':
                return props.readonly;
            case 'packing_id':
                return props.readonly;
            case 'packed_loose':
                return props.readonly;
            case 'remarks':
                return props.readonly;
            case 'hsn_code':
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="contract_date" value="Contract Date" />
                    <date-picker :disabled="isDisabled('contract_date')"  v-model="form.contract_date" :error="form.errors.get('contract_date') ? true :false" ></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('contract_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0" v-if="form.form_id >0">
                <div class="mb-1">
                    <InputLabel for="contract_no" value="Contract No." />
                    <TextInput v-model="form.contract_no" type="text" disabled  :error="form.errors.get('contract_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('contract_no')" />
                </div>
            </div>
            <div :class="form.form_id >0 ? 'w-full max-w-full px-3 shrink-0 md:w-2/4 lg:w-2/4 md:flex-0':'w-full max-w-full px-3 shrink-0 md:w-3/4 lg:w-3/4 md:flex-0'">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="ac_id" value="Client" />
                    <account-select :disabled="isDisabled('ac_id')"    v-model="form.ac_id" index="-2" :key="-2" :error="form.errors.get('ac_id') ? true :false"
                        :initials="data.clientInitials"
                        :selected="data.clientSelected"
                        @updateAccount = "updateAccount"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="valid_from_date" value="Valid From Date" />
                    <date-picker :disabled="isDisabled('valid_from_date')" class-name="valid_from_date" :min-date="form.contract_date" v-model="form.valid_from_date" :error="form.errors.get('valid_from_date') ? true :false"> </date-picker>
                    <InputError class="mt-2" :message="form.errors.get('valid_from_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="valid_to_date" value="Valid To Date" />
                   <date-picker  :disabled="isDisabled('valid_to_date')"  class-name="valid_to_date" :min-date="form.valid_from_date" v-model="form.valid_to_date" :error="form.errors.get('valid_to_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('valid_to_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="valid_extended_upto" value="Validity Extended Up to" />
                   <date-picker :disabled="isDisabled('valid_extended_upto')" class-name="valid_extended_upto" v-model="form.valid_extended_upto"   :error="form.errors.get('valid_extended_upto') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('valid_extended_upto')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1"  v-if="data.showPayTerm">
                    <InputLabel for="pay_term_id" value="Pay Term" />
                    <pay-term-select  :disabled="isDisabled('pay_term_id')"   v-model="form.pay_term_id"  :error="form.errors.get('pay_term_id') ? true :false"
                        :initials = "data.payTermInitials"
                        :selected = "data.payTermSelected"
                    ></pay-term-select>
                    <InputError class="mt-2" :message="form.errors.get('pay_term_id')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-3/4 lg:w-3/4 md:flex-0">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="broker_id" value="Broker" />
                    <account-select  :disabled="isDisabled('broker_id')" v-model="form.broker_id" index="broker"  :error="form.errors.get('broker_id') ? true :false"
                        :initials="data.brokerInitials"
                        :selected="data.brokerSelected"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('broker_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="brokerage_rate" value="Brokerage Rate" />
                    <TextInput  :disabled="isDisabled('brokerage_rate')" v-model="form.brokerage_rate" type="text" required :error="form.errors.get('brokerage_rate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('brokerage_rate')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="gst_terms" value="GST Terms" />
                    <SelectInput  :disabled="isDisabled('gst_terms')"  v-model="form.gst_terms" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'I','text':'INCLUSIVE'},
                        {'id':'E','text':'EXCLUSIVE'},
                    ]" :error="form.errors.get('gst_terms') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('gst_terms')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="delivery_terms" value="Delivery Terms" />
                    <SelectInput  :disabled="isDisabled('delivery_terms')"  v-model="form.delivery_terms" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'F','text':'FOR'},
                        {'id':'M','text':'EX-MILL'},
                        {'id':'K','text':'EX-KANDLA'},
                    ]" :error="form.errors.get('delivery_terms') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('delivery_terms')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="bargain_no" value="Bargain No." />
                    <TextInput  :disabled="isDisabled('bargain_no')" v-model="form.bargain_no" type="text" required :error="form.errors.get('bargain_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('bargain_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="bargain_date" value="Bargain Date" />
                      <date-picker  :disabled="isDisabled('bargain_date')" class-name="bargain_date" v-model="form.bargain_date" :error="form.errors.get('bargain_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('bargain_date')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="cust_po_no" value="Customer PO No." />
                    <TextInput  :disabled="isDisabled('cust_po_no')" v-model="form.cust_po_no" type="text" required :error="form.errors.get('cust_po_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('cust_po_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="cust_po_date" value="Customer PO Date" />
                      <date-picker  :disabled="isDisabled('cust_po_date')" class-name="cust_po_date" v-model="form.cust_po_date"  :error="form.errors.get('cust_po_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('cust_po_date')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="sap_po_no" value="SAP PO No." />
                    <TextInput  :disabled="isDisabled('sap_po_no')" v-model="form.sap_po_no" type="text" required :error="form.errors.get('sap_po_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sap_po_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 lg:w-1/4 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="sap_po_date" value="SAP PO Date" />
                      <date-picker  :disabled="isDisabled('sap_po_date')" class-name="sap_po_date" v-model="form.sap_po_date"  :error="form.errors.get('sap_po_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('sap_po_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="broker_type" value="Broker  Type" />
                    <SelectInput  :disabled="isDisabled('broker_type')"  v-model="form.broker_type" :options="[
                        {'id':'prec','text':'PREC'},
                        {'id':'per_unit','text':'PER UNIT'},
                    ]" :error="form.errors.get('broker_type') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('broker_type')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-1" v-if="data.showPacking">
                    <InputLabel for="packing_id" value="Packing" />
                    <packing-select  :disabled="isDisabled('packing_id')" v-model="form.packing_id" index="packing_id"  :error="form.errors.get('packing_id') ? true :false"
                        :initials="data.packingInitials"
                        :selected="data.packingSelected"></packing-select>
                    <InputError class="mt-2" :message="form.errors.get('packing_id')" />
                </div>
            </div>

           <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="packed_loose" value="Packed/Loose" />
                    <SelectInput v-model="form.packed_loose" :disabled="isDisabled('packed_loose')" :error="form.errors.get('packed_loose')" :options ="[{'id':'packed','text':'Packed'},{'id':'loose','text':'Loose'}]"  ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('packed_loose')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="remarks" value="Remarks" />
                    <TextAreaInput  :disabled="isDisabled('remarks')" v-model="form.remarks" type="text" required :error="form.errors.get('remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
        </div>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Sale Contract Details</legend>
                <tableLayout >
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                            <th >Item</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Bargain Price Diff</th>
                            <!-- <th>Packed/Loose</th> -->
                            <th>Tolerance Per</th>
                            <th v-if="props.close_qty">Close Qty</th>
                            <th v-if="isDisabled('button')"></th>
                        </tr>
                    </template>
                    <sale-contract-detail  ref="childs" v-for="(sale_contract_det,index) in form.sale_contract_details" :key="sale_contract_det.random"
                        :detail = "sale_contract_det"
                        :index = "index"
                        :form="form"
                        :brokerage_parameters="brokerage_parameters"
                        :close_qty = "props.close_qty"
                        :readonly="props.readonly"
                         @changeInDetails="changeInDetails"

                    >
                    </sale-contract-detail>

                </tableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('sale_contract_details')" />
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
