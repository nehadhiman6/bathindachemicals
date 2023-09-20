<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import globalMixin from '../../../../globalMixin';

const { base_url} = globalMixin();
const props = defineProps(['items']);
const state = reactive({
    table:null,
    tData:[]

})
const data = reactive({create_url:'posting-detail' });
onMounted(() => {
    state.tData = props.items;
    setTable();
    reloadTable();
});

const setTable = () => {
    let target = 0;
    state.table = $('#posting').DataTable({
        "processing": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: "10",
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        data: [],
        columns: [
            { title: 'S.No.', targets: target++, data: 'id',
          "render": function( data, type, row, meta) {
            return meta.row + 1;
          }},
          { title: 'Account Name', targets: target++, data: 'name' },
          { title: 'Particular', targets: target++, data: 'part' },
          { title: 'Dr Amount', targets: target++,
            "render": function( data, type, row, meta) {
                return row.dr_cr == 'D'? row.amount:'';
            }},
          { title: 'Cr Amount' , targets: target++,"render": function( data, type, row, meta) {
                return row.dr_cr == 'C' ? row.amount:'';
            }},
        ],
        "drawCallback": function (settings) {

        }
    });
}

const reloadTable = ()=>{
    state.table.clear();
    state.table.rows.add(state.tData).draw();
}

</script>

<template>
    <AppLayout title="Posting Details">
        <contractual-nav>
        </contractual-nav>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Filter
            </h2>
        </template>
        <div>
            <ListWrapper title="Posting Details">
                <template #table>
                    <table id="posting" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>
<style>

</style>
