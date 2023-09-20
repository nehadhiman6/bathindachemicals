
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import DistrictSelect from '@/Pages/ProjectComponents/SelectComponents/DistrictSelect.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','districts']);
    const form = reactive( new Form({
            form_id: 0,
            name:'',
            district_id:'',
            country_id:0,


    }));
    const data = reactive({ create_url:'cities',districtInitials:[],districtSelected:[],'show':true});
    const pageTitle = computed(() => props.form_id > 0 ? 'Update City':'Add City');
    const district_options = computed(() => {
        let array = (props.districts);
        array.forEach(arr => {
            arr.text = arr.name;
        });
        return array;
    });

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        form.form_id = props.form_id;
        if(props.form_id > 0){
            getCity();
        }
    });
    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!");
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getCity = () =>{
        axios.get(base_url.value+'/cities/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let city = response.data.city;
                form.name = city.name;
                // form.district_id = city.district_id;
                if(city.district){
                    data.districtInitials = [{'text': city.district.name,'id':city.district.id}];
                    data.districtSelected = [city.district.id];
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="district_id" value="District" />
                    <district-select v-model="form.district_id" :initials="data.districtInitials" :selected="data.districtSelected"></district-select>
                    <!-- <SelectInput v-model="form.district_id" :options ="district_options" autofocus :error="form.errors.get('district_id') ? true :false" ></SelectInput> -->
                    <InputError class="mt-2" :message="form.errors.get('district_id')" />
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
