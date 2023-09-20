

<script setup>
import { ref, onMounted, reactive, nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import ItemForm from '@/Pages/ProjectComponents/Items/Item/ItemForm.vue';
import globalMixin from '../../../../globalMixin';
import { usePage} from '@inertiajs/vue3';
const page = usePage();
const { base_url,canAny} = globalMixin();
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    readonly:false
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.readonly = false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editItem = (id) => {
    nextTick(()=>{
    state.formOpen = true;
    state.form_id = id;
    })
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#item_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/items-list",
            "type": "GET",
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
                data: 'item_name',
            },
            {
                title: 'Item Code',
                data: 'item_code',
            },
            {
                title: 'Actions',
                orderable: false,
                // visible:,
                data: 'id',
                render: function (data, type, row, meta) {
                    let str = '';
                    if(canAny(page.props.granted_permissions,['items-modify'])){

                    }
                    str += '<button  data-item-id=' + data + ' data-readonly="N" class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i data-item-id=' + data + ' class="mr-2 fas fa-pencil-alt text-slate-700 edit-item" aria-hidden="true"></i> Edit </button>';
                    if(canAny(page.props.granted_permissions,['items-view'])){
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
                editItem(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Item ">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Item
            </h2>
        </template>
        <div>
            <item-form v-if="state.formOpen" @resetForm="resetForm" :readonly="state.readonly" :form_id="state.form_id" >
            </item-form>
            <ListWrapper title="Item  List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['items-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Item
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="item_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

