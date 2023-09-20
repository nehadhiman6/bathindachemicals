
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({ form_id: 0,name:"",rate_diff_applicable:'Y',packing_id:0}));
    const data = reactive({ create_url:'party-categories', initialPacking:[],selectedPacking:[],show:true });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Party Category':'Add Party Category');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getPartyCategory();
        }
    });
    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getPartyCategory = () =>{
        axios.get(base_url.value+'/party-categories/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let party_category = response.data.party_category;
                form.name = party_category.name;
                form.rate_diff_applicable = party_category.rate_diff_applicable;
                form.packing_id = party_category.packing_id;
                if(party_category.packing){
                    data.initialPacking = [{'id':party_category.packing.id,'text':party_category.packing.name}];
                    data.selectedPacking = [party_category.packing.id];
                }
                refreshComponent(data,'show');
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                <div class="mb-2">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                <div class="mb-2">
                    <InputLabel for="rate_diff_applicable" value="Rate Diff Applicable" />
                     <SelectInput v-model="form.rate_diff_applicable" :options ="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" autofocus :error="form.errors.get('rate_diff_applicable') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('rate_diff_applicable')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/12 md:flex-0">
                <div class="mb-2" v-if="data.show">
                    <InputLabel for="packing_id" value="Packing" />
                        <packing-select :initials="data.initialPacking" :selected="data.selectedPacking"  placeholder="Packing" v-model="form.packing_id" :error="form.errors.get('packing_id') ? true :false"></packing-select>
                    <InputError class="mt-2" :message="form.errors.get('packing_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
             <div class="mb-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
