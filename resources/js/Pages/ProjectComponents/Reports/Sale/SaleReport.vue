

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
        start_date:BCL.start_date,
        to_date:BCL.today,
        branch_ids:[],
        item_ids:[],
        ac_ids:[],
        packed_loose:'all'
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
    console.log('id', id);
    form.ac_ids = id;
}
const setTable = () => {
    state.table = $('#sale_report').DataTable({
        fixedHeader: true,
        //  "iDisplayLength": 10,
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
                    return 'Sale Report';
                },
                function () {

                    return 'Sale Report';
                },

            },
        //    {
        //         extend: 'pdfHtml5',
        //         orientation: 'landscape',
        //     },
            'csvHtml5',
            // 'print'


        ],
        "processing": true,
        "serverSide": false,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        // // lengthMenu: [
        // //     [ 10, 25, 50, -1 ],
        // //     [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        // // ],


        // buttons: [
        //     'pageLength',
        //     {
        //         extend: 'excelHtml5',
        //         header: true,
        //         footer: true,
        //         exportOptions: {
        //             orthogonal: 'export',
        //             // columns: [1, 2, 3,4,5,6]
        //         },
        //         filename:function(){
        //             return 'Ledger';
        //         },
        //         title: function () {
        //             var str = '';
        //             return str;

        //         },
        //         messageTop: function () {
        //             return 'Ledger ( From: ' + form.date_from +' To ' + form.date_to+' )';
        //         },
        //         customize: function(xlsx) {
        //             var sheet = xlsx.xl.worksheets['sheet1.xml'];
        //             var col = $('col', sheet);
        //             var width_details = [];
        //             $('tr:nth-child(1) > th').each(function(e){
        //                 width_details.push($(this).width());
        //             });
        //             console.log(width_details);
        //             var q = 0;
        //             col.each(function () {
        //                 $(this).attr('width', width_details[q]);
        //                 q++;
        //             });
        //         }
        //     },
        // ],
        "autoWidth": true,
        data: [
    ],
        columnDefs: state.columnDefs,
        "sScrollX": true,
    });

    $('thead > tr> th:nth-child(3)').css({ 'min-width': '100px' });
    $('thead > tr> th:nth-child(6)').css({ 'min-width': '300px' });
    $('thead > tr> th:nth-child(8)').css({ 'min-width': '300px' });
    $('thead > tr> th:nth-child(9)').css({ 'min-width': '300px' });
    $('thead > tr> th:nth-child(13)').css({ 'min-width': '200px' });
    $('thead > tr> th:nth-child(15)').css({ 'min-width': '300px' });
    $('thead > tr> th:nth-child(17)').css({ 'min-width': '200px' });
    $('thead > tr> th:nth-child(19)').css({ 'min-width': '300px' });


}



const getData = () =>{
    form['postForm'](base_url.value +'/sale-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.sales;
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
        $('#sale_table').empty();
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
        state.columnDefs.push({title: 'Code',targets: target++,  data: 'vcode'});
        state.columnDefs.push(
            {
                title: 'Date',targets: target++, "render": function ( data, type, row, meta ) {
                    return row.invoice_date;
                }
            }
        );
          state.columnDefs.push(
            {
                title: 'Invoice No.',targets: target++, "render": function ( data, type, row, meta ) {
                    return row.invoice_no;
                }
            }
        );
        state.columnDefs.push({ title: 'Sale Contract No',targets: target++,data: 'contract_no'});
        state.columnDefs.push({ title: 'Sale Order No',targets: target++,data: 'sale_order_no'});
        state.columnDefs.push({ title: 'Client Name',targets: target++,data: 'ac_name'});
        state.columnDefs.push({ title: 'City',targets: target++,data: 'city_name'});
        state.columnDefs.push({ title: 'Bill To party',targets: target++,data: 'bill_to_party'});
        state.columnDefs.push({ title: 'Bill To City',targets: target++,data: 'bill_to_city'});
        state.columnDefs.push({ title: 'Ship To Party',targets: target++,data: 'ship_to_party'});
        state.columnDefs.push({ title: 'Ship To City',targets: target++,data: 'ship_to_city'});
        state.columnDefs.push({ title: 'GR No.',targets: target++,data: 'gr_lr_no'});
        state.columnDefs.push({ title: 'GR Date',targets: target++,data: 'gr_lr_date'});
        state.columnDefs.push({ title: 'Vehicle No',targets: target++,data: 'vehical_no'});
        state.columnDefs.push({ title: 'Delivery Term',targets: target++,data: 'delivery_terms'});
        state.columnDefs.push({ title: 'Item',targets: target++,data: 'item_name'});
        state.columnDefs.push({ title: 'HSN Code',targets: target++,data: 'hsn_code'});
        state.columnDefs.push({ title: 'Packing',targets: target++,data: 'packing_name'});
        state.columnDefs.push({ title: 'Brand',targets: target++,data: 'brand_name'});
        state.columnDefs.push({ title: 'Sale Account',targets: target++,data: 'sale_ac_name'});
        state.columnDefs.push({ title: 'Sale Qty',targets: target++,data: 'sale_qty'});
        state.columnDefs.push({ title: 'Weight',targets: target++,data: 'weight'});
        state.columnDefs.push({ title: 'UoM',targets: target++,data: 'unit'});
        state.columnDefs.push({ title: 'Sales Value',targets: target++,data: 'sale_value'});
        state.columnDefs.push({ title: 'IGST',targets: target++,data: 'igst'});
        state.columnDefs.push({ title: 'CGST',targets: target++,data: 'cgst'});
        state.columnDefs.push({ title: 'SGST',targets: target++,data: 'sgst'});
        state.columnDefs.push({ title: 'VAT',targets: target++,data: 'vat'});
        state.columnDefs.push({ title: 'Surcharge On Vat',targets: target++,data: 'surcharge_amt'});
        state.columnDefs.push({ title: 'CST',targets: target++,data: 'cst'});
        state.columnDefs.push({ title: 'Export Fees',targets: target++,data: 'export_fee'});
        state.columnDefs.push({ title: 'Add Excise Duty',targets: target++,data: 'add_excise_amount'});
        state.columnDefs.push({ title: 'Minus Excise Duty',targets: target++,data: 'less_excise_amount'});
        state.columnDefs.push({ title: 'Bar Code',targets: target++,data: 'bar_code_fees'});
        state.columnDefs.push({ title: 'TCS',targets: target++,data: 'tcs_amount'});
        state.columnDefs.push({ title: 'Freight',targets: target++,data: 'freight'});
        state.columnDefs.push({ title: 'Bill Value',targets: target++,data: 'net_amt'});
        state.columnDefs.push({ title: 'Agent/Broker',targets: target++,data: 'broker'});
        state.columnDefs.push({ title: 'IRN No.',targets: target++,data: 'irn_no'});
    }

    const setData = () =>{
        var id = 0;
        var record = {};
        state.tData = [];
        $.each(state.data,function(key,row) {
            record = getBlankRec();
            if(id == 0 || id != row.id) {
                id = row.id
                Utilities.copyProperties(row,record);
                record.freight = row.freight*1+row.freight_amount*1;
            } else {
                Utilities.copyProperties(row,record,'N',['vat','cst','export_fee','add_excise_amount','less_excise_amount','bar_code_fees','tcs_amount','freight','net_amt','surcharge_amt'],'E');
            }
            record.delivery_terms = row.delivery_terms ? (row.delivery_terms == 'F' ? 'For' : (row.delivery_terms == 'M' ? 'EX-MILL':'EX-KANDA')):'';
            state.tData.push(record);
        })
    }

    const  getBlankRec= (bold = 'N')=> {
        return {
            vcode: '',
            invoice_date: '',
            invoice_no: '',
            contract_no: "",
            sale_order_no: "",
            ac_name: '',
            city_name: '',
            bill_to_party: "",
            bill_to_city: '',
            ship_to_party: "",
            ship_to_city: '',
            gr_lr_no: '',
            gr_lr_date: '',
            vehical_no: '',
            delivery_terms:'',
            item_name:'',
            hsn_code: '',
            packing_name: '',
            brand_name: '',
            sale_ac_name:'',
            sale_qty: '',
            weight:'',
            unit: '',
            sale_value: '',
            igst: '',
            cgst:'',
            sgst:'',
            vat: '',
            surcharge_amt: '',
            cst: '',
            export_fee: '',
            add_excise_amount:'',
            less_excise_amount:'',
            bar_code_fees: '',
            tcs_amount: '',
            freight: '',
            net_amt:'',
            broker:'',
            irn_no:'',
            bold: bold,
        };
    }



</script>

<template>
    <AppLayout title="Sale Report">
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
                            <item-select :report="true" :multiple="true"  @updateItem="updateItem" :error="form.errors.get('item_ids') ? true :false"></item-select>
                            <InputError class="mt-2" :message="form.errors.get('item_ids')" />
                        </div>
                    </div>

                      <div class="w-full max-w-full px-3 md:w-2/3 lg:w-2/3">
                        <div class="mb-0">
                            <InputLabel for="branch_ids" value="Branches" />
                            <branch-select  :report="true"  :index="'branch_'" url="report-branches/filtered"  :multiple="true"    @updateBranch="updateBranch" :error="form.errors.get('branch_ids') ? true :false"></branch-select>
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
            <ListWrapper title="Sale Report">
                <template #table>
                    <table id="sale_report" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

