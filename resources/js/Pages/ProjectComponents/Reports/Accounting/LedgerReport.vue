h

<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
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


const state = reactive({
    data: {},
    openings: {},
    accounts: [],
    tData:[],
    columnDefs:[],
    table: null,
    cols_width: [],
    invoiceOpen:false,
    target_id:0

});

 const form = reactive( new Form({
        start_date:BCL.start_date,
        to_date:BCL.today,
        branch_ids:[],
        ac_ids:[],
        opening_bal:'Y',
        dr_cr:'A',
        amount_grt:'',
        amount_lt:'',
        weight:'N',
        due_date:'N',
        voucher_no:'Y',
        ac_group_ids:[],
        companies_ids:[],
        moved:'A',
        search_word:'',
        particulars:'',
        zero_bal_ending_date:'A',
        search_term_position: 'G'
}));




onMounted(() => {
    $(document).on('click', '.edit-invoice', function(e) {
        showSaleInvoice(e.target.dataset.invId);
    });
    // setTable();
});


const showSaleInvoice= (invoice_id)=>{
    state.target_id = invoice_id;
    state.invoiceOpen = true;
}
const updateBranch = (id,index,data) =>{
    form.branch_ids = id;
}
const updateCompany = (id,index,data) =>{
    form.companies_ids = id;
}

const updateAccountGroup = (id,index,data) =>{
    form.ac_group_ids = id;
}

const updateAccount = (id,index,data) =>{
    form.ac_ids = id;
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

                    return 'Ledger ( From: ' + self.form.date_from +' To ' + self.form.date_to+' )';
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
    state.invoiceOpen = false;
}


const getData = () =>{
    form['postForm'](base_url.value +'/ledger-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.ledger;
            state.openings = response.openings;
            state.accounts = response.accounts;
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
        state.columnDefs.push({title: 'Date',targets: target++, data:'trans_date'});
        state.cols_width.push({col_no: target,min_width: '75',max_width:'75'});
        if(form.due_date == 'Y') {
            state.columnDefs.push({title: 'Due Date',targets: target++, data:'due_date'});
            state.cols_width.push({col_no: target,min_width: '75',max_width:'75'});
        }
        state.columnDefs.push(
            {
                title: 'Type',targets: target++,  data: 'vtype'
                ,"render": function ( data, type, row, meta ) {
                var str = '';
                // if(row.vcode.substr(0,2) == 'VC'){
                //     str += '<a href="#redirectForm" data-voucher-id='+data+' data-voucher-action="Edit" class="edit-voucher">'+data+'</a>';
                //     var vc_id = data.substring(2);
                //     console.log(type);
                //     if(type != 'export'){
                //         str +='<a target="_blank" href="'+base_url.value+'/voucher-print/'+vc_id+'?value='+row.vtype+'  "class="btn iw-btn iw-btn-print mt-1 mb-1">Print</a>';
                //     }

                // }
                if(row.vcode != 'OS'){
                //     var inv_id = row.vcode.substring(2);
                    // str += '<a href="#redirectForm" data-inv-id='+inv_id+' data-inv-action="Edit"  data-btn-show="No" class="edit-invoice">'+data+'</a>';
                    str+="<br><a href='"+ base_url.value + '/posting-detail/'+row.vcode+"' class='inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700' target = _blank>"+data+"</a>"

                }
                else{
                    str += data;
                }
                return str;
            }
        });
        state.cols_width.push({col_no: target,min_width: '70',max_width:'70'});
        if(form.voucher_no == 'Y') {
            state.columnDefs.push({title: 'Voucher No',targets: target++,data: 'vchr_no'});
            state.cols_width.push({col_no: target,min_width: '100',max_width:'100'});
        }
        state.columnDefs.push({title: 'Particulars', targets: target++,
            "render": function ( data, type, row, meta ) {
                return Utilities.getBold(row.part,row.bold);
            }
        });
        state.cols_width.push({col_no: target,min_width: '350',max_width:'350'});
        if(form.weight == 'Y') {
            state.columnDefs.push({title: 'Weight',targets: target++,data: 'weight'});
            state.cols_width.push({col_no: target,min_width: '70',max_width:'70'});
        }
        state.columnDefs.push({ title: 'Amount(dr.)', targets: target++, className: 'text-right',
            "render": function ( data, type, row, meta ) {
                return Utilities.getBold(row.amount_dr,row.bold);
            }
        });
        state.cols_width.push({col_no: target,min_width: '100',max_width:'100'});
        state.columnDefs.push({ title: "Amount(Cr.)", targets: target++, className: 'text-right',
            "render": function ( data, type, row, meta ) {
                return Utilities.getBold(row.amount_cr,row.bold);
            }
        });
        state.cols_width.push({col_no: target,min_width: '100',max_width:'100'});
        state.columnDefs.push({ title: "Balance", targets: target++, data:'balance',className: 'text-right',});
        state.cols_width.push({col_no: target,min_width: '100',max_width:'100'});
        state.columnDefs.push({ title: "Dr/Cr", targets: target++, data:'bal_dr_cr'});
    }

    const setData = () => {
        var bal = 0;
        var dr_tot = 0;
        var cr_tot = 0;
        var record = {};
        var op = 0;
        var ac_data = '';
        var ac_id = 0;
        var name = '';
        state.tData = [];
        state.accounts.forEach(function(row) {
            ac_id = row.id;
            name = row.name;
            bal = 0;
            ac_data = 'N';
            dr_tot = 0;
            cr_tot = 0;
            op = form.opening_bal == 'Y' && state.openings[ac_id] ? state.openings[ac_id]:0;
            if(op != 0 && (form.moved != 'Y' || state.data[ac_id])) {
                record = getBlankRec('Y');
                Utilities.copyProperties({part: name},record);
                state.tData.push(record);
                bal += op;
                record = getBlankRec();
                Utilities.copyProperties({vcode: 'OS',trans_date: form.start_date,part: 'Opening Balance',amount_dr: op > 0 ? op:'',amount_cr: op < 0 ? Math.abs(op):'',balance: Utilities.formatNumber(Math.abs(bal)),bal_dr_cr: bal > 0 ? 'Dr':'Cr'},record);
                state.tData.push(record);
                dr_tot += (op > 0 ? op:0)*1;
                cr_tot += (op < 0 ? Math.abs(op):0)*1;
                ac_data = 'Y';
            }
            if(state.data[ac_id]) {
                $.each(state.data[ac_id],function(key,row) {
                    if(ac_data == 'N') {
                        record = getBlankRec('Y');
                        Utilities.copyProperties({part: name},record);
                        state.tData.push(record);
                    }
                    bal = Utilities.round(bal*1+(row.dr_cr == 'D' ? row.amount:-1*row.amount)*1);
                    record = getBlankRec();
                    Utilities.copyProperties(row,record);
                    Utilities.copyProperties({amount_dr: row.dr_cr == 'D' ? row.amount:'',amount_cr: row.dr_cr == 'C' ? row.amount:'',balance: Utilities.formatNumber(Math.abs(bal)),bal_dr_cr: bal > 0 ? 'Dr':'Cr'},record);
                    record.vtype = Utilities.getVchrType(record.vcode,record.vchr_no);
                    state.tData.push(record);
                    dr_tot += (row.dr_cr == 'D' ? row.amount:0)*1;
                    cr_tot += (row.dr_cr == 'C' ? row.amount:0)*1;
                    ac_data = 'Y';
                })
            }
            if(ac_data == 'Y') {
                record = getBlankRec('Y');
                record['amount_cr'] = Utilities.formatNumber(cr_tot);
                record['amount_dr'] = Utilities.formatNumber(dr_tot);
                record['part'] = 'Total';
                state.tData.push(record);
            }
        });
    }

    const  getBlankRec= (bold = 'N')=> {
        return {
            vcode: "",
            trans_date: '',
            due_date: '',
            vchr_no: '',
            vtype: "",
            part: "",
            weight: "",
            amount_dr: '',
            amount_cr: '',
            balance: '',
            bal_dr_cr: '',
            bold: bold,
        };
    }




</script>

<template>
    <AppLayout title="Ledger Report">
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

                    <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2 " >
                        <div class="mb-0">
                            <InputLabel for="opening_bal" value="Account"/>
                            <account-select v-model="form.ac_ids" :multiple="true" :report="true" type="all"
                            @updateAccount = "updateAccount"
                            ></account-select>
                            <InputError class="mt-2" :message="form.errors.get('opening_bal')" />
                        </div>
                    </div>
                      <div class="w-full max-w-full px-3 md:w-2/3 lg:w-2/3">
                        <div class="mb-0">
                            <InputLabel for="branch_ids" value="Branches" />
                            <branch-select :multiple="true" url="report-branches/filtered" v-model="form.branch_ids"  @updateBranch="updateBranch" :error="form.errors.get('branch_ids') ? true :false"></branch-select>
                            <InputError class="mt-2" :message="form.errors.get('branch_ids')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/3 " >
                        <div class="mb-0">
                            <InputLabel for="opening_bl" value="Opening Balance"/>
                            <SelectInput v-model="form.opening_bal" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('opening_bal')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="dr_cr" value="Debit/Credit"/>
                            <SelectInput v-model="form.dr_cr" :options ="[{'id':'C','text':'Credit'},{'id':'D','text':'Debit'},{'id':'A','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('dr_cr')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="weight" value="Weight"/>
                            <SelectInput v-model="form.weight" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('weight')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="due_date" value="Due Date"/>
                            <SelectInput v-model="form.due_date" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('due_date')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="voucher_no" value="Voucher No."/>
                            <SelectInput v-model="form.voucher_no" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('voucher_no')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/6 ">
                        <div class="mb-0">
                            <InputLabel for="amount_grt" value="Amount <" />
                            <TextInput v-model="form.amount_grt" type="text"  />
                            <InputError class="mt-2" :message="form.errors.get('amount_grt')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-1/6 ">
                        <div class="mb-0">
                            <InputLabel for="amount_lt" value="Amount >" />
                            <TextInput v-model="form.amount_lt" type="text"  />
                            <InputError class="mt-2" :message="form.errors.get('amount_lt')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2">
                        <div class="mb-0">
                            <InputLabel for="ac_group_ids" value="Account Groups"/>
                            <account-group-select v-model="form.ac_group_ids" :multiple="true" @updateAccountGroup="updateAccountGroup" ></account-group-select>
                            <InputError class="mt-2" :message="form.errors.get('ac_group_ids')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2">
                        <div class="mb-0">
                            <InputLabel for="company_ids" value="Account Groups"/>
                            <company-select v-model="form.company_ids" :multiple="true" @updateCompany="updateCompany" ></company-select>
                            <InputError class="mt-2" :message="form.errors.get('company_ids')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="moved" value="Moved"/>
                            <SelectInput v-model="form.moved" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'},{'id':'A','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('moved')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="zero_bal_ending_date" value="Zero Balance Ending Date"/>
                            <SelectInput v-model="form.zero_bal_ending_date" :options ="[{'id':'Y','text':'Yes'},{'id':'N','text':'No'},{'id':'A','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('zero_bal_ending_date')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-1/4 lg:w-1/6 " >
                        <div class="mb-0">
                            <InputLabel for="search_term_position" value="Search Term Position"/>
                            <SelectInput v-model="form.search_term_position" :options ="[{'id':'S','text':'Start'},{'id':'E','text':'End'},{'id':'M','text':'Middle'},{'id':'G','text':'Global'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('search_term_position')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-3/6 lg:w-3/6 " >
                        <div class="mb-0">
                            <InputLabel for="search_term" value="Account Search Term"/>
                            <TextInput v-model="form.search_term" type="text"  />
                            <InputError class="mt-2" :message="form.errors.get('search_term')" />
                        </div>
                    </div>
                        <div class="w-full max-w-full px-3 md:w-full lg:w-full " >
                        <div class="mb-0">
                            <InputLabel for="particulars" value="Particulars"/>
                            <TextInput v-model="form.particulars" type="text"  />
                            <InputError class="mt-2" :message="form.errors.get('particulars')" />
                        </div>
                    </div>


                </div>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <div class="mt-0">
                        <ButtonComp @buttonClicked="getData" type="save">SHOW</ButtonComp>
                    </div>
                </div>

            </FilterWrapper>
            <ListWrapper title="Ledger Report">
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
</style>
