

<script setup>
    import { ref, onMounted, reactive ,nextTick} from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { usePage} from '@inertiajs/vue3';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TableComponent from '@/Components/CustomComponents/Sections/TableComponent.vue';
    import SaleOrderSelect from '@/Pages/ProjectComponents/SelectComponents/SaleOrderSelect.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SaleOrderDispatchModal from '@/Pages/ProjectComponents/Sale/SaleOrderDispatch/SaleOrderDispatchModal.vue';
    import globalMixin from '../../../../globalMixin';
    const emit = defineEmits(['resetForm']);

    const { base_url,canAny,refreshComponent} = globalMixin();
    const page = usePage();
    const props = defineProps(['form_id','readonly']);
    const state = reactive({
        showModal: false,
        index: -1,
        packings:[],
        showDispatch:false,
        dispatch:null,
        orderInitials:[],
        orderSelected:[],
        showOrder:true,
        create_url :'sale-order-dispatch'
    });

    const getDestinationDetail =()=>{
        return {
            destination:'',
            packings:[],
            random:Utilities.getRandomNumber()
        }
    }

    const form = reactive( new Form({
        form_id:0,
        dispatch_id:0,
        sale_order_ids:[],
        dispatch_date:BCL.today,
        dispatch_advise_no:'',
        dispatch_advise_no_part:'',
        dispatch_advise:'N',
        destinations:[],
        uid:Utilities.getRandomNumber()
    })
);

    const getDetail =()=>{
        return {
            id:0,
            item_id:0,
            item_name:'',
            packing_id:0,
            brand_id:0,
            brand_name:'',
            packing_name:'',
            bill_party:'',
            total_qty:'',
            left:'',
            sale_order_no:'',sale_order_id:'',
            qty:'',
            random:Utilities.getRandomNumber()
        }
    }



const showModaDialog = (index) => {
    state.showModal = true;
    state.index = index;
}

const resetModal =()=>{
    state.showModal = false;
    nextTick(() => {
        state.index = -1;
    });
}
onMounted(() => {
    if(props.form_id > 0){
        form.dispatch_id = props.form_id;
        getPackings();
    }
});


const submitForm = () =>{
    form['postForm'](base_url.value+'/'+state.create_url)
    .then(function(response){
        console.log(response);
        if(response.success){
            Utilities.showPopMessage("Your data has been saved successfully!")
            emit('resetForm');
        }
    })
    .catch(function(error){
        Utilities.showPopMessage(error.message,"Invalid Data","error",'6000',true)
    });
}

    const getPackings =()=>{
        form['postForm'](base_url.value+'/dispatch-packings')
        .then(function(response){
            if(response.success){
                if(response.saved){
                    Utilities.showPopMessage('Success',"Dispatch has deen saved Successfully","success",'6000',true)
                    emit('resetForm');
                }
                if(response.packings){
                    state.packings = response.packings;
                    state.dispatch = response.dispatch;
                    if(response.dispatch){
                        Utilities.copyProperties(state.dispatch,form);
                        form.uid = Utilities.getRandomNumber();
                        state.orderInitials = [];
                        state.orderSelected = [];
                        state.dispatch.sale_orders.forEach(order => {
                            state.orderInitials.push({'text':order.sale_order_no,'id':order.id});
                            state.orderSelected.push(order.id);
                        });
                        refreshComponent(state,'showOrder');
                    }
                    setPackings();
                }
            }
        })
        .catch(function(error){
            console.log(error);
            Utilities.showPopMessage(error.message,"Invalid Data","error",'6000',true)
        });
    }

    const setPackings = () =>{
        if(form.dispatch_id > 0){
            var destination_packings =[];
            if(state.dispatch && state.dispatch.sale_order_dispatches){
                var destinations =  [...new Set(state.dispatch.sale_order_dispatches.map(obj => obj.destination))];
                destinations.forEach(des => {
                    var det = getDestinationDetail();
                    // det.id = des.id;
                    det.destination = des;
                    var packings = [];
                    state.packings.forEach(pack => {
                        var packing_matched =  state.dispatch.sale_order_dispatches.find(function(arr) {
                            return (arr["destination"] == des
                                && arr['item_id'] == pack.item_id
                                && arr['packing_id'] == pack.packing_id
                                && arr['brand_id'] == pack.brand_id
                            );
                        });

                        console.log("packing_matched" );
                        console.log(packing_matched);

                        let detail = getDetail();
                        detail.item_id = pack.item_id;
                        detail.packing_id = pack.packing_id;
                        detail.brand_id = pack.brand_id;
                        detail.qty =  packing_matched ?  packing_matched['qty']: 0;
                        detail.total_qty = pack.total_qty;
                        detail.brand_name =  pack.brand_name ? pack.brand_name :'';
                        detail.packing_name = pack.packing_name ? pack.packing_name :'';
                        detail.sale_order_no = pack.sale_order_no ? pack.sale_order_no :'';
                        detail.item_name = pack.item_name ? pack.item_name :'';
                        detail.bill_party = pack.bill_party ? pack.bill_party :'';
                        detail.sale_order_id = pack.sale_order_id ? pack.sale_order_id :0;
                        packings.push(detail);
                    });
                    det.packings = packings;
                    form.destinations.push(det);
                });

            }
        }
        else{
            addDestination();
        }
        state.showDispatch = true;
    }


    const getLeftQuantity = (packing)=>{
        var qty = packing.total_qty;
        form.destinations.forEach(element => {
            const matchedObject = element.packings.filter(obj => (
                obj.sale_order_id == packing.sale_order_id &&
                obj.brand_id == packing.brand_id &&
                obj.item_id == packing.item_id &&
                obj.packing_id == packing.packing_id
            ));
            matchedObject.forEach(ele => {
                qty = qty - ele['qty'];
            });
         });
        return qty;
    }

    const getPackingsArray= () =>{
        var packings = [];
        state.packings.forEach(element => {
            let detail = getDetail();
            detail.item_id = element.item_id;
            detail.packing_id = element.packing_id;
            detail.brand_id = element.brand_id;
            detail.qty =  getLeftQuantity(element);
            detail.total_qty = element.total_qty;
            detail.brand_name =  element.brand_name ? element.brand_name :'';
            detail.packing_name = element.packing_name ? element.packing_name :'';
            detail.sale_order_no = element.sale_order_no ? element.sale_order_no :'';
            detail.item_name = element.item_name ? element.item_name :'';
            detail.bill_party = element.bill_party ? element.bill_party :'';
            detail.sale_order_id = element.sale_order_id ? element.sale_order_id :0;
            packings.push(detail);
        });
        return packings;
    }

    const updateSaleOrder =(ids)=>{
        form.sale_order_ids = ids;
    }

    const changeInDetails = (type="add",index) =>{
        if(type == 'remove' && index != 0){
            props.destinations.splice(index,1);
        }
        else{
            if(anyQuantityLeft()){
                addDestination();
            }
        }
    }

    const anyQuantityLeft = () =>{
        var total_quantities = 0;
        var left = false;
        state.packings.forEach(element => {
            total_quantities += Utilities.round(element.total_qty);
        });

        console.log("total_quantities", total_quantities);

        var consumed = 0;
        form.destinations.forEach(element => {
            element.packings.forEach(ele => {
                consumed += Utilities.round(ele.qty);
            });
        });

        if(total_quantities > consumed){
            left = true;
        }
        return left;
    }

    const addDestination = () =>{
        var dest = getDestinationDetail();
        dest.packings = getPackingsArray();
        form.destinations.push(dest);
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'dispatch_date':
                if(props.readonly == true || state.showDispatch == true){
                    return true
                }
            case 'dispatch_advise':
                if(props.readonly == true || state.showDispatch == true){
                    return true
                }
            case 'sale_order_ids':
                if(props.readonly == true || state.showDispatch == true){
                    return true
                }
            case 'destination':
                return props.readonly;
            case 'cancel':
                if(props.readonly == false && anyQuantityLeft == true){
                    return true
                }
            default:
                return false;
        }
    }
    // :disabled="state.showDispatch"
</script>

<template>
    <FormWrapper title="ADD SALE ORDER DISPATCH">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                <div class="mb-1">
                    <InputLabel for="dispatch_date" value="Dispatch Date" />
                    <date-picker :disabled="isDisabled('dispatch_date')" v-model="form.dispatch_date" :error="form.errors.get('dispatch_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('dispatch_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                <div class="mb-1">
                    <InputLabel for="dispatch_advise" value="Dispatch Advise Required" />
                    <SelectInput  :disabled="isDisabled('dispatch_advise')"   v-model="form.dispatch_advise" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'N','text':'No'},
                        {'id':'Y','text':'Yes'},
                    ]" :error="form.errors.get('dispatch_advise') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('dispatch_advise')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-3/5 lg:w-3/5">

                <div class="mb-1"  v-if="state.showOrder" >
                    <InputLabel for="sale_order_ids" value="Sale Orders" />
                    <sale-order-select :disabled="isDisabled('sale_order_ids')"  index="sale_order1" :multiple="true" :error="form.errors.get('sale_order_ids') ? true :false"
                        packed_loose="packed" :dispatch_advice="form.dispatch_advise"
                        url="sale-orders-dispatch/filtered"
                        :initials="state.orderInitials"
                        :selected="state.orderSelected"
                        @updateSaleOrder="updateSaleOrder"></sale-order-select>
                    <InputError class="mt-2" :message="form.errors.get('sale_order_ids')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-full">
                <div class="mt-4">
                    <ButtonComp v-if="state.showDispatch == false" @buttonClicked="getPackings()" type="save">Add Dispatch</ButtonComp>
                    <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
                </div>
            </div>
        </div>
    </FormWrapper>
    <sale-order-dispatch-modal v-if="state.showModal" @resetForm="resetForm"
        :detail="form.destinations[state.index]" :form="form"
        :dest_index = "state.index"
        :readonly="props.readonly"
        @resetModal='resetModal'
    >
    </sale-order-dispatch-modal>
    <ListWrapper title="Dispatch" v-if="state.showDispatch">
        <template #button>
            <ButtonComp v-if="isDisabled('button')" @buttonClicked="submitForm()" type="save">
                SAVE
            </ButtonComp>
        </template>
        <template #table>
        <TableComponent>
        <template #thead>
        <thead  class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th  scope="col" class="py-4 px-6">Sr. no.</th>
                <th  scope="col" class="py-4 px-6">Destination</th>
                <th  scope="col" class="py-4 px-6">Packings</th>
                <th  scope="col" class="py-4 px-6"></th>
            </tr>
        </thead>
        </template>
        <tr class="bg-white b order-b dark:bg-gray-800 dark:border-gray-700 bg-indigo-500 bg-opacity-25 " v-for="(destination,index) in form.destinations" :key="destination.random">
            <td class="py-4 px-6" v-text="index+ 1"> </td>
            <td class="py-4 px-6" >
                <TextInput v-model="destination.destination" :disabled="isDisabled('destination')" type="text" required autofocus :error="form.errors.get('item_name') ? true :false" />
            </td>
            <td class="py-4 px-6" >
                <ButtonComp type="save" v-if="form.dispatch_advise =='Y'" size="sm" @buttonClicked="showModaDialog(index)">Packings
                </ButtonComp>
            </td>
            <td>
                <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" v-if="index > 0" aria-hidden="true" @click="emit('changeInDetails','remove',index)"></i>
            </td>
        </tr>
        <ButtonComp type="cancel" size="sm" v-if="isDisabled('cancel')" @buttonClicked="changeInDetails('add')">+ Add
        </ButtonComp>
        </TableComponent>
        </template>
    </ListWrapper>
</template>

