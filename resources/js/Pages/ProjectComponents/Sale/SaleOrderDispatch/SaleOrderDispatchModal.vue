<script setup>
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import TableComponent from '@/Components/CustomComponents/Sections/TableComponent.vue';
    import { onMounted,onUnmounted ,reactive,computed  } from 'vue';
    const getDestinationDetail =()=>{
        return {
            id:0,
            qty:'',
            destination:'',
            random:Utilities.getRandomNumber()
        }
    }


    const emit = defineEmits(['resetModal'])
    const props = defineProps([
        'detail','form','dest_index','readonly'
    ]);

    const data = reactive({'show':false});

    const packingsWithLeftItems = computed(() => {
        return props.detail.packings.filter(item => {
            return (item.qty >0 || item.left > 0);
        });
    });

    onMounted(()=>{
        data.show = true;
        props.detail.packings.forEach((packing,index) => {
            getLeftQuantity(packing,index);
        });
    });

    onUnmounted(() => {
        data.show = false;
    });

    const getLeftQuantity =(packing,packing_index) =>{
        var left =  packing.total_qty;
        props.form.destinations.forEach((element,dest_index) => {
            left = left - Utilities.round(element.packings[packing_index]['qty']);
        });
        packing.left = Utilities.round(left);
        if(left < 0){
            packing.qty = Utilities.round(packing.qty) + Utilities.round(left);
            packing.left = 0;
            Utilities.showPopMessage('Quantity can not be more than max Quantity',"Cannot exceed ","error",'6000',true)
        }
        return left;
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'qty':
                return props.readonly;
            default:
                return false;
        }
    }

</script>
<template>
    <Modal :show="data.show" max-width="5xl" @close="submitForm()">
       <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                Packings
            </div>
              <TableComponent>
                <template #thead>
                    <thead  class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th  scope="col" class="py-4 px-6">Sr. no.</th>
                            <th  scope="col" class="py-4 px-6">Bill Party</th>
                            <th  scope="col" class="py-4 px-6">Sale order No.</th>
                            <th  scope="col" class="py-4 px-6">Item</th>
                            <th  scope="col" class="py-4 px-6">Packing</th>
                            <th  scope="col" class="py-4 px-6">Brand</th>
                            <th  scope="col" class="py-4 px-6">Qty</th>
                            <th  scope="col" class="py-4 px-6">Left</th>
                        </tr>
                    </thead>
                </template>
                <tr class="bg-white b order-b dark:bg-gray-800 dark:border-gray-700 bg-indigo-500 bg-opacity-25 "  v-for="(packing,index) in packingsWithLeftItems" :key="packing.id" >
                    <td class="py-4 px-6" v-text="index+ 1"> </td>
                    <td class="py-4 px-6" v-text="packing.bill_party"> </td>
                    <td class="py-4 px-6" v-text="packing.sale_order_no"> </td>
                    <td class="py-4 px-6" v-text="packing.item_name"> </td>
                    <td class="py-4 px-6" v-text="packing.packing_name"> </td>
                    <td class="py-4 px-6" v-text="packing.brand_name"> </td>
                    <td class="py-4 px-6">
                          <TextInput v-model="packing.qty" :disabled="isDisabled('qty')" type="text"  @blur="getLeftQuantity(packing,index)"/>
                          <!-- <TextInput v-model="packing.qty" type="text" @blur="checkQty(packing,index)" /> -->
                    </td>
                     <td class="py-4 px-6" v-text="packing.left"> </td>
                </tr>
            </TableComponent>
        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <div class="mt-4">
                <ButtonComp @buttonClicked="emit('resetModal')" type="save" v-if="isDisabled('button')">OK</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetModal')" type="cancel">Cancel</ButtonComp>
            </div>
        </div>

    </Modal>
</template>
