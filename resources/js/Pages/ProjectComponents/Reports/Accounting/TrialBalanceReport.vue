h

<script setup>
import { ref, onMounted,computed,reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import FilterWrapper from '@/Pages/CustomComponents/Sections/FilterWrapper.vue';
import AccountingReportNav from '@/Pages/ProjectComponents/Reports/Accounting/AccountingReportsNav.vue';
import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
import AccountGroupSelect from '@/Pages/ProjectComponents/SelectComponents/AccountGroupSelect.vue';
import CompanySelect from '@/Pages/ProjectComponents/SelectComponents/CompanySelect.vue';
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
import InvoiceForm from '@/Pages/ProjectComponents/Sale/Invoice/InvoiceForm.vue';
import globalMixin from '../../../../globalMixin';
import InputError from '@/Components/InputError.vue';


const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['company']);
const state = reactive({
    data: [],
    data1: {},
    accounts: [],
    tData:[],
    columnDefs:[],
    table: null,
    cols_width: [],
    invoiceOpen:false,
    target_id:0,
    branch_name:''
});

 const form = reactive( new Form({
        start_date:BCL.start_date,
        to_date:BCL.fyDate,
        start_date1:BCL.start_date,
        to_date1:BCL.fyDate,
        branch_ids:[],
        amount_lt:'',
        group_wise:'Y',
        comparison:'Y',
        sundry_comb:'Y',
        ac_group_ids:[],
        moved:'N',
        detail:'N',
}));




onMounted(() => {

    // setTable();
});


const updateBranch = (id,index,data) =>{
    form.branch_ids = id;
    if(data){
        state.branch_name += state.branch_name?',':''
        state.branch_name += data.name;
    }
}

const updateAccountGroup = (id,index,data) =>{
    form.ac_group_ids = id;
}

const setTable = () => {
    state.table = $('#ledger_report').DataTable({
                // dom: 'Bfrtip',
                // buttons: [
                //     'copy', 'csv', 'excel', 'pdf', 'print'
                // ],
        dom: 'Bfrtip',
        lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],

        buttons: [
            'pageLength',
            {
                extend: 'excelHtml5',
                header: true,
                footer: true,
                exportOptions: {
                    orthogonal: 'export',
                    // columns: [1, 2, 3,4,5,6]
                },
                filename:function(){
                    return 'Ledger';
                },
                function () {

                    return 'Trail Balance ( From: ' + form.date_from +' To ' + form.date_to+' )';
                },

            },
            'csvHtml5',


        ],

        "processing": true,
        "serverSide": false,
        "searchDelay": 700,
        "ordering": false,
        scrollY: "500px",
        scrollCollapse: false,
        pageLength: 20,
        "autoWidth": true,
        data: [
    ],
        columnDefs: state.columnDefs,
        "sScrollX": true,
    });
    $.each(state.cols_width,function(key,row) {
        $('thead > tr> th:nth-child('+row.col_no+')').css({ 'min-width': row.min_width+'px', 'max-width': row.max_width+'px' });
        // $('thead > tr> th:nth-child(4)').css({ 'min-width': '250px', 'max-width': '250px' });
    })
}

const resetForm = ()=>{
    state.target_id = 0;
}


const getData = () =>{
    form['postForm'](base_url.value +'/trial-balance-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.data;
            state.data1 = response.data1;
            reloadTable();
        }
    })
    .catch(function (error)
    {
    });
}


const reloadTable = () =>{
    if(state.table != null) {
        state.table.destroy();
        $('#ledger_report').empty();
    }
    setColumns();
    setData();
    setTable();
    state.table.clear();
    state.table.rows.add(state.tData).draw();
}
    const setColumns = () => {
        state.columnDefs=[];
        state.cols_width = [];
        var target = 0;
        state.columnDefs = [];
        state.columnDefs.push({title: 'Account Description',targets: target++, data:'name',
            "render": function ( data, type, row, meta ) {
                return Utilities.getBold(row.name,row.bold);
            }
        });
        if(form.detail == 'Y') {
            state.columnDefs.push({ title: "Opening Dr.", targets: target++,className: 'text-right', data:'op_dr'});
            state.columnDefs.push({ title: "Opening Cr", targets: target++, className: 'text-right',data:'op_cr'});
            state.columnDefs.push({title: 'Transactions Dr',targets: target++, className: 'text-right',data:'trans_dr'});
            state.columnDefs.push({ title: "Transactions Cr", targets: target++, className: 'text-right',data:'trans_cr'});
        }
        state.columnDefs.push({title: 'Closing Dr',targets: target++, className: 'text-right',data:'cl_dr'});
        state.columnDefs.push({ title: "Closing Cr", targets: target++, className: 'text-right',data:'cl_cr'});
        if(form.comparison == 'Y') {
            if(form.detail == 'Y') {
                state.columnDefs.push({ title: "Opening Dr.", targets: target++, className: 'text-right',data:'op_dr1'});
                state.columnDefs.push({ title: "Opening Cr", targets: target++, className: 'text-right',data:'op_cr1'});
                state.columnDefs.push({title: 'Transactions Dr',targets: target++, className: 'text-right',data:'trans_dr1'});
                state.columnDefs.push({ title: "Transactions Cr", targets: target++, className: 'text-right',data:'trans_cr1'});
            }
            state.columnDefs.push({title: 'Closing Dr',targets: target++, className: 'text-right',data:'cl_dr1'});
            state.columnDefs.push({ title: "Closing Cr", targets: target++, className: 'text-right',data:'cl_cr1'});
        }
    }

    const setData = () => {
        var record = {};
        state.tData = [];
        var grp_name = '';
        var sub_grp_name = '';
        var grp_change = 'N';
        var row1 = {};
        var amt = 0;
        var process_rec = true;
        var totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
        if(form.group_wise == 'Y') {
            var grp_totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
            var sub_grp_totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
        }
        state.data.forEach(function(row) {
            process_rec = true;
            amt = Utilities.round(row.trans_dr)+Utilities.round(row.trans_cr);
            if(form.comparison == 'Y') {
                amt += Utilities.round(row.trans_dr1)+Utilities.round(row.trans_cr1);
            }
            if(form.moved == 'Y' && amt == 0) {
                process_rec = false;
            }
            amt = Math.abs(Utilities.round(row.bal));
            if(form.comparison == 'Y') {
                row1 = null;
                if(state.data1[row.name]) {
                    row1 = state.data1[row.name][0];
                }
            }
            if(form.comparison == 'Y' && row1 && Math.abs(Utilities.round(row1.bal)) > amt) {
                amt = Math.abs(Utilities.round(row1.bal));
            }
            if(form.amount_lt > 0 && amt < form.amount_lt) {
                process_rec = false;
            }
            console.log(amt);
            console.log(form.amount_lt);
            if(process_rec) {
                if(form.group_wise == 'Y') {
                    if(grp_name != row.group_name) {
                        grp_change = 'N';
                        if(grp_name != '') {
                            grp_change = 'Y';
                            record = getBlankRec('Y');
                            record['name'] = 'Sub Group Total';
                            for(let field in record) {
                                if(field != 'name' && field != 'bold') {
                                    record[field] = Utilities.round(sub_grp_totals[field+'_tot']);
                                }
                            }
                            state.tData.push(record);
                            record = getBlankRec('Y');
                            record['name'] = 'Group Total';
                            for(let field in record) {
                                if(field != 'name' && field != 'bold') {
                                    record[field] = Utilities.round(grp_totals[field+'_tot']);
                                }
                            }
                            state.tData.push(record);
                            grp_totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
                            sub_grp_totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
                        }
                        grp_name = row.group_name;
                        record = getBlankRec('Y');
                        record['name'] = grp_name;
                        state.tData.push(record);
                        if(grp_change == 'Y' || sub_grp_name == '') {
                            sub_grp_name = row.sub_group_name;
                            record = getBlankRec('Y');
                            record['name'] = sub_grp_name;
                            state.tData.push(record);
                            sub_grp_totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
                        }
                    }
                    if(sub_grp_name != row.sub_group_name) {
                        record = getBlankRec('Y');
                        record['name'] = 'Sub Group Total';
                        for(let field in record) {
                            if(field != 'name' && field != 'bold') {
                                record[field] = Utilities.round(sub_grp_totals[field+'_tot']);
                            }
                        }
                        state.tData.push(record);
                        sub_grp_name = row.sub_group_name;
                        record = getBlankRec('Y');
                        record['name'] = sub_grp_name;
                        state.tData.push(record);
                        sub_grp_totals = {op_dr_tot: 0,op_cr_tot: 0,trans_dr_tot: 0,trans_cr_tot: 0,cl_dr_tot: 0,cl_cr_tot: 0,op_dr1_tot: 0,op_cr1_tot: 0,trans_dr1_tot: 0,trans_cr1_tot: 0,cl_dr1_tot: 0,cl_cr1_tot: 0};
                    }
                }
                record = getBlankRec();
                record['name'] = row.name;
                record['op_dr'] = Utilities.round(row['opening']) > 0 ? row.opening:0;
                record['op_cr'] = Utilities.round(row['opening']) < 0 ? Math.abs(row.opening):0;
                record['trans_dr'] = Utilities.round(row['dr']);
                record['trans_cr'] = Utilities.round(row['cr']);
                record['cl_dr'] = Utilities.round(row['bal']) > 0 ? row.bal:0;
                record['cl_cr'] = Utilities.round(row['bal']) < 0 ? Math.abs(row.bal):0;
                if(form.comparison == 'Y') {
                    if(state.data1[row.name]) {
                        row1 = state.data1[row.name][0];
                        record['op_dr1'] = Utilities.round(row1['opening']) > 0 ? row1.opening:0;
                        record['op_cr1'] = Utilities.round(row1['opening']) < 0 ? Math.abs(row1.opening):0;
                        record['trans_dr1'] = Utilities.round(row1['dr']);
                        record['trans_cr1'] = Utilities.round(row1['cr']);
                        record['cl_dr1'] = Utilities.round(row1['bal']) > 0 ? row1.bal:0;
                        record['cl_cr1'] = Utilities.round(row1['bal']) < 0 ? Math.abs(row1.bal):0;
                    }
                }
                for(let field in record) {
                    if(field != 'name' && field != 'bold') {
                        totals[field+'_tot'] += Utilities.round(record[field]);
                        if(form.group_wise == 'Y') {
                            grp_totals[field+'_tot'] += Utilities.round(record[field]);
                            sub_grp_totals[field+'_tot'] += Utilities.round(record[field]);
                        }
                    }
                }
                state.tData.push(record);
            }
        });
        if(form.group_wise == 'Y') {
            if(grp_name != '') {
                grp_change = 'Y';
                record = getBlankRec('Y');
                record['name'] = 'Sub Group Total';
                for(let field in record) {
                    if(field != 'name' && field != 'bold') {
                        record[field] = Utilities.round(sub_grp_totals[field+'_tot']);
                    }
                }
                state.tData.push(record);
                record = getBlankRec('Y');
                record['name'] = 'Group Total';
                for(let field in record) {
                    if(field != 'name' && field != 'bold') {
                        record[field] = Utilities.round(grp_totals[field+'_tot']);
                    }
                }
                state.tData.push(record);
            }
        }
    }

    const  getBlankRec= (bold = 'N')=> {
        return {
            name: '',
            op_dr: '',
            op_cr: '',
            trans_dr: "",
            trans_cr: "",
            cl_dr: "",
            cl_cr: '',
            op_dr1: '',
            op_cr1: '',
            trans_dr1: "",
            trans_cr1: "",
            cl_dr1: "",
            cl_cr1: '',
            bold: bold
        };
    }

    const pageTitle = computed(() => 'Trial Balance Report ( '+props.company.company_name+' )( ' + state.branch_name+' ) From: ' + form.start_date +' To ' + form.to_date);




</script>

<template>
    <AppLayout title="Trial Balance Report">

        <AccountingReportNav>
        </AccountingReportNav>
            <invoice-form v-if="state.invoiceOpen" @resetForm="resetForm" :form_id="state.target_id"
            >
            </invoice-form>
        <div>
            <FilterWrapper title="Filter" open-close="open">
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                        <div class="mb-0">
                            <InputLabel for="start_date" value="From Date" />
                            <date-picker v-model="form.start_date" class-name="start_date" :error="form.errors.get('start_date') ? true :false"></date-picker>
                            <InputError class="mt-2" :message="form.errors.get('start_date')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                        <div class="mb-0">
                            <InputLabel for="to_date" value="To Date" />
                            <date-picker v-model="form.to_date" class-name="to_date" :min-date="form.start_date"  :error="form.errors.get('to_date') ? true :false"></date-picker>
                            <InputError class="mt-2" :message="form.errors.get('to_date')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2">
                        <div class="mb-0">
                            <InputLabel for="branch_ids" value="Branches" />
                            <branch-select :multiple="true" :index="'branch_ids'" url="report-branches/filtered" @updateBranch="updateBranch" :error="form.errors.get('branch_ids') ? true :false"></branch-select>
                            <InputError class="mt-2" :message="form.errors.get('branch_ids')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2">
                        <div class="mb-0">
                            <InputLabel for="ac_group_ids" value="Account Groups"/>
                            <account-group-select :index="'ac_group_ids'" :multiple="true" @updateAccountGroup="updateAccountGroup" ></account-group-select>
                            <InputError class="mt-2" :message="form.errors.get('ac_group_ids')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/4 " >
                        <div class="mb-0">
                            <InputLabel for="sundry_comb" value="Sundry Combined"/>
                            <SelectInput v-model="form.sundry_comb" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('sundry_comb')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/4" >
                        <div class="mb-0">
                            <InputLabel for="group_wise" value="Group/Sub Group Wise"/>
                            <SelectInput v-model="form.group_wise" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('group_wise')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-1/4 " >
                        <div class="mb-0">
                            <InputLabel for="comparison" value="Comparison"/>
                            <SelectInput v-model="form.comparison" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('comparison')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/4 lg:w-1/4 " >
                        <div class="mb-0">
                            <InputLabel for="moved" value="Moved"/>
                            <SelectInput v-model="form.moved" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'},{'id':'A','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('moved')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-1/4 ">
                        <div class="mb-0">
                            <InputLabel for="amount_lt" value="Amount >" />
                            <TextInput v-model="form.amount_lt" type="text"  />
                            <InputError class="mt-2" :message="form.errors.get('amount_lt')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/4 lg:w-1/4 " >
                        <div class="mb-0">
                            <InputLabel for="detail" value="Detailed"/>
                            <SelectInput v-model="form.detail" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('detail')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4">
                        <div class="mb-0" v-if="form.comparison=='Y'">
                            <InputLabel for="start_date1" value="From Date(Comparison)" />
                            <date-picker v-model="form.start_date1" class-name="start_date" :error="form.errors.get('start_date1') ? true :false"></date-picker>
                            <InputError class="mt-2" :message="form.errors.get('start_date1')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4">
                        <div class="mb-0" v-if="form.comparison=='Y'">
                            <InputLabel for="to_date1" value="To Date(Comparison)" />
                            <date-picker v-model="form.to_date1" class-name="to_date" :min-date="form.start_date1"  :error="form.errors.get('to_date1') ? true :false"></date-picker>
                            <InputError class="mt-2" :message="form.errors.get('to_date1')" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <div class="mt-0">
                        <ButtonComp @buttonClicked="getData" type="save">SHOW</ButtonComp>
                    </div>
                </div>

            </FilterWrapper>
            <ListWrapper :title="pageTitle">
                <template #table>
                    <table id="ledger_report" class="row-border stripe iw-table-compact" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

<style>
#ledger_report_wrapper table.dataTable thead th:nth-child(4), #ledger_report_wrapper table.dataTable thead th:nth-child(5),#ledger_report_wrapper table.dataTable thead th:nth-child(6) {
    text-align: right;
}

.text-right{
    text-align: right !important;;
}
</style>
