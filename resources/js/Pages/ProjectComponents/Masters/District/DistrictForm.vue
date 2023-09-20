
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import CitySelect from '@/Pages/ProjectComponents/SelectComponents/CitySelect.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import StateSelect from '@/Pages/ProjectComponents/SelectComponents/StateSelect.vue';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','states']);
    const form = reactive( new Form({
            form_id: 0,
            name:'',
            district_code:'',
            state_id:0
    }));

    const data = reactive({ create_url:'districts',stateInitials:[],stateSelected:[],showState:true });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  District':'Add District');
    const state_options = computed(() => {
        // console.log(props.states);
        let array = props.states;
        array.forEach(arr => {
            arr.text = arr.state_name;
        });
        return array;
    });

    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if(props.form_id > 0){
            getDistrict();
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
    const getDistrict = () =>{
        axios.get(base_url.value+'/districts/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let district = response.data.district;
                form.name = district.name;
                if(district.state){
                    data.stateInitials = [{'text':district.state.name,'id':district.state.id}];
                    data.stateSelected = [district.state.id];
                }
                refreshComponent(data,'showState');
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
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>

              <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="state_id" value="States" />
                    <state-select v-model="form.state_id"></state-select>
                    <InputError class="mt-2" :message="form.errors.get('state_id')" />
                </div>
            </div> -->

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/4 md:flex-0">
                 <div class="mb-4" v-if="data.showState">
                    <InputLabel for="state_id" value="State" />
                    <state-select  v-model="form.state_id" :key="-1" :initials="data.stateInitials" :selected="data.stateSelected" ></state-select>
                    <InputError class="mt-2" :message="form.errors.get('state_id')" />
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
