

<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import LowerTdsForm from '@/Pages/ProjectComponents/Accounts/LowerTds/LowerTdsForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['account']);

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

const editDistrict = (id) => {
    nextTick(()=>{
        state.formOpen = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/lower-tds-setting-list",
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
                title: 'TDS Section',
                data: 'acid1',
                "render": function (data, type, row, meta) {
                    var str = row.tds_section ? row.tds_section.section :'';
                    return  str;
                }
            },
            {
                title: 'Certificate No.',
                data: 'certificate_no',
            },
            {
                title: 'Date',
                data: 'date',
            },

            {
                title: 'TDS Account',
                data: 'acid_tds',
                "render": function (data, type, row, meta) {
                    var str = row.tds_account ? row.tds_account.name :'';
                    return  str;
                }
            },
            {
                title: 'Valid From Date',
                data: 'from_date',
            },

            {
                title: 'Valid To Date',
                data: 'to_date',
            },

            {
                title: 'Amount',
                data: 'amount',
            },

            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                visible:canAny(page.props.granted_permissions,['lower-tds-setting-modify']),
                render: function (data, type, row, meta) {
                     return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                }

            }

        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editDistrict(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout :title="'Lower TDS Setting('+props.account.name+')'">
        <template #header>
        </template>
        <div>
            <lower-tds-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id">
            </lower-tds-form >
            <ListWrapper :title="'Lower TDS Setting List ('+props.account.name+')'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['lower-tds-setting-add'])">
                        <i class="fa-solid fa-plus mr-2"></i>New Lower TDS Setting
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

