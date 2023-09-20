

<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import OpeningDetail from '@/Pages/ProjectComponents/Accounts/Account/OpeningDetail.vue';
import globalMixin from '../../../../globalMixin';

const { base_url, copyProperties} = globalMixin();
const data = reactive({ create_url:'opening' });

const props = defineProps([
    'account','branches','openings'
]);
const form = reactive( new Form({
    form_id: 0,
    ac_id:props.account.id,
    openings:[]
}));

onMounted(()=>{
    props.branches.forEach(branch => {
        var detail = getDetail();
        detail.branch_id = branch.id;
        detail.branch_name = branch.name;
        let obj = props.openings.find(o => o.branch_id == branch.id);
        if(obj){
            copyProperties(obj,detail);
        }
        form.openings.push(detail);
    });
});

const getDetail = () => {
    return {
        id:0,
        branch_id:0,
        ac_id:props.account.id,
        dr_cr:'',
        opening_amount:'',
        branch_name:'',
        bills:[],
        random:Utilities.getRandomNumber()
    }
}

const resetForm=() =>{
    Inertia.get(base_url.value+'/accounts');
}
const submitForm=() =>{
    form['postForm'](base_url.value+'/'+data.create_url)
    .then(function(response){
        console.log(response);
        if(response.success){
            resetForm();
        }
    })
    .catch(function(error){
    });
}

</script>

<template>
    <AppLayout title="Accounts">
        <template #header>
        </template>
        <div>

            <ListWrapper :title="props.account.name +' \'s Openings '">
                <template #table>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" width="100%">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <th width="10%" scopescope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">Branch</th>
                            <th width="15%" scopescope="col" class="px-6 py-3">Debit/Credit</th>
                            <th width="15%" scope="col" class="px-6 py-3">Amount</th>
                            <th v-if="props.account.bill_wise == 'Y'">Bill</th>
                        </thead>
                        <tbody>
                            <opening-detail v-for="(opening,index) in form.openings" :key="opening.random"
                                :opening="opening"
                                :form = "form"
                                :index = "index"
                                :account = "account"
                            >
                            </opening-detail>

                        </tbody>
                    </table>
                        <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
                        <div class="mt-4">
                            <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                            <ButtonComp @buttonClicked="resetForm" type="cancel">Cancel</ButtonComp>
                        </div>
                    </div>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

