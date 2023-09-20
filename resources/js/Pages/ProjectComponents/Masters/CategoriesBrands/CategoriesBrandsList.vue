

<script setup>
import { ref, onMounted, reactive ,nextTick,computed} from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListWrapper from '@/Pages/CustomComponents/Sections/ListWrapper.vue';
import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
import CategoriesBrandsForm from '@/Pages/ProjectComponents/Masters/CategoriesBrands/CategoriesBrandsForm.vue';
import globalMixin from '../../../../globalMixin';

const { base_url,canAny} = globalMixin();
const page = usePage();
const props = defineProps(['typeable_id','typeable_name','type']);
const state = reactive({
    formOpen: false,
    typeable_id: 0,
    type:'',
});

const title = computed(() => {
    if(props.type == 'category'){
        return 'Party Category\'s Brands'
    }
    return 'Brand\'s Catgeories';
});

const resetForm = () => {
    state.form_id = 0;
    state.formOpen = false;
    state.table.ajax.reload(null, false);
}

const editCategoryBrand = (id) => {
    nextTick(() => {
        state.formOpen = true;
        state.form_id = id;
    });
}

onMounted(() => {
    setTable();
});

const setTable = () => {
    state.table = $('#branches_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searchDelay": 700,
        "ordering": true,
        scrollY: "300px",
        scrollCollapse: false,
        pageLength: 10,
        "ajax": {
            "url": base_url.value+"/brands-categories-list",
            "type": "GET",
            "data":function(c){
                c.typeable_id=props.typeable_id,
                c.type=props.type
            }
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
                title: props.type == 'category' ? 'Brands':'Categories',
                render: function (data, type, row, meta) {
                    return props.type == 'category' ? (row.brand ? row.brand.name:'') : (row.category ? row.category.name :'');
                }
            },

            {
                title: 'Actions',
                orderable: false,
                visible:canAny(page.props.granted_permissions,['party-categories-modify','brand-categories-modify']),
                data: 'id',
                render: function (data, type, row, meta) {
                      return '<button  data-item-id=' + data + ' class="inline-block dark:text-white px-4 py-2.5 mb-0 font-bold text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-normal text-sm ease-in bg-150 hover:-translate-y-px active:opacity-85 bg-x-25 text-slate-700 edit-item"><i class="mr-2 fas fa-pencil-alt text-slate-700" aria-hidden="true"></i> Edit </button>';
                }

            }
        ],
        "drawCallback": function (settings) {
            $(".edit-item").unbind('click').on('click', function (e) {
                state.formOpen = false;
                editCategoryBrand(e.target.dataset.itemId);
            });

        }
    });
}

</script>

<template>
    <AppLayout title="Categories/branches">
        <div>
            <categories-brands-form v-if="state.formOpen" @resetForm="resetForm" :form_id="state.form_id" :typeable_id="props.typeable_id"
             :tyeable_name="props.typeable_name" :title="title" :type="props.type" ></categories-brands-form>

            <ListWrapper :title="props.typeable_name+' (' +title +')'">
                <template #button>
                    <ButtonComp @buttonClicked="state.formOpen = true" type="new" v-if="state.formOpen == false && canAny(page.props.granted_permissions,['party-categories-add','brand-categories-add'])">
                        <i class="fa-solid fa-plus mr-2"></i> Update <span v-text="title"></span>
                    </ButtonComp>
                </template>
                <template #table>
                    <table id="branches_list" width="100%" class="row-border stripe">
                    </table>
                </template>
            </ListWrapper>
        </div>
    </AppLayout>
</template>

