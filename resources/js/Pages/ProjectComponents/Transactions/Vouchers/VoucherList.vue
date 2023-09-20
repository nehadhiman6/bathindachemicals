

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import VoucherForm from '@/Pages/ProjectComponents/Transactions/Vouchers/VoucherForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['voucher_types']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.table.ajax.reload(null, false);
}

const editVoucher = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}



onMounted(() => {
    setTable();
});

const setTable = () => {
    var target  = 0;
    state.table = $('#vouchers_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/vouchers-list",
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
                title: 'Vcode',
                data: 'vcode',
            },
            {
                title: 'Voucher No.',
                data: 'voucher_no_part',
                 "render": function (data, type, row, meta) {
                    return row.voucher_no;
                }
            },
             {
                title: 'Voucher Date',
                data: 'voucher_date',
            },
            { title: 'Voucher Type', targets:target++, data: 'voucher_type',
                "render": function( data, type, row, meta) {
                    return data == 'R' ? 'Receipt'
                    :(data == 'P' ? 'Payment'
                    :(data == 'T' ? 'Transfer'
                    :(data == 'J' ? 'Journal'
                    :(data == 'I' ? 'Interest'
                    :(data)))));
                }
            },

            { title: 'TR Type', targets: target++, data: 'tr_type',
                "render": function( data, type, row, meta) {
                    return row.voucher_type == 'I' && data == 'C' ? 'Complete'
                    :(row.voucher_type == 'I' && data == 'T' ? 'TDS Only'
                    :(row.voucher_type == 'I' && data == 'A' ? 'Advance'
                    :((row.voucher_type == 'R' || row.voucher_type == 'P') && data == 'B' ? 'Bank'
                    :((row.voucher_type == 'R' || row.voucher_type == 'P') && data == 'C' ? 'Cash'
                    :(row.voucher_type == 'T' && data == 'D' ? 'Debit'
                    :(row.voucher_type == 'T' && data == 'C' ? 'Credit'
                    :(data)))))));
                }
            },

            { title: 'Account other', targets: target++,data: 'acid_other',
                "render": function( data, type, row, meta) {
                    var str =  '';
                    if(row.tr_type!="C"){
                        return row.account_other ? row.account_other.name:'';
                    }
                    return str;
                }
            },
            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,[
                    'receipt-vouchers-modify',
                    'payment-vouchers-modify',
                    'journal-vouchers-modify',
                    'interest-vouchers-modify',
                    'transfer-vouchers-modify',
                    'transfer-vouchers-print'
                ]),
                data: 'id',
                render: function (data, type, row, meta) {
                    var str='';
                        str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    if(canAny(page.props.granted_permissions,['transfer-vouchers-print'])){
                        str +='<br><a target="_blank" href="'+base_url.value+'/voucher-print/'+data+'?value='+row.vchr_type+'" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700">Print</a>';
                    }
                    return str;
               }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editVoucher(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Vouchers">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Voucher
            </h2>
        </template>
        <div>
            <voucher-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
                :voucher_types="voucher_types"
            >
            </voucher-form>
            <ListWrapper title="Vouchers">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,[
                        'receipt-vouchers-add',
                        'payment-vouchers-add',
                        'journal-vouchers-add',
                        'interest-vouchers-add',
                        'transfer-vouchers-add',
                    ])">
                        <i class="fa-solid fa-plus mr-2"></i> New Voucher
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="vouchers_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

