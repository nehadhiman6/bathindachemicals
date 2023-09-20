

<script setup>
import { ref, onMounted, reactive, nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import CityForm from '@/Pages/ProjectComponents/Masters/City/CityForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const props = defineProps(['districts']);
const page = usePage();
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

const editCity = (id) => {
    nextTick(()=>{
        state.formOpen = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#cities_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/cities-list",
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
                data: 'name',
            },

             {
                title: 'District',
                data: 'district_id',
                "render": function (data, type, row, meta) {
                     var str = row.state_name  ? row.district_name :'';
                    return  str;
                }
            },
            {
                title: 'State',
                data: 'state_id',
                "render": function (data, type, row, meta) {
                    var str = row.state_name  ? row.state_name :'';
                    return  str;
                }
            },
            {
                title: 'Actions',
                visible:canAny(page.props.granted_permissions,['cities-modify']),
                orderable: false,
                data: 'id',
                render: function (data, type, row, meta) {
                      return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i  data-item-id=' + data + '  class="mr-2 fas fa-pencil-alt text-slate-700" aria-hidden="true"></i> Edit </button>';
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editCity(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Cities">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                City
            </h2>
        </template>
        <div>
            <city-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
            :districts="props.districts">
            </city-form>
            <ListWrapper title="Cities List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['cities-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New City
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="cities_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

