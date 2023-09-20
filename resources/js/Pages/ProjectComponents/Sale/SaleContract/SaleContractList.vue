

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import BranchForm from '@/Pages/ProjectComponents/Masters/Branch/BranchForm.vue';
import SaleContractForm from '@/Pages/ProjectComponents/Sale/SaleContract/SaleContractForm.vue';
import AttachmentUploadModal from '@/Pages/AttachmentComponents/AttachmentUploadModal.vue';
import globalMixin from '../../../../globalMixin';
import resources from '../../../../Services/api/resources';

const { base_url,canAny} = globalMixin();
const { getCompanyResources,saveCompanyResources} = resources();
const page = usePage();
const props = defineProps(['default_packing','brokerage_parameters']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    close_qty:false,
    showAttachment:false,
    readonly:false

});

const form = reactive(new Form({
    form_id:0,
    target_id:0,
    resources:[]
}));

const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.close_qty = false;
    state.readonly = false;
    state.table.ajax.reload(null, false);
}

const editSaleContract = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}

const closeQtyContract = (id) => {
    nextTick(() => {
        state.close_qty = true;
        state.form_id = id;
        state.formOpen = true;
    });
}



onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#sale_contract_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/sale-contracts-list",
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
                data: 'client_name',
            },
            {
                title: 'Contract No.',
                data: 'contract_no',
            },
             {
                title: 'Contract Date',
                data: 'contract_date',
            },
            {
                title: 'Actions',
                orderable: false,
                // visible:canAny(page.props.granted_permissions,['sale-contracts-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                    var str = '';
                    if(canAny(page.props.granted_permissions,['sale-contracts-modify'])){
                        str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    }
                    str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 close-qty"><i class="mr-2 fas fa-pencil-alt close-qty text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Close Qty </button>';
                    if(canAny(page.props.granted_permissions,['sale-contracts-attachment'])){
                        str+= '<button  data-account-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  attachments-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  attachments-item" aria-hidden="true"  data-attachments-id=' + data + '></i> Attachments </button>';
                    }
                    if(canAny(page.props.granted_permissions,['sale-contracts-view'])){
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
                editSaleContract(e.target.dataset.itemId);
            });

            $(".close-qty").unbind('click').on('click', function (e) {
                state.formOpen = false;
                state.close_qty = false;
                closeQtyContract(e.target.dataset.itemId);
            });

             $(".attachments-item").unbind('click').on('click', function (e) {
                form.target_id = e.target.dataset.accountId;
                getCompanyResources('sale-contracts',form.target_id)
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

const updateResources = (save= false,files) =>{
    try{
        form.resources = files;
        saveCompanyResources('sale-contracts',form)
        .then(function(){
            state.showAttachment = false;
        });
    }
    catch(error){
        console.log('error',error)
    }
}
</script>

<template>
    <AppLayout title="Sale Contracts">
        <attachment-upload-modal v-if="state.showAttachment"  title="Upload"
            @updateStatus="state.showAttachment = false"
            @updateResources="updateResources"
            path="company-attachments"
            path-thumbnail="company-attachments-thumbnail"
            :initial-resources ="form.resources"
            :errors= "form.errors"
            >
        </attachment-upload-modal>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sale Contract
            </h2>
        </template>
        <div>
            <sale-contract-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id" :close_qty="state.close_qty"
                :default_packing="default_packing"
                :brokerage_parameters="brokerage_parameters"
                :readonly="state.readonly"
            >
            </sale-contract-form>
            <ListWrapper title="Sale Contracts">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['sale-contracts-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Sale Contract
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="sale_contract_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

