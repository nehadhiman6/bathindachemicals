

<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import TdsSectionForm from '@/Pages/ProjectComponents/Masters/TdsSection/TdsSectionForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps([]);

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
    state.table = $('#ifscs_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/tds-section-list",
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
                title: 'Section',
                data: 'section',
            },
            {
                title: 'TDS Rate1',
                data: 'rate1',
            },

            {
                title: 'GL Account1',
                data: 'ac_id1',
                "render": function (data, type, row, meta) {
                    var str = row.account1 ? row.account1.name :'';
                    return  str;
                }
            },
            {
                title: 'TDS Rate2',
                data: 'rate2',
            },
            {
                title: 'GL Account2',
                data: 'ac_id2',
                "render": function (data, type, row, meta) {
                    var str = row.account2 ? row.account2.name :'';
                    return  str;
                }
            },
            {
                title: 'TDS Rate3',
                data: 'rate3',
            },
            {
                title: 'GL Account3',
                data: 'ac_id3',
                "render": function (data, type, row, meta) {
                    var str = row.account3 ? row.account3.name :'';
                    return  str;
                }
            },
            {
                title: 'Higher Rate None Pan Cases',
                data: 'non_pan_rate',
            },

            {
                title: 'GL Account4',
                data: 'ac_id4',
                "render": function (data, type, row, meta) {
                    var str = row.account4 ? row.account4.name :'';
                    return  str;
                }
            },
            {
                title: 'Higher Rate',
                data: 'higher_rate',
            },
            {
                title: 'GL Account5',
                data: 'ac_id5',
                "render": function (data, type, row, meta) {
                    var str = row.account5 ? row.account5.name :'';
                    return  str;
                }
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                visible:canAny(page.props.granted_permissions,['tds-section-modify']),
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
    <AppLayout title="TDS Section">
        <template #header>
        </template>
        <div>
            <tds-section-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id">
            </tds-section-form>
            <ListWrapper title="TDS Section List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['tds-section-add'])">
                        <i class="fa-solid fa-plus mr-2"></i>New TDS Section
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="ifscs_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

