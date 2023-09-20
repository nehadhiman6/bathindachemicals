

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import BranchForm from '@/Pages/ProjectComponents/Masters/Branch/BranchForm.vue';
import globalMixin from '../../../../globalMixin';
import { usePage } from '@inertiajs/vue3';
import BranchToSkipHeader from './BranchToSkipHeader.vue';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['company_id','company_name']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    show:false,
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.show = false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editBranch = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}

const addBranch = (id) =>{
    nextTick(() => {
        state.formOpen = false;
        state.show = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#branches_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/branches-list",
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
                title: 'Name',
                data: 'name',
            },

            //  {
            //     title: 'Branches',
            //     data: 'district_id',
            //     "render": function (data, type, row, meta) {
            //         var str = row.state ? row.state.name :'';
            //         return  str;
            //     }
            // },
            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['branches-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                    var str ="";
                    if(canAny(page.props.granted_permissions,['branch-bank-accounts-add']))
                      str+= '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 add-account"><i class="mr-2 fas fa-pencil-alt text-slate-700 add-account"   data-item-id=' + data + ' aria-hidden="true"></i> Bank Accounts </button>';
                    str+= '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700 edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    if(canAny(page.props.granted_permissions,['skip-branches']))
                        str+= '<button  data-item-id=' + data + ' class="add-branch inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700 edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Branches To Skip</button>';

                    return str;
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editBranch(e.target.dataset.itemId);
            });

            $(".add-account").unbind('click').on('click', function (e) {
                state.formOpen = false;
                Inertia.get(base_url.value +'/branch-bank-accounts/'+ e.target.dataset.itemId);
            });

            $(".add-branch").unbind('click').on('click', function (e) {
                state.formOpen = false;
                addBranch(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Branches">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Branch
            </h2>
        </template>
        <div>
            <branch-to-skip-header v-if="state.show" :show="state.show" @resetForm="resetForm" :form_id="state.form_id">
            </branch-to-skip-header>
            <branch-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id" :company_id="props.company_id"
            :branches="props.districts" :company_name="props.company_name">
            </branch-form>
            <ListWrapper :title="props.company_name+' Branches'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['branches-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Branch
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="branches_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

