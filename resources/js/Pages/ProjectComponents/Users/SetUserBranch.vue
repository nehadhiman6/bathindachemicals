<script setup>
    import {    ref,  computed,  onBeforeMount,    reactive, nextTick} from 'vue';
    import globalMixin from '../../../globalMixin';
    import FormSection from '@/Components/FormSection.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
    import { usePage } from '@inertiajs/vue3';
     import { Inertia } from '@inertiajs/inertia';
     import AppLayout from '@/Layouts/AppLayout.vue';

    const { base_url} = globalMixin();
    const page  = usePage();
    const data = reactive({initialBranch:[],selectedBranch:[]});
    const form = reactive( new Form({
        form_id: 0,
        current_branch_id:0
    }));
    onBeforeMount(() => {
        if(page.props && page.props.session_branch){
            data.initialBranch = [{'id':page.props.session_branch.id,'text':page.props.session_branch.name}];
            data.selectedBranch = [page.props.session_branch.id];
        }
    });

    const updateBranch = ()=>{
        form['postForm'](base_url.value+'/current-branch')
        .then(function(response){
            Utilities.showPopMessage("The branch has been set successfully. You can now proceed with your operations.!","Current Branch",'success','3000')
            Inertia.reload();
        })
        .catch(function(error){
            console.log(error);
        });
    }
</script>

<template>
    <AppLayout title="Profile">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                 <FormSection>
                    <template #title>
                        Current Branch
                    </template>

                    <template #description>
                        Update your Current Branch.
                    </template>

                    <template #form>
                        <!-- Profile Photo -->
                        <div  class="col-span-6 sm:col-span-4">
                            <InputLabel for="branch_id" value="Current Branch" />
                            <branch-select :index="-3"  v-model="form.current_branch_id" placeholder="Session Branch"  url="login-branches/filtered" :initials="data.initialBranch" :selected="data.selectedBranch" @updateBranch="updateBranch"></branch-select>
                        </div>
                    </template>


                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
