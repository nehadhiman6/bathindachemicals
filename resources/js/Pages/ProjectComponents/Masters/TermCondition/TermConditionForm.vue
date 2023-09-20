
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({ form_id: 0,sno:"",type:"",terms:"" }));
    const data = reactive({ create_url:'term-conditions' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Term Condition':'Add Term Condition');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getTermCondition();
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
    const getTermCondition = () =>{
        axios.get(base_url.value+'/term-conditions/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let term_condition = response.data.term_condition;
                Utilities.copyProperties(term_condition,form);
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

              <div class="w-full max-w-full px-3 md:w-3/12">
                <div class="mb-4">
                    <InputLabel for="type" value="Type" />
                    <SelectInput   v-model="form.type" :options="[
                        {'id':'gst_invoice' ,'text':'GST Invoice'},
                        {'id':'vat_invoice' ,'text':'VAT Invoice'},
                        {'id':'retail_invoice' ,'text':'Retail Invoice'},
                        {'id':'high_seas_invoice' ,'text':'High Seas Invoice'},
                        {'id':'sale_against_bond' ,'text':'Sale Against Bond'},
                    ]" :error="form.errors.get('type') ? true :false" @change="calc" > </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/12">
                <div class="mb-4">
                    <InputLabel for="sno" value="S. no." />
                    <TextInput v-model="form.sno" autofocus :error="form.errors.get('sno') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sno')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-12/12">
                <div class="mb-4">
                    <InputLabel for="terms" value="Terms" />
                    <TextAreaInput v-model="form.terms" autofocus :error="form.errors.get('terms') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('terms')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/3">
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
