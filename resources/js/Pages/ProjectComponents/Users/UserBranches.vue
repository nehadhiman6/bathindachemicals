<script setup>
    import {   ref,  computed,  onMounted,    reactive} from 'vue';
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import globalMixin from '../../../globalMixin';
    import AppLayout from '@/Layouts/AppLayout.vue';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['user_id','type']);
    const form = reactive( new Form({
            user_id: props.user_id,
            branches:[]
    }));
    const data = reactive({ create_url:'user-branches',show:false,user_branches:[],branchInitials:[],
    branchSelected:[],showSelect:true});
    const pageTitle = computed(() => data.user_branches.length > 0 ? 'Update '+props.type+' User branches':'Add '+props.type+' User Branches');

    const emit = defineEmits(['resetForm']);
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    onMounted(() => {
        if(props.type == 'report'){
            data.create_url = 'report-user-branches';
        }
        data.show = true;
        getUserBranches();
    });


    const updateBranch= (ids,index,value)=>{
        form.branches = ids;
    }

    const setBranchSelect= ()=>{
        data.branchInitials = [];
        data.branchSelected = [];
        data.user_branches.forEach(user_comp => {
            data.branchInitials.push({'id':user_comp.branch_id,'text':user_comp.branch.name});
            data.branchSelected.push(user_comp.branch_id);
        });
        refreshComponent(data,'showSelect');
    }


    const getUserBranches= ()=>{
        axios.get(base_url.value+'/'+data.create_url+'/'+props.user_id)
        .then(function(response){
            console.log(response);
            if(response.data.user_branches){
                data.user_branches = response.data.user_branches;
                setBranchSelect();
            }
        })
        .catch(function(error){
            console.log(error);
        })
    }
</script>


<template>
<div>
    <Modal  max-width="5xl" :show="data.show" @close="submitForm()">
       <div class="px-6 py-4">
          <div class="text-lg font-medium text-gray-900" v-text="pageTitle">
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0">
                        <div class="mb-2" v-if="data.showSelect">
                            <InputLabel for="name" value="Branches" />
                            <branch-select  url="branches/filtered" :User_id="props.user_id" :initials="data.branchInitials" :selected="data.branchSelected"  :multiple="true" @updateBranch="updateBranch"></branch-select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <div class="mt-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
            </div>
        </div>

    </Modal>

</div>
</template>

<style>

</style>
