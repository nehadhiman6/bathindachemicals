<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import SaleOrderDetailPack from '@/Pages/ProjectComponents/Sale/SaleOrder/SaleOrderDetailPack.vue';
    const props = defineProps(['detail','index','form','readonly']);
    const emit = defineEmits(['changeInDetails','hidePackingModal','calc']);
    const data = reactive({showModal:false,copy_detail:null});


    onMounted(() => {
        data.showModal = true;
        data.copy_detail = JSON.parse(JSON.stringify(props.detail.sale_order_packs));
    });

    const resetDetails = (type= 'add',index) => {
        props.detail.sale_order_packs = data.copy_detail;
        emit('hidePackingModal');
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            default:
                return false;
        }
    }

</script>

<template>
    <Modal :show="data.showModal" max-width="5xl" @close="resetDetails">
       <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                Item Packing Details
            </div>
          <tableLayout >
            <template #thead>
                <tr>
                    <th>Sr</th>
                    <th>Packing</th>
                    <th>Brand</th>
                    <th>Qty</th>
                    <th>Weight</th>
                    <th>Discount</th>
                    <th>Final Rate</th>
                    <th>Amount</th>
                </tr>
            </template>
            <sale-order-detail-pack v-for="(sale_order_pack,ind) in props.detail.sale_order_packs"
                :key="sale_order_pack.random"
                :pack_detail = "sale_order_pack"
                :pack_index = "ind"
                :form="form"
                :readonly="props.readonly"
                @calc ="emit('calc')"
            ></sale-order-detail-pack>
        </tableLayout>

        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <div class="mt-4">
                <ButtonComp @buttonClicked="emit('hidePackingModal')" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                <ButtonComp @buttonClicked="resetDetails" type="cancel">Cancel</ButtonComp>
            </div>
        </div>

    </Modal>
</template>
