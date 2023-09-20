
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import ItemGroupSelect from '@/Pages/ProjectComponents/SelectComponents/ItemGroupSelect.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent } = globalMixin();
    const props = defineProps(['form_id','title','create_url','group_type']);
    const form = reactive( new Form({ form_id: 0,name:"" ,type:"",m_group_id:0,s_group_id:0,oil:'N'}));
    const pageTitle = computed(() => props.form_id > 0 ? 'Update ' + props.title:'Add ' + props.title);
    const emit = defineEmits(['resetForm']);
    const data = reactive({show:true,initialMainGroup:[],selectedMainGroup:[],initialSubGroup:[],selectedSubGroup:[]});

    onMounted(() => {
        form.type = props.group_type;
        if(props.form_id > 0){
            getTypeMaster();
        }
    });
    const submitForm = () =>{
        form['postForm'](props.create_url.replace(/_/g, "-"))
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
    const getTypeMaster = () =>{
        axios.get(base_url.value+'/'+props.create_url.replace(/_/g, "-")+'/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let item_group = response.data.item_group;
                // form.name = item_group.name;
                Utilities.copyProperties(item_group,form);
                if(item_group.main_group){
                    data.initialMainGroup = [{'text':item_group.main_group.name,'id':item_group.main_group.id}];
                    data.selectedMainGroup = [item_group.main_group.id];
                    form.m_group_id = item_group.main_group.id;
                }
                if(item_group.sub_group){
                    data.initialSubGroup = [{'text':item_group.sub_group.name,'id':item_group.sub_group.id}];
                    data.selectedSubGroup = [item_group.sub_group.id];
                    form.s_group_id = item_group.sub_group.id;
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
            <div class="w-full max-w-full px-3 md:w-6/12 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput v-model="form.name" type="text" required autofocus :error="form.errors.get('name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-6/12 md:flex-0" v-if="form.type != 'M'">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="m_group_id" value="Main Group" />
                    <item-group-select :index="'m_group_id'"
                        :initials="data.initialMainGroup"
                        :selected="data.selectedMainGroup"
                        v-model="form.m_group_id"  :error="form.errors.get('m_group_id') ? true :false">
                    </item-group-select>
                    <InputError class="mt-2" :message="form.errors.get('m_group_id')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-6/12 md:flex-0" v-if="form.type == 'S2'">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="s_group_id" value="Sub Group" />
                    <item-group-select
                        :initials="data.initialSubGroup"
                        :selected="data.selectedSubGroup"
                        :index="'s_group_id'" v-model="form.s_group_id" type="S1" :error="form.errors.get('s_group_id') ? true :false"></item-group-select>
                    <InputError class="mt-2" :message="form.errors.get('s_group_id')" />
                </div>
            </div>
                <div class="w-full max-w-full px-3 md:w-6/12 md:flex-0" v-if="form.type == 'S1'" >
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="oil" value="Oil" />
                    <SelectInput  v-model="form.oil" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('oil') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('oil')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 lg:w-1/4 md:w-1/3 md:flex-0">
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
