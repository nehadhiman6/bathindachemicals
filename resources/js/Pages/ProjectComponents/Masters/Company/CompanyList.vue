

<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import CompanyForm from '@/Pages/ProjectComponents/Masters/Company/CompanyForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const props = defineProps(['years']);
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

const editCompany = (id) => {
    nextTick(()=>{
        state.formOpen = true;
        state.form_id = id;
    })

}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#companies_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/companies-list",
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
                data: 'company_name',
            },
            {
                title: 'Print Name',
                data: 'print_name',
            },
            {
                title: 'GST Number',
                data: 'gst_number',
            },
            {
                title: 'PAN Number',
                data: 'pan_number',
            },
            {
                title: 'Remarks',
                data: 'remarks',
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                render: function (data, type, row, meta) {
                    var str = '';
                    if(canAny(page.props.granted_permissions,['companies-modify'])){
                        str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    }
                    if(canAny(page.props.granted_permissions,['branches-modify'])){
                        str+='<button  data-item-id=' + data + '  class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-branch"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-branch" aria-hidden="true"  data-item-id=' + data + '></i> Branches </button>';
                    }
                    return str;
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editCompany(e.target.dataset.itemId);
            });
            $(".edit-branch").unbind('click').on('click', function (e) {
                Inertia.get(base_url.value+'/branches/'+e.target.dataset.itemId);
            });


        }
    });
}

</script>

<template>
    <AppLayout title="Companies">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Company
            </h2>
        </template>
        <div>
            <company-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"
            :years="years">
            </company-form>
            <ListWrapper title="Companies List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['companies-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Company
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="companies_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

