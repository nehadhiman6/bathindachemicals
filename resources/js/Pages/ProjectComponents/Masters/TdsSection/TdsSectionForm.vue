
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent,copyProperties} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({
            form_id: 0,
            section:'',
            rate1:'',
            ac_id1:'',
            rate2:'',
            ac_id2:'',
            rate3:'',
            ac_id3:'',
            non_pan_rate:'',
            ac_id4:'',
            higher_rate:'',
            ac_id5:'',
    }));

    const data = reactive({ create_url:'tds-section',
        ac1Initials:[],
        ac1Selected:[],
        ac2Initials:[],
        ac2Selected:[],
        ac3Initials:[],
        ac3Selected:[],
        ac4Initials:[],
        ac4Selected:[],
        ac5Initials:[],
        ac5Selected:[],
        show:true
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update TDS Section':'Add TDS Section');
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
        axios.get(base_url.value+'/tds-section/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let tds_section = response.data.tds_section;
                form.section = tds_section.section;
                form.form_id = tds_section.id;
                form.rate1 = tds_section.rate1;
                form.rate2 = tds_section.rate2;
                form.rate3 = tds_section.rate3;
                form.non_pan_rate = tds_section.non_pan_rate;
                form.higher_rate = tds_section.higher_rate;
                form.ac_id1 = tds_section.ac_id1;
                form.ac_id2 = tds_section.ac_id2;
                form.ac_id3 = tds_section.ac_id3;
                form.ac_id4 = tds_section.ac_id4;
                form.ac_id5 = tds_section.ac_id5;
                if(tds_section.account1){
                    data.ac1Initials = [{'id':tds_section.account1.id,'text':tds_section.account1.name}];
                    data.ac1Selected = [tds_section.account1.id];
                }
                if(tds_section.account2){
                    data.ac2Initials = [{'id':tds_section.account2.id,'text':tds_section.account2.name}];
                    data.ac2Selected = [tds_section.account2.id];
                }
                if(tds_section.account3){
                    data.ac3Initials = [{'id':tds_section.account3.id,'text':tds_section.account3.name}];
                    data.ac3Selected = [tds_section.account3.id];
                }
                if(tds_section.account4){
                    data.ac4Initials = [{'id':tds_section.account4.id,'text':tds_section.account4.name}];
                    data.ac4Selected = [tds_section.account4.id];
                }
                if(tds_section.account5){
                    data.ac5Initials = [{'id':tds_section.account5.id,'text':tds_section.account5.name}];
                    data.ac5Selected = [tds_section.account5.id];
                }


                refreshComponent(data,'show');
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
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="section" value="Section" class="required" />
                    <TextInput v-model="form.section" type="text" required autofocus :error="form.errors.get('section') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('section')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="rate1" value="TDS Rate1"  class="required"/>
                    <TextInput v-model="form.rate1" type="text" required  :error="form.errors.get('rate1') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('rate1')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="ac_id1" value="GL Account1"  class="required"/>
                    <account-select
                        v-model="form.ac_id1"
                        index="1"
                        :error="form.errors.get('ac_id1') ? true :false"
                        :type="'gl'"
                        :initials="data.ac1Initials"
                        :selected="data.ac1Selected">
                    </account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id1')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="rate2" value="TDS Rate2" />
                    <TextInput v-model="form.rate2" type="text" :error="form.errors.get('rate2') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('rate2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="ac_id2" value="GL Account2"/>
                    <account-select
                        v-model="form.ac_id2"
                        index="2"
                        :error="form.errors.get('ac_id2') ? true :false"
                        :type="'gl'"
                        :initials="data.ac2Initials"
                        :selected="data.ac2Selected">
                    </account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id2')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="rate3" value="TDS Rate3" />
                    <TextInput v-model="form.rate3" type="text" :error="form.errors.get('rate3') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('rate3')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="ac_id3" value="GL Account3"/>
                    <account-select
                        v-model="form.ac_id3"
                        index="3"
                        :error="form.errors.get('ac_id3') ? true :false"
                        :type="'gl'"
                        :initials="data.ac3Initials"
                        :selected="data.ac3Selected">
                    </account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id3')" />
                </div>
            </div>


            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="non_pan_rate" value="Higher Rate None Pan Cases" />
                    <TextInput v-model="form.non_pan_rate" type="text" :error="form.errors.get('non_pan_rate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('non_pan_rate')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="ac_id4" value="GL Account4"/>
                    <account-select
                        v-model="form.ac_id4"
                        index="4"
                        :error="form.errors.get('ac_id4') ? true :false"
                        :type="'gl'"
                        :initials="data.ac4Initials"
                        :selected="data.ac4Selected">
                    </account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id4')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="higher_rate" value="Higher Rate" />
                    <TextInput v-model="form.higher_rate" type="text" :error="form.errors.get('higher_rate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('higher_rate')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="ac_id5" value="GL Account5"/>
                    <account-select
                        v-model="form.ac_id5"
                        index="5"
                        :error="form.errors.get('ac_id5') ? true :false"
                        :type="'gl'"
                        :initials="data.ac5Initials"
                        :selected="data.ac5Selected">
                    </account-select>
                    <InputError class="mt-2" :message="form.errors.get('ac_id5')" />
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
