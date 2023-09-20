
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import BankSelect from '@/Pages/ProjectComponents/SelectComponents/BankSelect.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent,copyProperties} = globalMixin();
    const props = defineProps(['form_id','banks']);
    const form = reactive( new Form({
            form_id: 0,
            ifsc_code:'',
            branch:'',
            micr_code:'',
            bsr_code:'',
            bank_id:0
    }));

    const data = reactive({ create_url:'ifscs',bankInitials:[],bankSelected:[] ,show:true});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Ifsc':'Add Ifsc');
    const bank_options = computed(() => {
        // console.log(props.banks);
        let array = props.banks;
        array.forEach(arr => {
            arr.text = arr.bank_name;
        });
        return array;
    });

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if(props.form_id > 0){
            getIfsc();
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
    const getIfsc = () =>{
        axios.get(base_url.value+'/ifscs/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let ifsc = response.data.ifsc;
                form.name = ifsc.name;
                form.ifsc_code = ifsc.ifsc_code;
                form.ifsc_id = ifsc.ifsc_id;
                if(ifsc.bank){
                    data.bankInitials = [{'id':ifsc.bank.id,'text':ifsc.bank.name}];
                    data.bankSelected = [ifsc.bank.id];
                    refreshComponent(data,'show');
                }
                copyProperties(ifsc,form);
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="ifsc_code" value="IFSC Code" />
                    <TextInput v-model="form.ifsc_code" type="text" required autofocus :error="form.errors.get('ifsc_code') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('ifsc_code')" />
                </div>
            </div>

              <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 md:flex-0">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="bank_id" value="Banks" />
                    <bank-select v-model="form.bank_id"  :error="form.errors.get('bank_id') ? true :false" :initials="data.bankInitials" :selected="data.bankSelected"></bank-select>
                    <InputError class="mt-2" :message="form.errors.get('bank_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="branch" value="Branch" />
                    <TextInput v-model="form.branch" type="text" required  :error="form.errors.get('branch') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('branch')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="micr_code" value="MICR Code" />
                    <TextInput v-model="form.micr_code" type="text" required  :error="form.errors.get('micr_code') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('micr_code')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="bsr_code" value="BSR Code" />
                    <TextInput v-model="form.bsr_code" type="text" required  :error="form.errors.get('bsr_code') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('bsr_code')" />
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
