<script setup>
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import { onMounted,onUnmounted ,reactive,computed  } from 'vue';
    import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import globalMixin from '../../../../globalMixin';

const { base_url,copyProperties,refreshComponent} = globalMixin();
    const emit = defineEmits(['resetForm'])
    const data = reactive({branchShow:true,create_url:'branches-skip',branchInitials:[],branchSelected:[]});

    const props = defineProps([
        'show','form_id'
    ]);
    const form = reactive( new Form({
            form_id: 0,
            skip_branches:[],
            random:Utilities.getRandomNumber()
    }));
    onMounted(()=>{
        props.show = true;
        form.form_id = props.form_id
        getBranch();
    });
    onUnmounted(() => {
        props.show = false;

    });

    const submitForm = ()=>{
        axios.get(base_url.value+'/'+data.create_url,{
            params: form.data()
        })
        .then(function(response){
            if(response.data.success){
                let branch = response.data.branch;
                emit('resetForm');
                props.show = false;
            }
        })
        .catch(function(error){
            console.log(error);
        });
    }

    const getBranch = ()=>{
        axios.get(base_url.value+'/get-branches/'+props.form_id)
        .then(function(response){
            if(response.data.success){
                // console.log(response.data.data);
                let branches = response.data.data;
                if(branches){
                    for (const key in branches) {
                        form.skip_branches.push(key);
                        data.branchInitials.push({'id':key,'text':branches[key]})
                        data.branchSelected.push(key);
                    }
                }

                refreshComponent(data,'branchShow');
            }
        })
        .catch(function(error){
            console.log(error);
        });
    }

    const updateBranch = (value) =>{
        form.skip_branches = value;
    }

</script>
<template>
    <Modal :show="props.show" max-width="5xl" @close="props.show == false">
       <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                Skip Header Line One
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0">
                        <div class="mb-2">
                            <InputLabel for="name" value="Skip Branches Address 1" />
                            <branch-select v-if="data.branchShow" v-model="form.skip_add_branches"
                                :index="'branch_'+form.random"
                                :multiple="true"
                                :initials="data.branchInitials"
                                :selected="data.branchSelected"
                                @updateBranch="updateBranch"
                                >
                            </branch-select>
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
</template>
