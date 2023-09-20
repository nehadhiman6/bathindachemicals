<script setup>
    import { ref, onMounted, reactive } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import UserForm from '@/Pages/ProjectComponents/Users/UserForm.vue';
    import globalMixin from '../../../globalMixin';

    const props = defineProps(['roles']);
    const {base_url} = globalMixin();

    const addPermissions = (id) =>{
        Inertia.get(base_url.value+'/roles/'+id +'/permissions', { }, { preserveState: true });
    }

    const addRole = (id) =>{
        Inertia.get(base_url.value+'/roles/'+id +'/add', { }, { preserveState: true });
    }



    onMounted(() => {
       $('#roles_list').DataTable();
    });
</script>

<template>
    <AppLayout title="Roles">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role
            </h2>
        </template>
        <div>
            <ListWrapper title="Roles List">
                <template #table>
                    <table id="roles_list" width="100%" class="row-border stripe">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="py-4 px-6">Sr. no</th>
                            <th scope="col" class="py-4 px-6">Role</th>
                            <th scope="col" class="py-4 px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='(role,index) in roles' :key="role.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white" v-text="index+1"></td>
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white" v-text="role.name"></td>
                            <td>
                                <ButtonComp type="save" size="sm" @buttonClicked="addPermissions(role.id)">Permissions</ButtonComp>
                                <ButtonComp  type="save" size="sm" class="ml-1" @buttonClicked="addRole(role.id)">Add Role</ButtonComp>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
