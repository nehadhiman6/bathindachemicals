

<script setup>
import { ref,computed,onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import globalMixin from '../../../globalMixin';
import PurchaseOrderForm from './PurchaseOrderForm.vue';
import QtyExtendedForm from './QtyExtendedForm.vue';
import DelExtendedForm from './DelExtendedForm.vue';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['pur_order_types']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    readonly:false,
    status:'',
    showQtyExt:false,
    det_id:0,
    rate_ext:0,
    qty_ext:0,
    del_extended:''
});

const resetForm = () => {
    state.form_id = 0;
    state.det_id = 0;
    state.rate_ext = 0;
    state.qty_ext = 0;
    state.del_extended = '';
    state.formOpen = false;
    state.showQtyExt = false;
    state.showDelExt = false;
    state.readonly =false
    state.table.ajax.reload(null, false);
}

onMounted(() => {
    setTable();
});

const editPurOrder = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.showQtyExt = false;
        state.showDelExt = false;
        state.form_id = id;
    });
}
const getQtyExtended=(det_id,qtyExt,rateExt)=>{
    nextTick(() => {
        state.formOpen = false;
        state.showQtyExt = true;
        state.det_id = det_id;
        state.rate_ext = rateExt;
        state.qty_ext = qtyExt;
        $(window).scrollTop(0);
    });
}

const getDelExtended=(id,delExt)=>{
    nextTick(() => {
        state.formOpen = false;
        state.showQtyExt = false;
        state.showDelExt = true;
        state.form_id = id;
        state.del_extended = delExt;
        $(window).scrollTop(0);
    });
}

const setTable = () => {
    let target = 0;
    state.table = $('#purchase_order_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/purchase-order-list",
            "type": "GET",
            "data":function(c){
                // c.company_id=props.company_id
            }
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        fixedColumns:{
            rightColumns: 1,
            leftColumns: 1
        },
        columnDefs: [{
                        title: 'S.No.',
                        'width': '10%',
                        targets: target++,
                        data: 'id',
                        "render": function (data, type, row, meta) {
                            let index = meta.row + parseInt(meta.settings.json.start);
                            let str = index + 1;
                            // str += "<a data-item-id='+row.id+' data-item-action='delete' class='delete ml-3'><img  src='"+Reliance.base_url+"/images/delete.png' width='20' height='20'></a>";
                            return str;
                        }
                    },

                    {
                        title: 'Delivery From',
                        targets: target++,
                        data: 'del_from'
                    },
                    {
                        title: 'Delivery To',
                        targets: target++,
                        data: 'del_to'
                    },
                    {
                        title: 'Seller',
                        targets: target++,
                        data: 'seller_name',
                    },
                    {
                        title: 'Buyer',
                        targets: target++,
                        data: 'buyer_acid',
                        "render": function (data, type, row, meta) {
                            return row.buyer ?row.buyer.name:'';
                        }
                    },
                    {
                        title: 'Payment Term',
                        targets: target++,
                        data: 'pay_term_id',
                        "render": function (data, type, row, meta) {
                            return row.pay_term ?row.pay_term.name:'';
                        }
                    },
                    // {
                    //     title: 'Broker',
                    //     targets: target++,
                    //     data: 'broker_id',
                    //     "render": function (data, type, row, meta) {
                    //         return row.broker ?row.broker.name:'';
                    //     }
                    // },

                    {
                        title: 'Item',
                        targets: target++,
                        data: 'item_name',
                    },

                    {
                        title: 'Unit',
                        targets: target++,
                        data: 'unit_name',
                        "render": function (data, type, row, meta) {
                            return row.broker ?row.broker.name:'';
                        }
                    },


                    {
                        title: 'Qty From',
                        targets: target++,
                        data: 'qty_from',

                    },

                    {
                        title: 'Qty To',
                        targets: target++,
                        data: 'qty_to',

                    },

                    {
                        title: 'Rate(Quantal)',
                        targets: target++,
                        data: 'rate',

                    },


                    {
                        title: 'Action',
                        targets: target++,
                        data: 'id',
                        "render": function (data, type, row, meta) {
                            var str = '';
                                if(canAny(page.props.granted_permissions,['purchase-order-modify'])){
                                    str += '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center edit-item align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                                }
                                if(canAny(page.props.granted_permissions,['qty-extend-order'])){
                                    str += '<button  data-det-id=' + row.det_id + ' data-qty-ext=' + row.qty_extended + ' data-rate-ext=' + row.rate_extended + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center edit-item align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 qty-extended"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700 qty-extended"  aria-hidden="true"  data-item-id=' + row.det_id + '></i> Qty Extended </button>';
                                }
                                if(canAny(page.props.granted_permissions,['del-extend-order'])){
                                    str += '<button  data-item-id="' +row.id+ '" data-del-ext="' +row.del_extended_date+ '"  class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center edit-item align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 del-extended"><i class="mr-2 fas fa-pencil-alt edit-del text-slate-700 del-extended" aria-hidden="true"  data-item-id=' + row.id + '></i> Delivery Extended </button>';
                                }
                                return str;
                        }
                    }
                ],
                "drawCallback": function (settings) {

                    $(".edit-item").on('click', function (e) {
                        self.formOpen = false;
                        editPurOrder(e.target.dataset.itemId);
                    });

                    $(".qty-extended").on('click', function (e) {
                        self.formOpen = false;
                        getQtyExtended(e.target.dataset.detId,e.target.dataset.qtyExt,e.target.dataset.rateExt);
                    });

                    $(".del-extended").on('click', function (e) {
                        self.formOpen = false;
                    console.log(e.target.dataset.delExt);
                        getDelExtended(e.target.dataset.itemId,e.target.dataset.delExt);
                    });
                }
                // "sScrollX": true,
    });
}

</script>

<template>
    <AppLayout  title="Purchase Orders">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" v-text="title">
            </h2>
        </template>
        <div>
            <purchase-order-form v-if="state.formOpen" :pur_order_types="pur_order_types" @resetForm="resetForm" :readonly="state.readonly" :form_id="state.form_id"
            >
            </purchase-order-form>
            <QtyExtendedForm v-if="state.showQtyExt" @resetForm="resetForm" :form_id="state.det_id" :qty="state.qty_ext" :rate="state.rate_ext"
            />

            <DelExtendedForm v-if="state.showDelExt" @resetForm="resetForm" :form_id="state.form_id" :del-extended="state.del_extended"
            />
            <ListWrapper title="Purchase Order List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['purchase-order-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Purchase Order
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="purchase_order_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

