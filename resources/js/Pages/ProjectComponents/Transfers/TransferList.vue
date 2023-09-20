

<script setup>
import { ref,computed,onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import globalMixin from '../../../globalMixin';
import TransferForm from './TransferForm.vue';

const { base_url,snakeCaseToString,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['master_type']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    readonly:false,
    status:''
});

const title = computed(() => {
  return snakeCaseToString(props.master_type);
})

const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.readonly =false
    state.table.ajax.reload(null, false);
}

const editGstDocument = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}


onMounted(() => {
    if(props.master_type == 'issue'){
        state.status= 'I';
    }
    else{
        state.status= 'R';
    }
    setTable();

});

const editTransfer = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}

const setTable = () => {
    let target = 0;
    state.table = $('#sale_order_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/"+props.master_type.replace(/_/g, "-")+'-list'+'/'+state.status,
            "type": "GET",
            "data":function(c){
                // c.company_id=props.company_id
            }
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
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
                        title: 'Branch',
                        targets: target++,
                        data: 'branch'
                    },
                    {
                        title: 'Issue Date',
                        targets: target++,
                        data: 'iss_date'
                    },
                    {
                        title: 'Vehical No',
                        targets: target++,
                        data: 'vehical_no'
                    },
                    {
                        title: 'Challan No',
                        targets: target++,
                        data: 'iss_doc_no_print'
                    },
                    // {
                    //     title: 'Gr Date',
                    //     targets: target++,
                    //     data: 'gr_date'
                    // },

                    {
                        title: 'Net Weight',
                        targets: target++,
                        data: 'net_wt'
                    },

                    {
                        title: 'Action',
                        targets: target++,
                        data: 'id',
                        "render": function (data, type, row, meta) {
                            var str = '';
                           let edit = '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center edit-item align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                            if (props.master_type == "issue" && row.status == 'I') {
                                if(canAny(page.props.granted_permissions,['issue-modify'])){
                                    str += edit;
                                }
                                if(canAny(page.props.granted_permissions,['issue-print']))
                                    str +='<br><a target="_blank" href="'+base_url.value+"/"+props.master_type+'-print/'+data+'" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700">Issue Print </a>';

                            }


                            if (props.master_type == "receipt") {
                                if(canAny(page.props.granted_permissions,['receipt-modify'])){
                                    if(row.status == 'I'){
                                        str += '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center edit-item align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i>Received</button>';
                                    }
                                    else{
                                        str += edit;
                                    }
                                }
                                if(canAny(page.props.granted_permissions,['receipt-modify'])){
                                    str +='<br><a target="_blank" href="'+base_url.value+"/"+props.master_type+'-print/'+data+'" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700">Received Print </a>';
                                }
                            }

                            return str;
                        }
                    }
                ],
                "drawCallback": function (settings) {

                    $(".edit-item").on('click', function (e) {
                        self.formOpen = false;
                        editTransfer(e.target.dataset.itemId);
                    });

                    // $(".delete-gstdoc").unbind('click').on('click', function (e) {
                    //     self.deleteDocs(e.target.dataset.itemId);
                    // });
                }
                // "sScrollX": true,
    });
}

</script>

<template>
    <AppLayout  :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" v-text="title">
            </h2>
        </template>
        <div>
            <transfer-form v-if="state.formOpen" @resetForm="resetForm" :readonly="state.readonly" :form_id="state.form_id"
                :title="title" :create_url="master_type"
            >
            </transfer-form>
            <ListWrapper :title="title +' List'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && props.master_type == 'issue'">
                        <i class="fa-solid fa-plus mr-2"></i> New <span v-text="title"></span>
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

