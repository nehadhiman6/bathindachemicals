
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import PartyCategorySelect from '@/Pages/ProjectComponents/SelectComponents/PartyCategorySelect.vue';
    import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';
    import InputError from '@/Components/InputError.vue';

    import {    ref,  computed,  onMounted, onBeforeMount,   reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent} = globalMixin();
    const props = defineProps(['title',
        'typeable_id',
        'tyeable_name',
        'type',
    ]);

    const form = reactive( new Form({
        typeable_id:'',
        type:'',
        typeable_data:[]
     }));

    onBeforeMount(() => {
        getData();
        form.typeable_id = props.typeable_id;
        form.type = props.type;

    });
    const data = reactive({ create_url:'categories-brands',initials :[],selected:[] ,show:true});
    const pageTitle = computed(() => 'Update ' +props.title);
    const emit = defineEmits(['resetForm']);

    const updateBrandsCategories = (ids) =>{
        console.log(ids);
        form.typeable_data = ids;
    }
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
            console.log(error);
        });
    }

    const getData=()=>{
        axios.get(base_url.value+'/categories-brands/'+props.typeable_id+'/edit?type='+props.type)
        .then(function(response){
            if(response.data.success){
                let typeable = response.data.typeable;
                let type = props.type == 'category' ?'category_brands':'brand_categories';
                let nested_type = props.type == 'category' ?'brand':'category';
                typeable[type].forEach(element => {
                    data.initials.push({'id':element[nested_type]['id'],'text':element[nested_type]['name']})
                    data.selected.push(element[nested_type]['id']);
                    form.typeable_data.push(element[nested_type]['id']);
                });
                refreshComponent(data,'show');
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
            console.log(error);
        });
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0" v-if="props.type =='brand'">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="name" value="Categories" />
                    <party-category-select  :index="-4" :multiple="true" :initials="data.initials" :selected='data.selected'  @updatePartyCategory="updateBrandsCategories"> </party-category-select>
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/1 md:flex-0" v-if="props.type =='category'">
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="name" value="Brands" />
                    <brand-select :index="-4" :multiple="true"  :initials="data.initials" :selected='data.selected'  @updateBrands="updateBrandsCategories"></brand-select>
                    <InputError class="mt-2" :message="form.errors.get('name')" />
                </div>
            </div>

        </div>
         <div class="w-full max-w-full px-3 shrink-0 lg:w-1/4 md:w-1/3 md:flex-0">
             <div class="mb-4">
                <ButtonComp @buttonClicked="submitForm" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
