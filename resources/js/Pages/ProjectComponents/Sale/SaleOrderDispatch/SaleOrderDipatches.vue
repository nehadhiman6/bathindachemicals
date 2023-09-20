

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import SaleOrderDispatchList from '@/Pages/ProjectComponents/Sale/SaleOrderDispatch/SaleOrderDispatchList.vue';
   import InputError from '@/Components/InputError.vue';
import globalMixin from '../../../../globalMixin';
import { usePage } from '@inertiajs/vue3';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    readonly:false
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.readonly=false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editDispatch = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#disptach_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/sale-order-dispatches-list",
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
                title: 'Date',
                data: 'dispatch_date',
            },
            {
                title: 'Dispatch Advise No.',
                data: 'dispatch_advise_no_part',
                 "render": function (data, type, row, meta) {
                    return row.dispatch_advise_no;
                }
            },

            {
                title: 'Sale Order No',
                data: 'sale_oders_nos',
            },

            {
                title: 'Actions',
                orderable: false,
                // visible:,
                data: 'id',
                render: function (data, type, row, meta) {
                    var str ="";
                    if(canAny(page.props.granted_permissions,['sale-order-dispatches-modify'])){
                        if(row.dispatch_advise == 'Y'){
                            str+= '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700" aria-hidden="true"></i> Edit </button>';
                        }
                    }
                    str +='<br><a target="_blank" href="'+base_url.value+'/sale-dispatch-print/'+data+'" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 sale-dispatch-print">Dispatch Print</a>';
                    if(canAny(page.props.granted_permissions,['sale-order-dispatches-view'])){
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
                editDispatch(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Dispatches">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dispatches List
            </h2>
        </template>
        <div>
            <sale-order-dispatch-list v-if="state.formOpen" :readonly="state.readonly" @resetForm="resetForm" :form_id="state.form_id">
            </sale-order-dispatch-list>
            <ListWrapper title="Sale Order Dispatch" v-show="state.formOpen == false">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['sale-order-dispatches-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Sale Order Dispatch
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="disptach_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

