

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
import SaleContractSelect from '@/Pages/ProjectComponents/SelectComponents/SaleContractSelect.vue';
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
        sale_contract_ids:[],
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
    form.ac_ids = id;
}
const updateSaleContract = (id,index,data) =>{
    form.sale_contract_ids = id;
}
const setTable = () => {
    state.table = $('#ledger_report').DataTable({
        fixedHeader: true,
        dom: 'Bfrtip',
        // "iDisplayLength": 10,
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
                    return 'Contract Execution';
                },
                function () {

                    return 'Contract Execution';
                },

            },
            // {
            //     extend: 'pdfHtml5',
            //     orientation: 'landscape',
            // },
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

        "autoWidth": true,
        data: [
    ],
        columnDefs: state.columnDefs,
        "sScrollX": true,
    });

    $('thead > tr> th:nth-child(3)').css({ 'min-width': '100px', 'max-width': '100px' });
    $('thead > tr> th:nth-child(4)').css({ 'min-width': '300px', 'max-width': '500px' });
    $('thead > tr> th:nth-child(6)').css({ 'min-width': '300px', 'max-width': '300px' });
    $('thead > tr> th:nth-child(7)').css({ 'min-width': '300px', 'max-width': '300px' });

}



const getData = () =>{
    form['postForm'](base_url.value +'/contract-execution-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.contract_execution;
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
        state.columnDefs.push({title: 'Contract No.',targets: target++,  data: 'contract_no'});
        state.columnDefs.push({title: 'Date',targets: target++,data:'contract_date'});
        state.columnDefs.push({title: 'Branch',targets: target++, data:'branch_name'});
        state.columnDefs.push({ title: 'Client ID',targets: target++,data: 'ac_id'});
        state.columnDefs.push({ title: 'Client Name',targets: target++,data: 'ac_name'});
        state.columnDefs.push({ title: 'Item Name',targets: target++,data: 'item_name'});
        state.columnDefs.push({ title: 'Item Description',targets: target++,data: 'item_desc'});
        state.columnDefs.push({ title: 'Quantity',targets: target++,data: 'qty'});
        state.columnDefs.push({ title: 'Uom of Qty.',targets: target++,data: 'unit_name'});
        state.columnDefs.push({ title: 'Packed/Loose',targets: target++,data: 'packed_loose'});
        state.columnDefs.push({ title: 'Rate',targets: target++,data: 'rate'});
        state.columnDefs.push({ title: 'GST/VAT(Exclusive or Inclusive)',targets: target++,data: 'gst_vat'});
        state.columnDefs.push({ title: 'Delivery Term',targets: target++,data: 'delivery_term'});
        state.columnDefs.push({ title: 'Valid_upto',targets: target++,data: 'valid_to_date'});
        state.columnDefs.push({ title: 'SO Number',targets: target++,data: 'so_no'});
        state.columnDefs.push({ title: 'SO Date',targets: target++,data: 'so_date'});
        state.columnDefs.push({ title: 'SO Packing',targets: target++,data: 'so_packing'});
        state.columnDefs.push({ title: 'SO Qty',targets: target++,data: 'so_qty'});
        state.columnDefs.push({ title: 'SO Weight',targets: target++,data: 'so_weight'});
        state.columnDefs.push({ title: 'SO Rate',targets: target++,data: 'so_rate'});
        state.columnDefs.push({ title: 'Discount',targets: target++,data: 'discount'});
        state.columnDefs.push({ title: 'Freight',targets: target++,data: 'freight'});
        state.columnDefs.push({ title: 'Packing',targets: target++,data: 'packing_cost'});
        state.columnDefs.push({ title: 'Final SO Rate',targets: target++,data: 'final_rate'});
        state.columnDefs.push({ title: 'Pending Qty',targets: target++,data: 'pending_qty'});
        state.columnDefs.push({ title: 'Invoice No',targets: target++,data: 'invoice_no'});
        state.columnDefs.push({ title: 'Invoice Date',targets: target++,data: 'invoice_date'});
    }

    const setData = () => {
        var record = {};
        var first_time = 'Y';
        var pend_qty = 0;
        var qty = 0;
        var ci_key = '';
        state.tData = [];
        $.each(state.data,function(key,row) {
            record = getBlankRec();
            Utilities.copyProperties(row,record);
            record.delivery_term = row.delivery_terms == 'F' ? 'For' : (row.delivery_terms == 'M' ? 'EX-MILL':'EX-KANDA');
            record.gst_vat = record.gst_terms == 'I' ? 'Inclusive' : 'Exclusive';
            ci_key = 'C'+row.id+'I'+row.item_id;
            pend_qty = row.qty;
            first_time = 'Y'
            if(state.sale_orders[ci_key]) {
                $.each(state.sale_orders[ci_key],function(key1,row1) {
                    if(first_time == 'N') {
                        record = getBlankRec();
                    }
                    record.so_no = row1.sale_order_no;
                    record.so_date = row1.sale_order_date;
                    record.so_packing = row1.packing;
                    record.so_qty = row1.qty;
                    record.so_weight = row1.weight;
                    record.so_rate = row1.rate;
                    record.discount = row1.discount;
                    record.freight = row1.freight;
                    record.packing_cost = row1.packing_cost;
                    record.final_rate = row1.net_rate;
                    record.invoice_no = row1.invoice_no;
                    record.invoice_date = row1.invoice_date ? moment(row1.invoice_date,'YYYY-MM-DD').format('DD-MM-YYYY'):'';
                    pend_qty -= Utilities.round(((row.packed_loose == 'packed' && row.unit_id != row1.unit_id_qtl) || (row.packed_loose == 'loose' && row1.rate_on == 'Q')) ? row1.qty:row1.weight,3);
                    pend_qty = Utilities.round(pend_qty,3);
                    record.pending_qty = pend_qty;
                    state.tData.push(record);
                    first_time = 'N';
                })
            }
            if(first_time == 'Y') {
                state.tData.push(record);
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
            item_desc: '',
            qty: "",
            unit_name: '',
            packed_loose: '',
            rate: '',
            gst_vat: '',
            delivery_term:'',
            valid_to_date:'',
            so_no: '',
            so_date: '',
            so_packing: '',
            so_qty: '',
            so_weight: '',
            so_rate: '',
            discount: '',
            freight: '',
            packing_cost: '',
            final_rate: '',
            pending_qty: '',
            invoice_no: '',
            invoice_date: '',
            bold: bold,
        };
    }



</script>

<template>
    <AppLayout title="Contract Execution Report">
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
                            <item-select :multiple="true" :report="true"    @updateItem="updateItem" :error="form.errors.get('item_ids') ? true :false"></item-select>
                            <InputError class="mt-2" :message="form.errors.get('item_ids')" />
                        </div>
                    </div>

                      <div class="w-full max-w-full px-3 md:w-2/3 lg:w-2/3">
                        <div class="mb-0">
                            <InputLabel for="branch_ids" value="Branches" />
                            <branch-select :multiple="true" url="report-branches/filtered"  @updateBranch="updateBranch" :error="form.errors.get('branch_ids') ? true :false"></branch-select>
                            <InputError class="mt-2" :message="form.errors.get('branch_ids')" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 md:w-1/3 lg:w-1/3 " >
                        <div class="mb-0">
                            <InputLabel for="type" value="Packed /Loose"/>
                            <SelectInput v-model="form.packed_loose" :options ="[{'id':'loose','text':'Loose'},{'id':'packed','text':'Packed'},{'id':'all','text':'All'}]"></SelectInput>
                            <InputError class="mt-2" :message="form.errors.get('packed_loose')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-full lg:w-full">
                        <div class="mb-0">
                            <InputLabel for="ac_ids" value="Clients" />
                            <account-select  :index="-2" :multiple="true" :key="-2" :error="form.errors.get('ac_ids') ? true :false"
                                @updateAccount = "updateAccount" :report="true"
                            ></account-select>
                            <InputError class="mt-2" :message="form.errors.get('ac_ids')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-full lg:w-full">
                        <div class="mb-0">
                            <InputLabel for="sale_contract_ids" value="Sale Contracts" />
                             <sale-contract-select  :index="-2" :multiple="true" :key="-2" :error="form.errors.get('sale_contract_ids') ? true :false"
                                @updateSaleContract = "updateSaleContract" :report="true"
                            ></sale-contract-select>
                            <InputError class="mt-2" :message="form.errors.get('sale_contract_ids')" />
                        </div>
                    </div>

                </div>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <div class="mt-0">
                        <ButtonComp @buttonClicked="getData" type="save">SHOW</ButtonComp>
                    </div>
                </div>

            </FilterWrapper>
            <ListWrapper title="Contract Execution Report">
                <template #table>
                    <table id="ledger_report" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

