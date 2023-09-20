

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
    cols_width: [],
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
    form.ac_ids = id;
}

const setTable = () => {
    state.table = $('#ledger_report').DataTable({
        fixedHeader: true,
        serverSide:false,
          dom: 'Bfrtip',
            // "iDisplayLength": 10,
            "autoWidth":false,
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
                    return 'Contract Booking';
                },
                function () {

                    return 'Contract Booking';
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

        "autoWidth": true,
        data: [
    ],
        columnDefs: state.columnDefs,
        "sScrollX": true,
    });
    $.each(state.cols_width,function(key,row) {
        $('thead > tr> th:nth-child('+row.col_no+')').css({ 'min-width': row.min_width+'px', 'max-width': row.max_width+'px' });
    })

    // $('thead > tr> th:nth-child(6)').css({ 'min-width': '250px' });
    // $('thead > tr> th:nth-child(7)').css({ 'min-width': '350px' });
    // $('thead > tr> th:nth-child(13)').css({ 'min-width': '250px' });
    // $('thead > tr> th:nth-child(13)').css({ 'min-width': '200px' });
    // $('thead > tr> th:nth-child(15)').css({ 'min-width': '300px' });
    // $('thead > tr> th:nth-child(17)').css({ 'min-width': '200px' });
    // $('thead > tr> th:nth-child(19)').css({ 'min-width': '300px' });


}



const getData = () =>{
    form['postForm'](base_url.value +'/contract-booking-report')
    .then(function (response)
    {
        if(response.success) {
            state.data = response.contract_booking;
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
        state.cols_width = [];
        var target = 0;
        state.columnDefs = [
            { targets: '_all', visible: true },
            { title: 'S.No',targets: target++, "render": function ( data, type, row, meta ) {
                return meta.row+1;
            }}
        ];
        state.columnDefs.push({title: 'Date',targets: target++, data: 'contract_date'});
        state.columnDefs.push({title: 'Booking No.',targets: target++, data:'contract_no'});
        state.columnDefs.push({ title: 'Client Name',targets: target++,data: 'ac_name',"width": "50%"});
        state.cols_width.push({col_no: target,min_width: '250',max_width:'250'});
        state.columnDefs.push({ title: 'Item Name',targets: target++,data: 'item_name'});
        state.cols_width.push({col_no: target,min_width: '350',max_width:'350'});
        state.columnDefs.push({ title: 'Quantity',targets: target++,data: 'qty',sorting:false,className:'text-right'});
        state.columnDefs.push({ title: 'Uom of Qty.',targets: target++,data: 'unit_name'});
        state.columnDefs.push({ title: 'Rate',targets: target++,data: 'rate'});
        state.columnDefs.push({ title: 'GST/VAT(Exclusive or Inclusive)',targets: target++,data: 'gst_vat'});
        state.columnDefs.push({ title: 'Delivery Term',targets: target++,data: 'delivery_terms',
            "render": function ( data, type, row, meta ) {
                return data == 'F' ? 'For' : (data == 'M' ? 'EX-MILL':'EX-KANDA');
            }}
        );
        state.columnDefs.push({ title: 'Broker',targets: target++,data: 'broker'});
        state.cols_width.push({col_no: target,min_width: '250',max_width:'250'});
        state.columnDefs.push({ title: 'Brokerage',targets: target++,data: 'brokerage_rate'});
        state.columnDefs.push({ title: 'Delivery Start Date',targets: target++,data: 'valid_from_date'});
        state.columnDefs.push({ title: 'Delivery End Date',targets: target++,data: 'valid_to_date'});
        state.columnDefs.push({ title: 'Delivery Extended Date',targets: target++,data: 'valid_extended'});
        state.columnDefs.push({ title: 'Payment Term',targets: target++,data: 'pay_term'});
        state.columnDefs.push({ title: 'Tolerance',targets: target++,data: 'tolerance_per'});
    }

    const setData = () =>{
        var record = {};
        state.tData = [];
        $.each(state.data,function(key,row) {
            record = getBlankRec('Y');
            Utilities.copyProperties(row,record);
            record.gst_vat = row.gst_terms == 'I' ? 'Inclusive':'Exclusive';
            state.tData.push(record);
        })
    }

    const  getBlankRec= (bold = 'N')=> {
        return {
            vcode: "",
            contract_date: '',
            contract_no: '',
            ac_id: '',
            ac_name: "",
            item_name: '',
            qty: '',
            unit_name: "",
            rate: '',
            gst_vat: '',
            delivery_terms: '',
            broker: '',
            brokerage_rate:'',
            valid_from_date:'',
            valid_to_date:'',
            valid_extended:'',
            pay_term:'',
            tolerance_per:'',
            bold: bold
        };
    }



</script>

<template>
    <AppLayout title="Contract Booking Report">
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
                            <item-select :report="true"  :multiple="true"   @updateItem="updateItem" :error="form.errors.get('item_ids') ? true :false"></item-select>
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
                    <div class="w-full max-w-full px-3 md:w-full lg:w-full">
                        <div class="mb-0">
                            <InputLabel for="ac_ids" value="Clients" />
                            <account-select  :index="-2" :multiple="true" :key="-2" :error="form.errors.get('ac_ids') ? true :false"
                                @updateAccount = "updateAccount" :report="true"
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
            <ListWrapper title="Contract Booking Report">
                <template #table>
                    <table id="ledger_report" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

