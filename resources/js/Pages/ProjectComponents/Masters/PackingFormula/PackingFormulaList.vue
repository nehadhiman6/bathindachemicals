

<script setup>
import { ref, onMounted, reactive ,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage} from '@inertiajs/vue3';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import PackingFormulaForm from '@/Pages/ProjectComponents/Masters/PackingFormula/PackingFormulaForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['default_packing']);
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    close_qty:false,
    copy:false,
    readonly:false
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.close_qty = false;
    state.readonly = false;
    state.table.ajax.reload(null, false);
}

const editRateDifference = (id) => {
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
const copyPackingFormula = (id) =>{
    nextTick(() =>{
        state.formOpen = true;
        state.copy = true;
        state.form_id = id;
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
            "url": base_url.value+"/packing-formula-list",
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
                title: 'Item Name',
                data: 'item_name',
            },
            {
                title: 'WEF Date.',
                data: 'wef_date',
            },
             {
                title: 'Party Category ',
                data: 'party_category',
            },
            {
                title: 'Packing ',
                data: 'packing_name',
            },
            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['packing-formulas-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                    let str = '';
                        if(canAny(page.props.granted_permissions,['packing-formulas-modify']))
                            str = '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';

                        str += '<button data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 new-item"><i class="mr-2 fas fa-plus new-item text-slate-700" aria-hidden="true"  data-item-id=' + data + '></i> New </button>';

                        if(canAny(page.props.granted_permissions,['packing-formulas-view']))
                            str += '<button data-item-id=' + data + ' data-readonly="Y" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"> <i class="mr-2 fas fa-pencil-alt edit-item text-slate-700" aria-hidden="true" data-btn-type="View"  data-item-id=' + data + '></i>View </button>';
                      return str;
               }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                state.readonly = e.target.dataset.readonly == 'Y' ?true:false;
                editRateDifference(e.target.dataset.itemId);
            });

            $(".new-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                copyPackingFormula(e.target.dataset.itemId);
            });
        }
    });
}

</script>

<template>
    <AppLayout title="Packing Formulas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Packing Formula
            </h2>
        </template>
        <div>
            <packing-formula-form v-if="state.formOpen" :readonly="state.readonly" @resetForm="resetForm" :form_id="state.form_id" :close_qty="state.close_qty"
                :default_packing="default_packing" :copy="state.copy"
            >
            </packing-formula-form>
            <ListWrapper title="Packing Formulas">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['packing-formulas-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Packing Formula
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

