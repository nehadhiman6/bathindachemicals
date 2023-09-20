

<script setup>
import { ref, onMounted, reactive, nextTick,computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import PackingForm from '@/Pages/ProjectComponents/Masters/Packing/PackingForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny,snakeCaseToString} = globalMixin();
const page = usePage();
const props = defineProps(['packing_type']);
const title = computed(() => {
  return snakeCaseToString(props.packing_type);
});

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

const editPacking = (id) => {
    nextTick(()=>{
    state.formOpen = true;
    state.form_id = id;})
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    let pack_type = '';
        if(props.packing_type == 'oil_packings'){
            pack_type = 'O';
        }
        else if(props.packing_type == 'loose_packings'){
            pack_type = 'L';
        }
        else{
            pack_type = 'Q';
        }
    state.table = $('#packings_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/packings-list",
            "type": "GET",
            "data":function(d){
                d.type = pack_type;
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
                title: 'Rate Difference Applicable',
                data: 'rate_diff_applicable',
                visible: props.packing_type == 'oil_packings',
                 render: function (data, type, row, meta) {
                    return  data == 'N' ? 'No':'Yes';
                }
            },
            {
                title: 'Order No.',
                data: 'order_no',
                visible: props.packing_type == 'oil_packings'
            },
            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['oil-packings-modify','loose-packings-modify','liquor-packings-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                    return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editPacking(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Packing
            </h2>
        </template>
        <div>
            <packing-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
            :packing_type="props.packing_type">
            </packing-form>
            <ListWrapper :title="title+ ' List'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['oil-packings-add','loose-packings-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Packing
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="packings_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

