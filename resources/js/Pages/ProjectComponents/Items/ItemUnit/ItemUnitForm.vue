
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import UqcSelect from '@/Pages/ProjectComponents/SelectComponents/UqcSelect.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({
            form_id: 0,
            unit_name:'',
            code:'',
            uqc_id:0
    }));

    const data = reactive({ create_url:'item-units',uqcInitials:[],uqcSelected:[],show:true });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Item Unit':'Add Item Unit');
    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if(props.form_id > 0){
            getItemUnit();
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
    const getItemUnit = () =>{
        axios.get(base_url.value+'/item-units/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let item_unit = response.data.item_unit;
                form.unit_name = item_unit.unit_name;
                form.code = item_unit.code;
                form.uqc_id = item_unit.uqc_id;
                if(item_unit.uqc){
                    data.uqcInitials = [{'text':item_unit.uqc.uqc_name,'id':item_unit.uqc.id}];
                    data.uqcSelected = [item_unit.uqc.id];
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="unit_name" value="Name" />
                    <TextInput v-model="form.unit_name" type="text" required autofocus :error="form.errors.get('unit_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('unit_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="code" value="Unit Code" />
                    <TextInput v-model="form.code" type="text" required  :error="form.errors.get('code') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('code')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/4 md:flex-0">
                 <div class="mb-4" v-if="data.show">
                    <InputLabel for="uqc_id" value="UQC" />
                    <uqc-select  v-model="form.uqc_id" :key="-1" :initials="data.uqcInitials" :selected="data.uqcSelected" ></uqc-select>
                    <InputError class="mt-2" :message="form.errors.get('uqc_id')" />
                </div>
             </div>
            <div class="w-full max-w-full px-3 shrink-0  md:w-1/3 lg:w-1/4 md:flex-0">
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
