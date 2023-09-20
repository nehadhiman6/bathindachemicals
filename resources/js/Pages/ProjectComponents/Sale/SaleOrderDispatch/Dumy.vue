

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import TableComponent from '@/Components/CustomComponents/Sections/TableComponent.vue';
import SaleOrderDispatchModal from '@/Pages/ProjectComponents/Sale/SaleOrderDispatch/SaleOrderDispatchModal.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['packings','sale_order','sale_order_dispatches']);
const state = reactive({
    showModal: false,
    index: -1,
    create_url :'sale-order-dispatch'
});

const getDestinationDetail =()=>{
    return {
        id:0,
        qty:'',
        destination:'',
        random:Utilities.getRandomNumber()
    }
}

const form = reactive( new Form({
    sale_order_id:props.sale_order.id,
    packings:[]
})
);

const getDetail =()=>{
    return {
        item_id:0,
        item_name:'',
        packing_id:0,
        brand_id:0,
        brand_name:'',
        packing_name:'',
        qty:'',
        consumed:'',
        destinations:[],
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
    props.packings.forEach(element => {
        let detail = getDetail();
        detail.item_id = element.item_id;
        detail.packing_id = element.packing_id;
        detail.brand_id = element.brand_id;
        detail.qty = element.total_qty;
        detail.brand_name =  element.brand_name ? element.brand_name :'';
        detail.packing_name = element.packing_name ? element.packing_name :'';
        detail.item_name = element.item_name ? element.item_name :'';

        var destinations = props.sale_order_dispatches.filter(function(arr) {
            return (arr["item_id"] == element.item_id &&  arr["brand_id"] == element.brand_id && arr["packing_id"] == element.packing_id);
        })

        var destination_packings = [];
        destinations.forEach(des => {
            var det = getDestinationDetail();
            det.id = des.id;
            det.qty = des.qty;
            det.destination = des.destination;
            destination_packings.push(det);
        });
        detail.destinations = destination_packings;
        form.packings.push(detail);
    });
});

const getDestinationsLength = (packing) =>{
    if(packing.destinations.length > 0){
        if(packing.destinations.length == 1 && packing.destinations[0].destination == ''){
            return "";
        }
        else{
            return ' (' +  packing.destinations.length + ')';
        }
    }
    return "";
}

const submitForm = () =>{
    form['postForm'](base_url.value+'/'+state.create_url)
    .then(function(response){
        console.log(response);
        if(response.success){
            Utilities.showPopMessage("Your data has been saved successfully!")
        }
    })
    .catch(function(error){
        Utilities.showPopMessage(error.message,"Invalid Data","error",'6000',true)
    });
}
</script>

<template>
    <AppLayout title="Sale Order Dispatch">
        <div>
            <sale-order-dispatch-modal v-if="state.showModal" @resetForm="resetForm" :detail=" form.packings[state.index]"
                @resetModal='resetModal' :sale_order_id="props.sale_order.id">
            </sale-order-dispatch-modal>
            <ListWrapper :title="'Dispatch for Sale Order No.: ' + sale_order.sale_order_no">
                    <template #button>
                        <ButtonComp @buttonClicked="submitForm()" type="save">
                            SAVE
                        </ButtonComp>
                    </template>
                  <template #table>
                <TableComponent>
                    <template #thead>
                        <thead  class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th  scope="col" class="py-4 px-6">Sr. no.</th>
                                <th  scope="col" class="py-4 px-6">Item</th>
                                <th  scope="col" class="py-4 px-6">Packing</th>
                                <th  scope="col" class="py-4 px-6">Brand</th>
                                <th  scope="col" class="py-4 px-6">Qty</th>
                                <th  scope="col" class="py-4 px-6">Destination</th>
                            </tr>
                        </thead>
                    </template>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 bg-indigo-500 bg-opacity-25 " v-for="(packing,index) in form.packings" :key="packing.random">
                            <td class="py-4 px-6" v-text="index+ 1"> </td>
                            <td class="py-4 px-6" v-text="packing.item_name"> </td>
                            <td class="py-4 px-6" v-text="packing.packing_name"> </td>
                            <td class="py-4 px-6" v-text="packing.brand_name"> </td>
                            <td class="py-4 px-6" v-text="packing.qty"> </td>
                            <td class="py-4 px-6" >
                                <ButtonComp type="save" size="sm" @buttonClicked="showModaDialog(index)">Destinations
                                    <span v-text="getDestinationsLength(packing)"> </span>
                                </ButtonComp>
                             </td>

                        </tr>
                        </TableComponent>
                  </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

