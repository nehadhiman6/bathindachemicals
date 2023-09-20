

<script setup>
import { ref, onMounted, reactive,nextTick ,computed} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import AccountForm from '@/Pages/ProjectComponents/Accounts/Account/AccountForm.vue';
import AttachmentUploadModal from '@/Pages/AttachmentComponents/AttachmentUploadModal.vue';
import globalMixin from '../../../../globalMixin';
import resources from '../../../../Services/api/resources';
const { base_url,canAny} = globalMixin();
const { getSharedResources,saveSharedResources} = resources();
const props = defineProps(['type']);
const form = reactive(new Form({
    form_id:0,
    target_id:0,
    resources:[]
}));
const title = computed(() => props.type == 'party' ? 'Accounts' :'GL Accounts');
const page = usePage();
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    showAttachment:false,
    readonly:false
});


const resetForm = () => {
    state.form_id = 0;
    form.target_id = 0;
    form.resources = [];
    state.formOpen = false;
    state.readonly = false;
    state.table.ajax.reload(null, false);
}

const editAccount = (id) => {
    nextTick(()=>{
        state.formOpen = true;
        state.form_id = id;
    });

}

onMounted(() => {
    setTable();
});

const updateResources = (save= false,files) =>{
    try{
        form.resources = files;
        saveSharedResources('accounts',form)
        .then(function(){
            state.showAttachment = false;
        });
    }
    catch(error){
        console.log('error',error)
    }
}
const setTable = () => {
    state.table = $('#accounts_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/accounts-list",
            "type": "GET",
            "data" :function(p){
                p.type = props.type;
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
             {
                title: 'Acc. Code',
                data: 'ac_code',
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                render: function (data, type, row, meta) {
                    var str ='';
                    if(canAny(page.props.granted_permissions,['accounts-modify'])){
                        str+= '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    }
                    if(canAny(page.props.granted_permissions,['accounts-opening']) && props.type == 'party'){
                        str+= '<button  data-opening-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700   opening-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  opening-item" aria-hidden="true"  data-opening-id=' + data + '></i> Opening </button>';
                    }
                      if(canAny(page.props.granted_permissions,['accounts-attachment']) && props.type == 'party'){
                        str+= '<button  data-account-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  attachments-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  attachments-item" aria-hidden="true"  data-attachments-id=' + data + '></i> Attachments </button>';
                    }
                    if(canAny(page.props.granted_permissions,['lower-tds-setting']) && props.type == 'party'){
                        str+="<br><a href='"+ base_url.value + '/lower-tds-setting/'+row.id+"' class='inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700' target = _blank><i class='mr-2 fas fa-pencil-alt text-slate-700  edit-item' aria-hidden='true'></i>Lower TDS Setting</a>"
                    }

                    if(canAny(page.props.granted_permissions,['accounts-view'])){
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
                editAccount(e.target.dataset.itemId);
            });

            $(".opening-item").unbind('click').on('click', function (e) {
                // openingAccount(e.target.dataset.itemId);
                Inertia.get(base_url.value+'/opening/'+e.target.dataset.openingId);
            });

            $(".attachments-item").unbind('click').on('click', function (e) {
                form.target_id = e.target.dataset.accountId;
                getSharedResources('accounts',form.target_id)
                .then(function(response){
                    if(response.data.resources){
                        form.resources = response.data.resources;
                        state.showAttachment = true;
                    }
                })
                .catch(function(error){
                });
            });

        }
    });


}

</script>

<template>
    <AppLayout :title="title">
        <attachment-upload-modal v-if="state.showAttachment"  title="Upload"
            @updateStatus="state.showAttachment = false"
            @updateResources="updateResources"
            :initial-resources ="form.resources"
            :number-attachments="1"
            :errors= "form.errors"
            >
        </attachment-upload-modal>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Account
            </h2>
        </template>
        <div>
            <Account-form v-if="state.formOpen" @resetForm="resetForm" :readonly="state.readonly" :form_id="state.form_id" :type="props.type">
            </Account-form>
            <ListWrapper :title="title +' List'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['accounts-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Account
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="accounts_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

