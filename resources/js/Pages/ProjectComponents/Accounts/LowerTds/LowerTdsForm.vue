
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import TdsSectionSelect from '@/Pages/ProjectComponents/SelectComponents/TdsSectionSelect.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent,copyProperties} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({
            form_id: 0,
            ac_id:'0',
            tds_sec_id:'',
            certificate_no:'',
            date:'',
            rate:'',
            acid_tds:'',
            from_date:'',
            to_date:'',
            amount:'',
    }));

    const data = reactive({ create_url:'lower-tds-setting',
        acInitials:[],
        acSelected:[],
        sectionInitials:[],
        sectionSelected:[],
        show:true
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Lower TDS Setting':'Add Lower TDS Setting');
    const emit = defineEmits(['resetForm']);
    onMounted(() => {
        if(props.form_id > 0){
            getIfsc();
        }
    });
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
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
        axios.get(base_url.value+'/lower-tds-setting/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let lower_tds_setting = response.data.lower_tds_setting;
                form.ac_id = lower_tds_setting.ac_id;
                form.form_id = lower_tds_setting.id;
                form.tds_sec_id = lower_tds_setting.tds_sec_id;
                form.certificate_no = lower_tds_setting.certificate_no;
                form.date = lower_tds_setting.date;
                form.rate = lower_tds_setting.rate;
                form.acid_tds = lower_tds_setting.acid_tds;
                form.from_date = lower_tds_setting.from_date;
                form.to_date = lower_tds_setting.to_date;
                form.amount = lower_tds_setting.amount;
                if(lower_tds_setting.tds_section){
                    data.sectionInitials = [{'id':lower_tds_setting.tds_section.id,'text':lower_tds_setting.tds_section.section}];
                    data.sectionSelected = [lower_tds_setting.tds_section.id];
                }
                if(lower_tds_setting.tds_account){
                    data.acInitials = [{'id':lower_tds_setting.tds_account.id,'text':lower_tds_setting.tds_account.name}];
                    data.acSelected = [lower_tds_setting.tds_account.id];
                }

                refreshComponent(data,'show');
                copyProperties(lower_tds_setting,form);
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
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="tds_sec_id" value="Section" class="required" />
                    <tds-section-select
                        v-model="form.tds_sec_id"
                        index="1"
                        :error="form.errors.get('tds_sec_id') ? true :false"
                        :initials="data.sectionInitials"
                        :selected="data.sectionSelected">
                    </tds-section-select>
                    <InputError class="mt-2" :message="form.errors.get('tds_sec_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="certificate_no" value="Certificate No"  class="required"/>
                    <TextInput v-model="form.certificate_no" type="text" required  :error="form.errors.get('certificate_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('certificate_no')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="date" value="Date" />
                    <date-picker class-name="date" v-model="form.date" :error="form.errors.get('date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="rate" value="Lower Tds Rate" />
                    <TextInput v-model="form.rate" type="text" :error="form.errors.get('rate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('rate')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4"  v-if="data.show">
                    <InputLabel for="acid_tds" value="Tds Account"  class="required"/>
                    <account-select
                        v-model="form.acid_tds"
                        index="1"
                        :error="form.errors.get('acid_tds') ? true :false"
                        :type="'gl'"
                        :initials="data.acInitials"
                        :selected="data.acSelected">
                    </account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid_tds')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="from_date" value="Valid From Date" />
                    <date-picker class-name="from_date" v-model="form.from_date" :error="form.errors.get('from_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('from_date')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="to_date" value="Valid To Date" />
                    <date-picker class-name="to_date" v-model="form.to_date" :error="form.errors.get('to_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('to_date')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amount" value="Amount" />
                    <TextInput v-model="form.amount" type="text" :error="form.errors.get('amount') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amount')" />
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
