<script setup>
import { ref, onMounted, reactive,nextTick } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import GstForm from '@/Pages/ProjectComponents/Items/Gst/GstForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny } = globalMixin();
const page = usePage();
const state = reactive({
    formOpen: false,
    table: null,
    form_id: 0,
    changeRate:false
});


const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.changeRate = false;
    state.table.ajax.reload(null, false);
    // Inertia.reload();
}

const editGst = (id) => {
    nextTick(()=>{
        state.formOpen = true;
        state.form_id = id;
    });
}

const changeGST = (id) => {
    state.formOpen = true;
    state.form_id = id;
    state.changeRate = true;
}




onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#gsts_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/gsts-list",
            "type": "GET",
        },
        'createdRow': function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
        columns: [{
                title: 'Sr no.',
                data: 'id',
                "render": function (data, type, row, meta) {
                    var str = meta.row + parseInt(meta.settings.json.start) +1;
                    return  str;
                }
            },
            {
                title: 'Name',
                data: 'name',
            },

             {
                title: 'Date',
                data: 'wef_date',
            },
            { title: 'CGST' , data: 'id',
                "render": function( data, type, row, meta) {
                    var str = "";
                    row.types.forEach(function(element) {
                        element.details.forEach(function(ele) {
                            if (ele.name == "cgst") {
                            str = ele.rate;
                            }
                        });
                    });
                    return str;
                }
            },
            { title: 'SGST' ,data: 'id',
                "render": function( data, type, row, meta) {
                        var str = "";
                    row.types.forEach(function(element) {
                        element.details.forEach(function(ele) {
                            if (ele.name == "sgst") {
                            str = ele.rate;
                            }
                        });
                    });
                    return str;
                }
            },
                { title: 'IGST' ,  data: 'id',
                "render": function( data, type, row, meta) {
                    var str = "";
                    row.types.forEach(function(element) {
                        element.details.forEach(function(ele) {
                            if (ele.name == "igst") {
                            str = ele.rate;
                            }
                        });
                    });
                    return str;
                }
            },
                { title: 'CESS' ,  data: 'id',
                "render": function( data, type, row, meta) {
                    var str = "";
                    row.types.forEach(function(element) {
                        element.details.forEach(function(ele) {
                            if (ele.name == "cess") {
                            str = ele.rate;
                            }
                        });
                    });
                    return str;
                }
            },
                { title: 'CESS ON' ,  data: 'id',
                "render": function( data, type, row, meta) {
                        var str = "";
                    row.types.forEach(function(element) {
                        element.details.forEach(function(ele) {
                            if (ele.name == "cess") {
                            if (ele.rate_on == "qty") {
                                str = "Quantity";
                            } else if (ele.rate_on == "amt") {
                                str = "Amount";
                            }
                            }
                        });
                    });
                    return str;
                }
            },
            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['gsts-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                    var str =  '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700  edit-item" aria-hidden="true"  data-item-id=' + data + '></i> Edit </button>';
                    if (canAny(page.props.granted_permissions,['gst-change-rates'])) {
                        str += '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700  change-rate"><i class="mr-2 fas fa-pencil-alt text-slate-700  change-rate" aria-hidden="true"  data-item-id=' + data + '></i> Change Rates </button>';
                    }
                    return str;
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editGst(e.target.dataset.itemId);
            });

            $(".change-rate").unbind('click').on("click", function(e) {
                changeGST(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Gsts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gst
            </h2>
        </template>
        <div>
            <gst-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id"     :change-rate="state.changeRate">
            </gst-form>
            <ListWrapper title="Gsts List">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['gsts-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> New Gst
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="gsts_list" class="row-border stripe" width="100%">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

