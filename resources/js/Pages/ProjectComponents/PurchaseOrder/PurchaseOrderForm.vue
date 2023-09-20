
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
    import PayTermSelect from '@/Pages/ProjectComponents/SelectComponents/PayTermSelect.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import PurchaseOrderDetail from './PurchaseOrderDetail.vue';

    const { base_url,refreshComponent } = globalMixin();
    const props = defineProps(['form_id','title','readonly','pur_order_types']);
    // const data = reactive({  });
    const form = reactive( new Form({
        form_id: 0,
        po_type: '',
        del_from: BCL.fyDate,
        del_to: BCL.fyDate,
        doc_no: '',
        doc_no_print: '',
        seller_acid: '',
        buyer_acid: '',
        pay_term_id: '0',
        condition:'',
        broker_acid: '',
        direct: 'N',
        del_term: 'XML',
        remarks: '',
        branch_id: 0,
        uid: Utilities.getRandomNumber(),
        details: [],
    }));
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Purchase Order':'Add Purchase Order');
    const emit = defineEmits(['resetForm']);
    const data = reactive({show:true,
        account: [],
        accountshow: true,
        initialSeller: [],
        selectedSeller: [],
        initialBuyer: [],
        selectedBuyer: [],
        showBroker: true,
        initialBroker: [],
        selectedBroker: [],
        showPayment: true,
        initialPayment: [],
        selectedPayment: [],
        showDetail:true,
        create_url:'purchase-orders'

    });

    const getNewDetail = () => {
        return {
            id: 0,
            pur_ord_id:'0',
            item_id: '',
            unit_id: '',
            qty_from: '0',
            qty_to: '0',
            rate: 0,
            qty_extended:0,
            rate_extended:0,
            item:null,
            random:Utilities.getRandomNumber()
        }
    }

    onMounted(() => {
        if (props.form_id > 0) {
            editTransfer(props.form_id);
        } else {
            if (form.details.length < 1) {
                form.details.push(getNewDetail());
            }
        }
    });

    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
            // if(error && error.errors && error.errors.net_weight_msg)
            //     Utilities.showPopMessage(error.errors.net_weight_msg[0],"Invalid Data","error",'6000',true);
        });
    }


    const editTransfer = (id) =>{
        axios.get(base_url.value+'/'+data.create_url+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let pur_order = response.data.pur_order
                    Utilities.copyProperties(pur_order,form);
                    form.form_id = pur_order.id;
                    form.details = [];
                    pur_order.details.forEach(function (row) {
                        let blank_detail = getNewDetail();
                        blank_detail.item = row.item;
                        blank_detail.item_unit = row.unit;
                        Utilities.copyProperties(row, blank_detail);
                        form.details.push(blank_detail);
                    })

                    if (pur_order.seller) {
                        data.initialSeller = [{
                            'id': pur_order.seller.id,
                            'text': pur_order.seller.name
                        }];
                        data.selectedSeller = [pur_order.seller.id];
                    }

                    if (pur_order.buyer) {
                        data.initialBuyer.push({
                            'id': pur_order.buyer.id,
                            'text': pur_order.buyer.name
                        });
                        data.selectedBuyer.push(pur_order.buyer.id);
                    }

                    if (pur_order.pay_term) {
                        data.initialPayment.push({
                            'id': pur_order.pay_term.id,
                            'text': pur_order.pay_term.name
                        });
                        data.selectedPayment.push(pur_order.pay_term.id);
                    }

                    if (pur_order.broker) {
                        data.initialBroker.push({
                            'id': pur_order.broker.id,
                            'text': pur_order.broker.name
                        });
                        data.selectedBroker.push(pur_order.broker.id);
                    }

                refreshComponent(data,'account');
                refreshComponent(data,'accountshow');
                refreshComponent(data,'showBroker');
                refreshComponent(data,'showPayment');
                refreshComponent(data,'showDetail');
                $(window).scrollTop(0);

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
    }



    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'po_type':
                return props.readonly;
            case 'del_from':
                return props.readonly;
            case 'del_to':
                return props.readonly;
            case 'seller_acid':
                return props.readonly;
            case 'buyer_acid':
                return props.readonly;
            case 'pay_term_id':
                return props.readonly;
            case 'condition':
                return props.readonly;
            case 'broker_acid':
                return props.readonly;
            case 'direct':
                return props.readonly;
            case 'del_term':
                return props.readonly;
            case 'remarks':
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
                    <InputLabel for="del_from" value="Delivery From"/>
                    <date-picker :disabled="isDisabled('del_from')" class-name="del_from" v-model="form.del_from" :error="form.errors.get('del_from') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('del_from')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="del_to" value="Delivery To"/>
                    <date-picker :disabled="isDisabled('del_to')" class-name="del_to" v-model="form.del_to" :error="form.errors.get('del_to') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('del_to')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="po_type" value="Order Type"/>
                    <SelectInput  :disabled="isDisabled('po_type')"  v-model="form.po_type" :options="props.pur_order_types" :error="form.errors.get('po_type') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('po_type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.accountshow">
                    <InputLabel for="seller_acid" value="Seller Account" />
                    <account-select :disabled="isDisabled('seller_acid')"
                        v-model="form.seller_acid" index="1"
                        :error="form.errors.get('seller_acid') ? true :false"
                        :initials="data.initialSeller"
                        :selected="data.selectedSeller"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('seller_acid')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.accountshow">
                    <InputLabel for="buyer_acid" value="Buyer Account" />
                    <account-select :disabled="isDisabled('buyer_acid')"
                        v-model="form.buyer_acid" index="2"
                        :error="form.errors.get('buyer_acid') ? true :false"
                        :initials="data.initialBuyer"
                        :selected="data.selectedBuyer"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('buyer_acid')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4" v-if="data.showPayment">
                    <InputLabel for="pay_term_id" value="Payment Term" />
                    <pay-term-select :disabled="isDisabled('pay_term_id')"
                        v-model="form.pay_term_id" index="2"
                        :error="form.errors.get('pay_term_id') ? true :false"
                        :initials="data.initialPayment"
                        :selected="data.selectedPayment"
                    ></pay-term-select>
                    <InputError class="mt-2" :message="form.errors.get('pay_term_id')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/3">
                <div class="mb-4">
                    <InputLabel for="condition" value="Condition" />
                    <TextInput  v-model="form.condition" type="text" :disabled="isDisabled('condition')" required :error="form.errors.get('condition') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('condition')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="direct" value="Direct"/>
                    <SelectInput  v-model="form.direct" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('direct') ? true :false" :disabled="isDisabled('direct')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('direct')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0" v-if="form.direct == 'Y' && data.showBroker">
                <div class="mb-4">
                    <InputLabel for="broker_acid" value="Broker Account" />
                    <account-select :disabled="isDisabled('broker_acid')"
                        v-model="form.broker_acid" index="4"
                        :error="form.errors.get('broker_acid') ? true :false"
                        :initials="data.initialBroker"
                        :selected="data.selectedBroker"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('broker_acid')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="del_term" value="Delivery Term" />
                    <SelectInput  v-model="form.del_term" :options="[
                        {'id':'FOR','text':'FOR'},
                        {'id':'XML','text':'XML'},
                    ]" :error="form.errors.get('del_term') ? true :false" :disabled="isDisabled('del_term')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('del_term')" />
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
        <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0" v-if="data.showDetail">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Details</legend>
                <TableLayout >
                    <template #thead>
                        <tr>
                        <th>Sr</th>
                        <th style="min-width:300px;">Item</th>
                        <th>Item Unit</th>
                        <th>Qty Form</th>
                        <th>Qty To</th>
                        <th>Rate(Quantal)</th>
                        <th ></th>
                        </tr>
                    </template>
                    <purchase-order-detail v-for="(entry,index) in form.details"
                        v-bind:key="entry.random"
                        :index="index"
                        :detail="entry"
                        :form="form"
                        :u-id="entry.random"
                        :create_url="props.create_url"
                        :readonly="readonly"
                        @changeInDetails="changeInDetails"

                     >
                    </purchase-order-detail>

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
