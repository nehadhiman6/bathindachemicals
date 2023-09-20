
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputError from '@/Components/InputError.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';

    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url} = globalMixin();
    const props = defineProps(['form_id']);
    const form = reactive( new Form({
        form_id: 0,
        acid: '',
        width: '',
        height: '',
        date_top: '',
        date_left: '',
        name_top: '',
        name_left: '',
        name_char: '',
        amt_w1_top : '',
        amt_w1_left: '',
        amt_w1_char: '',
        amt_w2_top: '',
        amt_w2_left: '',
        amt_w2_char: '',
        amt_f_left: '',
        amt_f_top:'',
        for_top: '',
        for_left:'',
        for_char:'',
        for_flag:'N',
        sign1_left: '',
        sign1_flag: 'N',
        sign1: '',
        sign1_top:'',
        sign2_top: '',
        sign2_left: '',
        sign2_flag: 'N',
        sign2: '',
        sign3_left: '',
        sign3_flag: 'N',
        sign3_top: '',
        sign3: '',
        print_mode: 'L',
        date_font: '',
        name_font: '',
        amt_w1_font: '',
        amt_w2_font: '',
        amt_f_font: '',
        tds_flag: 'N',
        tds_top: '',
        tds_left: '',
        tds_char: '',
        tds_font: '',
    }));
    const data = reactive({
        create_url:'cheque-setting',
        show: true,
        bankInitials:[],
        bankSelected:[],
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Cheque Setting':'Add Cheque Setting');
    const emit = defineEmits(['resetForm']);

    onMounted(() => {
        if(props.form_id > 0){
            getChequeSetting();
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
    const getChequeSetting = () =>{
        axios.get(base_url.value+'/cheque-setting/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                var cheque = response.data.cheque
                Utilities.copyProperties(cheque,form);
                form.form_id = cheque.id;
                if (cheque.account) {
                    self.bankSelected = [cheque.account.id];
                    self.bankInitials = [{
                        'id': cheque.account.id,
                        'text': cheque.account.ac_name
                    }];
                }
                state.show = true;
                Utilities.refreshComponent(self, 'show');
            }
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
                <div class="mb-4" v-if="data.show">
                    <InputLabel for="acid" value="Bank Name" />
                    <account-select
                        v-model="form.acid" index="1"
                        type="bank"
                        :error="form.errors.get('acid') ? true :false"
                        :initials="data.bankInitials"
                        :selected="data.bankSelected"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('acid')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="width" value="Width of Cheque" />
                    <TextInput v-model="form.width" type="text" required :error="form.errors.get('width') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('width')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="height" value="Height of Cheque" />
                    <TextInput v-model="form.height" type="text" required :error="form.errors.get('height') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('height')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="date_top" value="Date From Top" />
                    <TextInput v-model="form.date_top" type="text" required :error="form.errors.get('date_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('date_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="date_left" value="From Left" />
                    <TextInput v-model="form.date_left" type="text" required :error="form.errors.get('date_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('date_left')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="print_mode" value="Printer Mode" />
                    <SelectInput  v-model="form.print_mode" :options="[
                        {'id':'P','text':'Portrait'},
                        {'id':'L','text':'Landscape'},
                    ]" :error="form.errors.get('print_mode') ? true :false" disabled> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('print_mode')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name_top" value="Name From Top" />
                    <TextInput v-model="form.name_top" type="text" required :error="form.errors.get('name_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name_left" value="From Left" />
                    <TextInput v-model="form.name_left" type="text" required :error="form.errors.get('name_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name_left')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name_char" value="No. of character" />
                    <TextInput v-model="form.name_char" type="text" required :error="form.errors.get('name_char') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name_char')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w1_top" value="Amount Word Line1 Top" />
                    <TextInput v-model="form.amt_w1_top" type="text" required :error="form.errors.get('amt_w1_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w1_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w1_left" value="From Left" />
                    <TextInput v-model="form.amt_w1_left" type="text" required :error="form.errors.get('amt_w1_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w1_left')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w1_char" value="No. of character" />
                    <TextInput v-model="form.amt_w1_char" type="text" required :error="form.errors.get('amt_w1_char') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w1_char')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w2_top" value="Amount Word Line2 Top" />
                    <TextInput v-model="form.amt_w2_top" type="text" required :error="form.errors.get('amt_w2_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w2_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w2_left" value="From Left" />
                    <TextInput v-model="form.amt_w2_left" type="text" required :error="form.errors.get('amt_w2_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w2_left')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w2_char" value="No. of character" />
                    <TextInput v-model="form.amt_w2_char" type="text" required :error="form.errors.get('amt_w2_char') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w2_char')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_f_top" value="Amount Figure Top" />
                    <TextInput v-model="form.amt_f_top" type="text" required :error="form.errors.get('amt_f_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_f_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_f_left" value="From Left" />
                    <TextInput v-model="form.amt_f_left" type="text" required :error="form.errors.get('amt_f_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_f_left')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="for_flag" value="For Company Name Req" />
                    <SelectInput  v-model="form.for_flag" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'N'},
                    ]" :error="form.errors.get('for_flag') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('for_flag')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="for_top" value="For Company Name from Top" />
                    <TextInput v-model="form.for_top" type="text" required :error="form.errors.get('for_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('for_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="for_left" value="From Left" />
                    <TextInput v-model="form.for_left" type="text" required :error="form.errors.get('for_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('for_left')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="for_char" value="No. of character" />
                    <TextInput v-model="form.for_char" type="text" required :error="form.errors.get('for_char') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('for_char')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign1_flag" value="Signature (1) Required" />
                    <SelectInput  v-model="form.sign1_flag" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('sign1_flag') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('sign1_flag')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign1" value="Print In Sign1" />
                    <TextInput v-model="form.sign1" type="text" required :error="form.errors.get('sign1') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign1')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign1_top" value="Signature (1) from Top" />
                    <TextInput v-model="form.sign1_top" type="text" required :error="form.errors.get('sign1_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign1_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign1_left" value="From Left" />
                    <TextInput v-model="form.sign1_left" type="text" required :error="form.errors.get('sign1_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign1_left')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign2_flag" value="Signature (2) Required" />
                    <SelectInput  v-model="form.sign2_flag" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('sign2_flag') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('sign2_flag')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign2" value="Print In Sign2" />
                    <TextInput v-model="form.sign2" type="text" required :error="form.errors.get('sign2') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign2')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign2_top" value="Signature (2) from Top" />
                    <TextInput v-model="form.sign2_top" type="text" required :error="form.errors.get('sign2_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign2_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign2_left" value="From Left" />
                    <TextInput v-model="form.sign2_left" type="text" required :error="form.errors.get('sign2_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign2_left')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign3_flag" value="Signature (3) Required" />
                    <SelectInput  v-model="form.sign3_flag" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('sign3_flag') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('sign3_flag')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign3" value="Print In Sign3" />
                    <TextInput v-model="form.sign3" type="text" required :error="form.errors.get('sign3') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign3')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign3_top" value="Signature (3) from Top" />
                    <TextInput v-model="form.sign3_top" type="text" required :error="form.errors.get('sign3_top') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign3_top')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="sign3_left" value="From Left" />
                    <TextInput v-model="form.sign3_left" type="text" required :error="form.errors.get('sign3_left') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sign3_left')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="date_font" value="Date Font" />
                    <TextInput v-model="form.date_font" type="text" required :error="form.errors.get('date_font') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('date_font')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="name_font" value="Name Font" />
                    <TextInput v-model="form.name_font" type="text" required :error="form.errors.get('name_font') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('name_font')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w1_font" value="Amount Word 1 Font" />
                    <TextInput v-model="form.amt_w1_font" type="text" required :error="form.errors.get('amt_w1_font') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w1_font')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_w2_font" value="Amount Word 2 Font" />
                    <TextInput v-model="form.amt_w2_font" type="text" required :error="form.errors.get('amt_w2_font') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_w2_font')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="amt_f_font" value="Amount Figure Font" />
                    <TextInput v-model="form.amt_f_font" type="text" required :error="form.errors.get('amt_f_font') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('amt_f_font')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/4 md:flex-0">
                <div class="mb-4">
                    <InputLabel for="tds_font" value="TDS Figure Font" />
                    <TextInput v-model="form.tds_font" type="text" required :error="form.errors.get('tds_font') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('tds_font')" />
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-end -mx-3">
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
