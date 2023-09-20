

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import VoucherForm from '@/Pages/ProjectComponents/Transactions/Vouchers/VoucherForm.vue';
import globalMixin from '../../../../globalMixin';
import BillForm from '@/Pages/ProjectComponents/Bill/BillForm.vue';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['voucher_types']);
const showBill = ref(false);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    create_url:'bill-detail-add'
});

const form = reactive( new Form({
    id:0,
    vcode:'',
    voucher_date:'',
    drcr:'',
    ac_id:'',
    amount:'',
    acname:'',
    bills:[],
}));

function getBillDetail(){
    return {
        id: 0,
        branch_id:0,
        trans_date:'',
        dr_cr:'',
        amount:'',
        ref:'',
        ref_date:'',
        ref_type:'N',
        ref_key:'',
        random:Utilities.getRandomNumber(),
    }
}

const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    form.bills = [];
    state.table.ajax.reload(null, false);
}

const editVoucher = (id,acname,amount,drcr,acid,vcode,transDate) => {
    nextTick(() => {
        form.id = id;
        form.acname = acname.replace(/_/g, ' ');
        form.amount =amount;
        form.drcr =drcr;
        form.ac_id =acid;
        form.voucher_date=transDate;
        form.vcode = vcode;
        showBill.value = true;
    });
}
const updateStatus=()=>{
    form['postForm'](state.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                showBill.value = false;
                resetForm();
            }
        })
        .catch(function(error){
        });
}

const cancelStatus=()=>{
    showBill.value = false;
    resetForm();
}


onMounted(() => {
    if(form.id == 0){
        form.bills.push(getBillDetail());
    }
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
            "url": base_url.value+"/vouchers-detail-list",
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
            {
                title: 'Account Name',
                data: 'ac_name',
            },
            {
                title: 'Debit/Credit',
                data: 'drcr',
                "render": function (data, type, row, meta) {
                    return data == 'D' ? 'Debit':'Credit';
                }
            },

            {
                title: 'Narration',
                data: 'part',
            },

            {
                title: 'Weight',
                data: 'weight',
            },

            {
                title: 'Amount',
                data: 'amount',
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                render: function (data, type, row, meta) {
                    var str='';
                    var ac_name = row.ac_name.replace(/ /g, '_');
                        str += '<button  data-item-id=' + data + ' data-vcode=' + row.vcode + ' data-trans-date=' + row.voucher_date + ' data-ac-id=' + row.ac_id + ' data-acname=' + ac_name + ' data-amount=' + row.amount + ' data-drcr=' + row.drcr + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Bill </button>';
                    return str;
               }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editVoucher(e.target.dataset.itemId,e.target.dataset.acname,e.target.dataset.amount,e.target.dataset.drcr,e.target.dataset.acId,e.target.dataset.vcode,e.target.dataset.transDate);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Vouchers">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bills
            </h2>
        </template>
        <div>
            <bill-form
                v-if="showBill"
                :form="form"
                :entry="form"
                :bills="form.bills"
                :index ="index"
                :account-name ="form.acname"
                :amount = "form.amount"
                :debit-credit = "form.drcr"
                :key = "form.random"
                @updateStatus = "updateStatus"
                @cancelStatus = "cancelStatus"
            >
            </bill-form>
            <!-- <voucher-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
                :voucher_types="voucher_types"
            >
            </voucher-form> -->
            <ListWrapper title="Vouchers">
                <template #table>
                    <table id="vouchers_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

