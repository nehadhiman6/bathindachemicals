

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import BranchForm from '@/Pages/ProjectComponents/Masters/Branch/BranchForm.vue';
import SaleOrderForm from '@/Pages/ProjectComponents/Sale/SaleOrder/SaleOrderForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['transport_types']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    readonly:false
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.readonly =false
    state.table.ajax.reload(null, false);
}

const editSaleOrder = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}


onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#sale_order_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/sale-orders-list",
            "type": "GET",
            "data":function(c){
                c.company_id=props.company_id
            }
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var str = meta.row + parseInt(meta.settings.json.start) +1;
                    return  str;
                }
            },
            {
                title: 'Client Name',
                data: 'client_name',
            },
            {
                title: 'Sale Order No.',
                data: 'sale_order_no',
            },
             {
                title: 'Sale Order Date',
                data: 'sale_order_date',
            },
              {
                title: 'Packed/Loose',
                data: 'packed_loose',
            },
            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['sale-orders-modify']),
                // data: 'id',
                render: function (data, type, row, meta) {
                    var str = '';
                    if(canAny(page.props.granted_permissions,['sale-orders-modify'])){
                        str += '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    }
                    if(canAny(page.props.granted_permissions,['sale-invoices']) && row.sale_invoice_count == 0){
                        str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 sale-invoices"><i class="mr-2 fas fa-pencil-alt sale-invoices text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Sale Invoice </button>';
                    }
                    if(canAny(page.props.granted_permissions,['sale-orders-view'])){
                        str += '<button data-item-id=' + data + ' data-readonly="Y" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"> <i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true" data-btn-type="View"  data-item-id=' + data + '></i>View </button>';
                    }
                    return str;
               }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                state.readonly = e.target.dataset.readonly == 'Y' ?true:false;
                editSaleOrder(e.target.dataset.itemId);
            });


            $(".sale-invoices").unbind('click').on('click', function (e) {
                Inertia.get(base_url.value+'/sale-invoices/'+e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Sale Orders">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sale Orders
            </h2>
        </template>
        <div>
            <sale-order-form v-if="state.formOpen" @resetForm="resetForm" :readonly="state.readonly" :form_id="state.form_id"
                :transport_types="transport_types"
            >
            </sale-order-form>
            <ListWrapper title="Sale Orders">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['sale-orders-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Sale Order
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="sale_order_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

