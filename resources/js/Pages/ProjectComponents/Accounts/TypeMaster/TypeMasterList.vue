

<script setup>
import { ref, onMounted, reactive,computed,nextTick} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import TypeMasterForm from '@/Pages/ProjectComponents/Accounts/TypeMaster/TypeMasterForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,snakeCaseToString,canAny,joinArraytoString} = globalMixin();
const page = usePage();
const props = defineProps(['master_type']);

const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
});

const title = computed(() => {
  return snakeCaseToString(props.master_type);
})


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editTypeMaster = (id) => {
    nextTick(()=>{
        state.formOpen = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#type_master_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/type-masters-list",
            "type": "GET",
            "data":function(d){
                d.type = props.master_type
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
                title: 'Limit',
                visible:props.master_type == 'limit_sub_groups',
                data: 'limit',
            },
             {
                title: 'Accounts',
                visible:props.master_type == 'limit_sub_groups',
                render: function (data, type, row, meta) {
                    var str = [];
                    row.limit_accounts.forEach(element => {
                        if(element.account){
                            str.push(element.account.name);
                        }
                    });
                    return joinArraytoString(str);
                }
            },

            {
                title: 'Actions',
                visible:canAny(page.props.granted_permissions,['party-sub-groups-modify','clients-modify','vendors-modify','gl-sub-groups-modify','limit-sub-groups-modify']),
                orderable: false,
                data: 'id',
                render: function (data, type, row, meta) {
                    return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editTypeMaster(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight" v-text="title">
            </h2>
        </template>
        <div>
            <type-master-form v-if="state.formOpen " @resetForm="resetForm" :form_id="state.form_id" :title="title" :create_url="master_type">
            </type-master-form>
            <ListWrapper :title="title +' List'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['party-sub-groups-modify','clients-modify','vendors-modify','gl-sub-groups-modify','limit-sub-groups-modify'])">
                        <i class="fa-solid fa-plus mr-2"></i> New <span v-text="title"></span>
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="type_master_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

