

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import InvoiceForm from '@/Pages/ProjectComponents/Sale/Invoice/InvoiceForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny,snakeCaseToString} = globalMixin();
const page = usePage();
const props = defineProps([
    'sale_order',
    'invoice',
    'credit_limit',
    'balance',
    'delivery_terms',
]);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    readonly:false
});


const resetForm = () => {
    if(props.sale_order){
        Inertia.get(base_url.value+'/sale-orders');
    }else{
        state.form_id = 0;
        state.formOpen = false;
        state.readonly = false;
        state.table.ajax.reload(null, false);
    }

}

const editInvoice = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}


onMounted(() => {
    if(props.sale_order){
        state.formOpen = true;
    }
    setTable();
});

const setTable = () => {
    state.table = $('#invoice_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/invoices-list",
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
                title: 'Invoice No.',
                data: 'invoice_no_part',
                 "render": function (data, type, row, meta) {
                    return row.invoice_no ?row.invoice_no:'';
                }
            },
               {
                title: 'Sale Order No.',
                data: 'invoice_no',
                "render": function (data, type, row, meta) {
                    return row.sale_order ?row.sale_order['sale_order_no']:'';
                }
            },
             {
                title: 'Invoice Date',
                data: 'invoice_date',
            },

              {
                title: 'Invoice Type',
                data: 'invoice_type',
                "render": function (data, type, row, meta) {
                    return snakeCaseToString(data);
                }
            },
            {
                title: 'Actions',
                orderable: false,
                // visible:)/,
                data: 'id',
                render: function (data, type, row, meta) {
                    var str = '';
                    if(canAny(page.props.granted_permissions,['sale-orders-modify'])){
                        str += '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    }
                    if(canAny(page.props.granted_permissions,['sale-invoices-print'])){
                        str +='<br><a target="_blank" href="'+base_url.value+'/sale-invoice-print/'+data+'" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700">Invoice Print</a>';
                    }
                     if(canAny(page.props.granted_permissions,['sale-invoices-view'])){
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
                editInvoice(e.target.dataset.itemId);
            });

            $(".sale-invoice").unbind('click').on('click', function (e) {
                Inertia.get(base_url.value+'/sale-invoices/'+e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Sale Invoices">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sale Invoice
            </h2>
        </template>
        <div>
            <invoice-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
                :sale_order = "props.sale_order"
                :delivery_terms="delivery_terms"
                :credit_limit="credit_limit"
                :balance="balance"
                :readonly="state.readonly"
            >
            </invoice-form>
            <ListWrapper title="Sale Invoice" v-if="!props.sale_order ">
                <template #button>
                    <ButtonComp  v-if="state.formOpen == false && canAny(page.props.granted_permissions,['sale-orders-add'])" @buttonClicked="state.formOpen = true" type="new">
                        <i class="fa-solid fa-plus mr-2"></i> New Invoice
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="invoice_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

