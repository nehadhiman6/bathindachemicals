
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,copyProperties} = globalMixin();
    const props = defineProps(['form_id','years']);
    const form = reactive( new Form({ form_id: 0,company_name:"",print_name:"",office_address:"",website:"",gst_number:"",pan_number:"",cin_number:"",remarks:"",year:""}));
    const data = reactive({ create_url:'companies' });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Company':'Add Company');
    const emit = defineEmits(['resetForm']);
    const year_options =   computed(() => {
        let array = (props.years);
        array.forEach(arr => {
            let newStr = arr.year.substring(0, 4).concat(' - ').concat( arr.year.substring(4));
            arr.id = arr.year;
            arr.text = newStr;
        });
        return array;
    });


    onMounted(() => {
        if(props.form_id > 0){
            getCompany();
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
    const getCompany = () =>{
        axios.get(base_url.value+'/companies/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let company = response.data.company;
                // form.name = company.name;
                copyProperties(company,form);
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="company_name" value="Company Name" />
                    <TextInput v-model="form.company_name" type="text" required autofocus :error="form.errors.get('company_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('company_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="print_name" value="Print Name" />
                    <TextInput v-model="form.print_name" type="text" required autofocus :error="form.errors.get('print_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('print_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="office_address" value="H.O/Regd. Office Address" />
                    <TextInput v-model="form.office_address" type="text" required autofocus :error="form.errors.get('office_address') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('office_address')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="website" value="Website" />
                    <TextInput v-model="form.website" type="text" required autofocus :error="form.errors.get('website') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('website')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="gst_number" value="GST Number" />
                    <TextInput v-model="form.gst_number" type="text" required autofocus :error="form.errors.get('gst_number') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('gst_number')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="pan_number" value="PAN Number" />
                    <TextInput v-model="form.pan_number" type="text" required autofocus :error="form.errors.get('pan_number') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('pan_number')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="cin_number" value="CIN Number" />
                    <TextInput v-model="form.cin_number" type="text" required autofocus :error="form.errors.get('cin_number') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('cin_number')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="year" value="Current Session" />
                    <SelectInput v-model="form.year" :options ="year_options"  :error="form.errors.get('year') ? true :false" ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('year')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="remarks" value="Remarks" />
                    <TextInput v-model="form.remarks" type="text" required  :error="form.errors.get('remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                <ButtonComp  @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
