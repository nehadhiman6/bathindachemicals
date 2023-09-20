
<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import UserForm from '@/Pages/ProjectComponents/Users/UserForm.vue';
import UserCompanies from '@/Pages/ProjectComponents/Users/UserCompanies.vue';
import UserBranches from '@/Pages/ProjectComponents/Users/UserBranches.vue';
import globalMixin from '../../../globalMixin';

const { base_url} = globalMixin();
const props = defineProps(['roles']);
const state = reactive({
    formOpen: false,
    type:'',
    formCompanyOpen:false,
    formBranchOpen:false,
    table: null,
    form_id: 0,
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.formCompanyOpen = false;
    state.formBranchOpen = false;
    state.type = '';
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editUser = (id) => {
    state.formOpen = true;
    state.form_id = id;
}

 const  editUserCompanies = (id)=>{
    state.formCompanyOpen = true;
    state.form_id = id;

 }
 const  editUserBranches = (id,type='')=>{
    state.formBranchOpen = true;
    state.form_id = id;
    state.type = type;

 }

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#users_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/users-list",
            "type": "GET",
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var index = meta.row + parseInt(meta.settings.json.start);
                    return index + 1;
                }
            },
            {
                title: 'Name',
                data: 'name',
            },
            {
                title: 'Email',
                data: 'email',
            },
             {
                title: 'Role',
                data:'id',
                "render": function (data, type, row, meta) {
                      return row.roles.length>0 ? row.roles[0].name:'';
                }
            },
            {
                title: 'Companies',
                data:'id',
                "render": function (data, type, row, meta) {
                    var companies = [];
                    row.user_companies.forEach(element => {
                        if(element.company){
                            companies.push(element.company.company_name);
                        }
                    });
                    let company_str = Utilities.joinArrayAsString(companies);
                    var str= company_str;
                    str +=  '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-companies-item"><i data-item-id=' + data + ' class="mr-2 fas fa-pencil-alt text-slate-700 edit-companies-item" aria-hidden="true"></i>  </button>';
                   return str;
                }
            },
            {
                title: 'Branches',
                data:'id',
                 "render": function (data, type, row, meta) {
                    var branches = [];
                    row.user_branches.forEach(element => {
                        if(element.branch){
                            branches.push(element.branch.name);
                        }
                    });
                    let branch_str = Utilities.joinArrayAsString(branches);
                    var str= branch_str;
                    str +=   '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-branches-item"><i data-item-id=' + data + '  class="mr-2 fas fa-pencil-alt text-slate-700 edit-branches-item" aria-hidden="true"></i>  </button>';
                   return str;
                }
            },
            {
                title: 'Report Branches',
                data:'id',
                 "render": function (data, type, row, meta) {
                    var branches = [];
                    row.report_user_branches.forEach(element => {
                        if(element.branch){
                            branches.push(element.branch.name);
                        }
                    });
                    let branch_str = Utilities.joinArrayAsString(branches);
                    var str= branch_str;
                    str +=   '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-report-branches-item"><i data-item-id=' + data + '  class="mr-2 fas fa-pencil-alt text-slate-700 edit-report-branches-item" aria-hidden="true"></i>  </button>';
                   return str;
                }
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                "render": function (data, type, row, meta) {
                    var str = '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i data-item-id=' + data + '  class="mr-2 fas fa-pencil-alt text-slate-700 edit-item" aria-hidden="true"></i> Edit </button>';
                   return str;
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editUser(e.target.dataset.itemId);
            });
             $(".edit-companies-item").unbind('click').on('click', function (e) {
                state.formCompanyOpen = false;
                editUserCompanies(e.target.dataset.itemId);
            });
             $(".edit-branches-item").unbind('click').on('click', function (e) {
                state.formBranchOpen = false;
                editUserBranches(e.target.dataset.itemId);
            });
             $(".edit-report-branches-item").unbind('click').on('click', function (e) {
                state.formBranchOpen = false;
                editUserBranches(e.target.dataset.itemId,'report');
            });



        }
    });
}

</script>

<template>
    <AppLayout title="Users">

        <div>
            <user-companies v-if="state.formCompanyOpen"
                :user_id = 'state.form_id'
                @resetForm="resetForm">
            </user-companies>
            <user-branches  v-if="state.formBranchOpen"
                :user_id = 'state.form_id'
                :type="state.type"
                @resetForm="resetForm">
            </user-branches>
            <user-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
            :roles="roles">
            </user-form>
            <ListWrapper title="Users List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false">
                        <i class="fa-solid fa-plus mr-2"></i> New User
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="users_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

