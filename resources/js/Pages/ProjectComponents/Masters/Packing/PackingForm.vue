
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url} = globalMixin();
    const props = defineProps(['form_id','packing_type']);
    const form = reactive( new Form({ form_id: 0,name:"" ,rate_diff_applicable:'N',order_no:'',type:'O'}));
    const data = reactive({ create_url:'oil-packings' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Packing':'Add Packing');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getPacking();
        }
        if(props.packing_type == 'loose_packings'){
            form.type = 'L';
        }
        else if(props.packing_type == 'liquor_packings'){
            form.type = 'Q';
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
    const getPacking = () =>{
        axios.get(base_url.value+'/oil-packings/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let packing = response.data.packing;
                form.name = packing.name;
                form.rate_diff_applicable = packing.rate_diff_applicable;
                form.order_no = packing.order_no;
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
            <div class="w-full max-w-full px-3  md:w-6/12">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-3/12" v-if="props.packing_type == 'oil_packings'">
                <div class="mb-4">
                    <InputLabel for="rate_diff_applicable" value="Rate Difference Applicable" />
                    <SelectInput  v-model="form.rate_diff_applicable" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('rate_diff_applicable') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('rate_diff_applicable')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-3/12" v-if="props.packing_type == 'oil_packings'">
                <div class="mb-4">
                    <InputLabel for="order_no" value="Order" />
                    <TextInput v-model="form.order_no" type="text" required  :error="form.errors.get('order_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('order_no')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  lg:w-3/12 md:w-3/12">
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
