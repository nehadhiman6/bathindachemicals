

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import BranchBankAccountForm from '@/Pages/ProjectComponents/Masters/BranchBankAccount/BranchBankAccountForm.vue';
import globalMixin from '../../../../globalMixin';
import { usePage } from '@inertiajs/vue3';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['branch_id','branch']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editBankAccount = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#branch_bank_acc_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/branch-bank-accounts-list",
            "type": "GET",
            "data":function(c){
                c.branch_id=props.branch_id
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
                title: 'Account Number',
                data: 'bank_account_number',
            },

            {
                title: 'IFSC',
                data: 'ifsc_id',
                "render": function (data, type, row, meta) {
                    var str = row.ifsc ? row.ifsc.ifsc_code:'';
                    return  str;
                }
            },

             {
                title: 'Beneficiary Name',
                data: 'benificiary_name',
            },

            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['branches-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                      return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editBankAccount(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Branch Accounts ">
        <div>
            <branch-bank-account-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id" :branch_id="props.branch_id"
            :branch="props.branch" >
            </branch-bank-account-form>
            <ListWrapper :title="props.branch.name+'\'s Bank Accounts'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['branches-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Bank Account
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="branch_bank_acc_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

