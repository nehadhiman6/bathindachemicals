<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import NewRecordButton from '@/Components/CustomComponents/Buttons/NewRecordButton.vue';
import TitleComponent from '@/Components/CustomComponents/Sections/TitleComponent.vue';
import TableComponent from '@/Components/CustomComponents/Sections/TableComponent.vue';
import UserForm from '@/Pages/Users/UserForm.vue';
import { usePage } from '@inertiajs/vue3';
import {
    ref,
    onMounted,
    reactive
} from 'vue';
import globalMixin from '../../globalMixin';

const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0
})
const props = defineProps(['roles','data']);
const page = usePage();

onMounted(() => {
    setTable();
});
const {
    base_url,canAny
} = globalMixin();

const setTable = () => {
    state.table = $('#users_table').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: true,
        pageLength: 10,
        "ajax": {
            "url": base_url.value + "/users-list",
            "type": "GET",
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var index = meta.row + parseInt(meta.settings.json.start);
                    return index + 1;
                }
            },
            {
                title: 'Name',
                data: 'name',
            },
            {
                title: 'Email',
                data: 'email',
            },
            {
                title: 'Actions',
                orderable: false,
                data: 'id',
                 visible:canAny(page.props.granted_permissions,['users-modify']),
                render: function (data, type, row, meta) {
                    return '<button  data-item-id=' + data + ' class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded edit-item"> Edit </button>';

                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").on('click', function (e) {
                props.formOpen = false;
                editUser(e.target.dataset.itemId);
            });
        }
    });
}

const editUser = (id) => {
    state.formOpen = true;
    state.form_id = id;
}

const resetForm = () => {
    state.formOpen = false;
    state.form_id = 0;
    state.table.ajax.reload(null, false);
}
</script>

<template>
<AppLayout title="Users" :data="data">
    <template #header>
        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
            Users
        </span>
    </template>
    <user-form v-if="state.formOpen == true" :form_id="state.form_id" :roles="props.roles" @resetForm="resetForm">
    </user-form>

    <TitleComponent v-if="state.formOpen == false">
        <template #title>
            Users List
        </template>
        <template #right-content>
            <NewRecordButton @buttonClicked="state.formOpen = true &&canAny(page.props.granted_permissions,['users-modify'])">
                <i class="fa-solid fa-plus mr-2"></i> New User
            </NewRecordButton>
        </template>
    </TitleComponent>

    <TableComponent  v-show="state.formOpen == false" id="users_table">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            </thead>
    </TableComponent>
</AppLayout>
</template>
