

<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import FilterWrapper from '@/Pages/CustomComponents/Sections/FilterWrapper.vue';
import SaleReportNav from '@/Pages/ProjectComponents/Reports/Sale/SaleReportsNav.vue';
import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
import AccountGroupSelect from '@/Pages/ProjectComponents/SelectComponents/AccountGroupSelect.vue';
import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
import globalMixin from '../../../../globalMixin';
import InputError from '@/Components/InputError.vue';


const { base_url,canAny} = globalMixin();
const page = usePage();


const state = reactive({
    data: [],
    sale_orders: {},
    tData:[],
    columnDefs:[],
    table: null,
});

 const form = reactive( new Form({
        start_date:BCL.start_date,
       to_date:BCL.today,
        branch_ids:[],
        item_ids:[],
        ac_ids:[],
        packed_loose:'all',
        status:'a',
        pending_grt:''
}));




onMounted(() => {
    // setTable();
});

const updateBranch = (id,index,data) =>{
    form.branch_ids = id;
}
const updateItem = (id,index,data) =>{
    form.item_ids = id;
}
const updateAccount = (id,index,data) =>{
    form.ac_ids = id;
}

const setTable = () => {
    state.table = $('#ledger_report').DataTable({
        fixedHeader: true,
        //   "iDisplayLength": 10,
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
                    return 'Pending';
                },
                function () {

                    return 'Pending ';
                },

            },
            'csvHtml5',


        ],
     "processing": true,
        "serverSide": false,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        // lengthMenu: [
        //     [ 10, 25, 50, -1 ],
        //     [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        // ],


        // buttons: [
        //     'pageLength',
        //     // {
        //     //     extend: 'excelHtml5',
        //     //     header: true,
        //     //     footer: true,
        //     //     exportOptions: {
        //     //         orthogonal: 'export',
        //     //         // columns: [1, 2, 3,4,5,6]
        //     //     },
        //     //     filename:function(){
        //     //         return 'Ledger';
        //     //     },
        //     //     title: function () {
        //     //         var str = '';
        //     //         return str;

        //     //     },
        //     //     messageTop: function () {
        //     //         return 'Ledger ( From: ' + form.date_from +' To ' + form.date_to+' )';
        //     //     },
        //     //     customize: function(xlsx) {
        //     //         var sheet = xlsx.xl.worksheets['sheet1.xml'];
        //     //         var col = $('col', sheet);
        //     //         var width_details = [];
        //     //         $('tr:nth-child(1) > th').each(function(e){
        //     //             width_details.push($(this).width());
        //     //         });
        //     //         console.log(width_details);
        //     //         var q = 0;
        //     //         col.each(function () {
        //     //             $(this).attr('width', width_details[q]);
        //     //             q++;
        //     //         });
        //     //     }
        //     // },
        // ],
        "autoWidth": true,
        data: [
    ],
        columnDefs: state.columnDefs,
        "sScrollX": true,
    });
    $('thead > tr> th:nth-child(3)').css({ 'min-width': '100px', 'max-width': '100px' });
    $('thead > tr> th:nth-child(4)').css({ 'min-width': '300px', 'max-width': '500px' });
    $('thead > tr> th:nth-child(5)').css({ 'min-width': '300px', 'max-width': '300px' });
    $('thead > tr> th:nth-child(6)').css({ 'min-width': '100px', 'max-width': '300px' });
    $('thead > tr> th:nth-child(7)').css({ 'min-width': '300px', 'max-width': '300px' });
}



const getData = () =>{
    form['postForm'](base_url.value +'/pending-contract-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.contracts;
            state.sale_orders = response.sale_orders;
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
        $('#ledger_table').empty();
    }
    setColumns();
    setData();
    setTable();
    state.table.clear();
    state.table.rows.add(state.tData).draw();
}
    const setColumns = () => {
        state.columnDefs=[];
        var target = 0;
        state.columnDefs = [
            { targets: '_all', visible: true },
            { title: 'Sr No',targets: target++, "render": function ( data, type, row, meta ) {
                return meta.row+1;
            }}
        ];
        state.columnDefs.push({ title: 'Contract no.',targets: target++,data: 'contract_no'});
        state.columnDefs.push({ title: 'Contract Date',targets: target++,data: 'contract_date'});
        state.columnDefs.push({ title: 'Branch',targets: target++,data: 'branch_name'});
        state.columnDefs.push({ title: 'Item Description',targets: target++,data: 'item_name'});
        state.columnDefs.push({ title: 'Contract Qty',targets: target++,data: 'qty'});
        state.columnDefs.push({ title: 'Client Name',targets: target++,data: 'ac_name'});
        state.columnDefs.push({ title: 'Packed/Loose',targets: target++,data: 'packed_loose'});
        state.columnDefs.push({ title: 'Qty UOM',targets: target++,data: 'unit_name'});
        state.columnDefs.push({ title: 'Rate',targets: target++,data: 'rate'});
        state.columnDefs.push({ title: 'Delivery Term',targets: target++,data: 'delivery_term'});
        state.columnDefs.push({ title: 'GST/VAT(INCL/EXLU)',targets: target++,data: 'gst_terms'});
        state.columnDefs.push({ title: 'Valid Upto',targets: target++,data: 'valid_to_date'});
        state.columnDefs.push({ title: 'SO Qty',targets: target++,data: 'so_qty'});
        state.columnDefs.push({ title: 'Short Close(QTY)',targets: target++,data: 'sc_qty'});
        state.columnDefs.push({ title: 'Pending QTY',targets: target++,data: 'pending_qty'});
    }

    const setData = () =>{
        var record = {};
        var first_time = 'Y';
        var pend_qty = 0;
        var ci_key = '';
        var row1 = null;
        state.tData = [];
        $.each(state.data,function(key,row) {
            record = getBlankRec();
            Utilities.copyProperties(row,record);
            record.delivery_term = row.delivery_terms == 'F' ? 'For' : (row.delivery_terms == 'M' ? 'EX-MILL':'EX-KANDA');
            record.gst_terms = record.gst_terms == 'I' ? 'Inclusive' : 'Exclusive';
            ci_key = 'C'+row.id+'I'+row.item_id;
            pend_qty = row.qty;
            if(state.sale_orders[ci_key]) {
                row1 = state.sale_orders[ci_key][0];
                record.so_qty = row1.qty;
                pend_qty -= Utilities.round(row1.qty,3);
                pend_qty = Utilities.round(pend_qty,3);
            }
            record.pending_qty = pend_qty;
            if((Utilities.round(form.pending_grt) == 0 || pend_qty >= Utilities.round(form.pending_grt))) {
                if(form.status == 'a' || (form.status == 'p' && pend_qty > 0) || (form.status == 'c' && pend_qty <= 0)) {
                    state.tData.push(record);
                }
            }
        });
    }

    const  getBlankRec= (bold = 'N')=> {
        return {
            vcode: "",
            contract_no: '',
            contract_date: '',
            branch_name: '',
            ac_id: '',
            ac_name: "",
            item_name: '',
            qty: "",
            unit_name: '',
            packed_loose: '',
            rate: '',
            gst_terms: '',
            delivery_term:'',
            valid_to_date:'',
            so_qty: '',
            sc_qty: '',
            pending_qty: '',
            bold: bold,
        };
    }



</script>

<template>
    <AppLayout title="Pending Contract Report">
        <SaleReportNav>
        </SaleReportNav>
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
                      <div class="w-full max-w-full px-3 md:w-2/4 lg:w-2/4">
                        <div class="mb-0">
                            <InputLabel for="item_ids" value="Items" />
                            <item-select :multiple="true"   @updateItem="updateItem" :report="true"  :error="form.errors.get('item_ids') ? true :false"></item-select>
                            <InputError class="mt-2" :message="form.errors.get('item_ids')" />
                        </div>
                    </div>

                      <div class="w-full max-w-full px-3 md:w-2/3 lg:w-2/3">
                        <div class="mb-0">
                            <InputLabel for="branch_ids" value="Branches" />
                            <branch-select :multiple="true" url="report-branches/filtered" @updateBranch="updateBranch" :error="form.errors.get('branch_ids') ? true :false"></branch-select>
                            <InputError class="mt-2" :message="form.errors.get('branch_ids')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3 " >
                        <div class="mb-0">
                            <InputLabel for="packed_loose" value="Packed /Loose"/>
                            <SelectInput v-model="form.packed_loose" :options ="[{'id':'loose','text':'Loose'},{'id':'packed','text':'Packed'},{'id':'all','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('packed_loose')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3 " >
                        <div class="mb-0">
                            <InputLabel for="status" value="Status"/>
                            <SelectInput v-model="form.status" :options ="[{'id':'p','text':'Pending'},{'id':'c','text':'Completed'},{'id':'a','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('status')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3 " >
                        <div class="mb-0">
                            <InputLabel for="pending_grt" value="Pending Qty >="/>
                            <TextInput v-model="form.pending_grt" type="text"  />
                            <InputError class="mt-2" :message="form.errors.get('pending_grt')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-full lg:w-full">
                        <div class="mb-0">
                            <InputLabel for="ac_ids" value="Clients" />
                            <account-select  :index="-2" :multiple="true" :key="-2" :error="form.errors.get('ac_ids') ? true :false"
                                :report="true"
                                @updateAccount = "updateAccount"
                            ></account-select>
                            <InputError class="mt-2" :message="form.errors.get('ac_ids')" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <div class="mt-0">
                        <ButtonComp @buttonClicked="getData" type="save">SHOW</ButtonComp>
                    </div>
                </div>

            </FilterWrapper>
            <ListWrapper title="Pending Contract Report">
                <template #table>
                    <table id="ledger_report" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

