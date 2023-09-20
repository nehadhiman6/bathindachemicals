

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
    tData:[],
    columnDefs:[],
    table: null,
});

 const form = reactive( new Form({
        start_date:'',
        to_date:'',
        branches_ids:[],
        item_ids:[],
        ac_ids:[],
        packed_loose:''
}));




onMounted(() => {
    // setTable();
});

const updateBranch = (id,index,data) =>{
    form.branches_ids = id;
}
const updateItem = (id,index,data) =>{
    form.item_ids = id;
}
const updateAccount = (id,index,data) =>{
    form.ac_ids = id;
}

const setTable = () => {
    state.table = $('#sale_report_format').DataTable({
        fixedHeader: true,
              dom: 'Bfrtip',
        lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        "iDisplayLength": 10,
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
                    return 'Sale Report Form';
                },
                function () {

                    return 'Sale Report Form ';
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
}



const getData = () =>{
    form['postForm'](base_url.value +'/sale-format-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.sale_format;
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
        state.columnDefs.push(
            {
                title: 'Code',targets: target++,  data: 'vcode'
                ,"render": function ( data, type, row, meta ) {
                var str = '';
                return str;
            }
        });
        state.columnDefs.push(
            {
                title: 'Date',targets: target++, "render": function ( data, type, row, meta ) {
                    return row.invoice_date;
                }
            }
        );
        state.columnDefs.push(
            {
                title: 'Booking No.',targets: target++, "render": function ( data, type, row, meta ) {
                    return row.booking_no;
                }
            }
        );
        state.columnDefs.push({ title: 'Client ID',targets: target++,data: 'ac_id'});
        state.columnDefs.push({ title: 'Client Name',targets: target++,data: 'ac_name'});
        state.columnDefs.push({ title: 'Item Name',targets: target++,data: 'item_name'});
        state.columnDefs.push({ title: 'Quantity',targets: target++,data: 'qty'});
        state.columnDefs.push({ title: 'Uom of Qty.',targets: target++,data: 'uom_of_qty'});
        state.columnDefs.push({ title: 'Rate',targets: target++,data: 'rate'});
        state.columnDefs.push({ title: 'GST/VAT(Exclusive or Inclusive)',targets: target++,data: 'gst_vat'});
        state.columnDefs.push({ title: 'Delivery Term',targets: target++,data: 'delivery_term'});
        state.columnDefs.push({ title: 'Broker',targets: target++,data: 'broker'});
        state.columnDefs.push({ title: 'Brokerage',targets: target++,data: 'brokerage'});
        state.columnDefs.push({ title: 'Delivery Start Date',targets: target++,data: 'delivery_start_date'});
        state.columnDefs.push({ title: 'Delivery End Date',targets: target++,data: 'delivery_end_date'});
        state.columnDefs.push({ title: 'Delivery Extended Date',targets: target++,data: 'delivery_extended_date'});
        state.columnDefs.push({ title: 'Payment Term',targets: target++,data: 'payment_term'});
        state.columnDefs.push({ title: 'Tolerance',targets: target++,data: 'tolerance'});
    }

    const setData = () =>{
        var bal = 0;
        var dr_tot = 0;
        var cr_tot = 0;
        var acid = 0;
        var name = '';
        var record = {};
        var bal_drcr = '';
        state.tData = [];
            $.each(state.data,function(key,row) {
                if(name == '' || name != row.name) {
                    if(name != 0) {
                        record = getBlankRec('Y');
                        record['amountcr'] = cr_tot;
                        record['amountdr'] = dr_tot;
                        record['part'] = 'Total';
                        state.tData.push(record);
                        dr_tot = 0;
                        cr_tot = 0;
                        bal = 0;
                    }
                    acid = row.acid;
                    name = row.name;
                    record = getBlankRec('Y');
                    record['part'] = "Account Name :"  + row.name;
                    state.tData.push(record);
                }

                bal += row.amountdr-row.amountcr;
                bal_drcr = bal > 0 ? 'Dr':'Cr';
                var amtdr = row.amountdr < 0 ? 0:row.amountdr;
                var amtcr = row.amountdr < 0 ? Math.abs(row.amountdr):row.amountcr;
                record = getBlankRec();
                Utilities.copyProperties(row,record);
                record['amountcr'] = amtcr;
                record['amountdr'] = amtdr;
                record['ref_date'] = row.ref_date ? moment(row.ref_date,'YYYY-MM-DD').format('DD-MM-YYYY'):'' ;
                record['balance'] = Utilities.currencyNumber(Math.abs(bal))+' '+bal_drcr;
                record['attached'] = row.att_vcode;
                state.tData.push(record);
                dr_tot += Utilities.round(amtdr);
                cr_tot += Utilities.round(amtcr);
            });
            if(name != '') {
                record = getBlankRec('Y');
                record['amountcr'] = cr_tot;
                record['amountdr'] = dr_tot;
                record['part'] = "Total";
                state.tData.push(record);
            }
    }

    const  getBlankRec= (bold = 'N')=> {
        return {
            amountcr: '',
            amountdr: '',
            name: '',
            part: "",
            trans_date: '',
            balance: '',
            vcode: "",
            vtype: "",
            bold: bold,
            attached: '',
            ref_no: '',
            ref_date: '',
            bill_type: '',
            doc_type:'',
            bill_no:'',
        };
    }



</script>

<template>
    <AppLayout title="Sale Report Format">
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
                            <item-select :multiple="true"   @updateItem="updateItem" :error="form.errors.get('item_ids') ? true :false"></item-select>
                            <InputError class="mt-2" :message="form.errors.get('item_ids')" />
                        </div>
                    </div>

                      <div class="w-full max-w-full px-3 md:w-2/3 lg:w-2/3">
                        <div class="mb-0">
                            <InputLabel for="branch_ids" value="Branches" />
                            <branch-select :multiple="true"   @updateBranch="updateBranch" :error="form.errors.get('branch_ids') ? true :false"></branch-select>
                            <InputError class="mt-2" :message="form.errors.get('branch_ids')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3 " >
                        <div class="mb-0">
                            <InputLabel for="packed_loose" value="Packed /Loose"/>
                            <SelectInput v-model="form.packed_loose" :options ="[{'id':'loose','text':'Loose'},{'id':'packed','text':'Packed'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('packed_loose')" />
                        </div>
                    </div>
                </div>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <div class="mt-0">
                        <ButtonComp @buttonClicked="getData" type="save">SHOW</ButtonComp>
                    </div>
                </div>

            </FilterWrapper>
            <ListWrapper title="Sale Format Report">
                <template #table>
                    <table id="sale_report_format" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

