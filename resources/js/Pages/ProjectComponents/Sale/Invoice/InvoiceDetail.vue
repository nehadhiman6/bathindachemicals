<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';


    const props = defineProps(['detail','index','form','close_qty','tcs_liquor','readonly']);
    const emit = defineEmits(['changeInDetails','calc']);


    const data = reactive({
        itemInitials:[],itemSelected:[],
        itemUnitInitials:[],itemUnitSelected:[],
        brandInitials:[],brandSelected:[],
        packingInitials:[],packingSelected:[],secondary_unit:null,
        show:true
    });
    onMounted(() => {
        if(props.detail.item){
            data.itemInitials = [{'id':props.detail.item.id,'text':props.detail.item.item_name}];
            data.itemSelected = [props.detail.item.id];
            if(props.detail.item.is_liqour == 'Y'){
                props.form.is_liqour = 'Y';
            }
            if(props.detail.item.secondary_unit && props.detail.item.secondary_unit.length > 0){
                props.detail.secondary_unit = props.detail.item.secondary_unit[0];
            }
            props.detail.acid_sale=  props.detail.item.sale_ledger_acid;
        }
        if(props.detail.item_unit){
            data.itemUnitInitials = [{'id':props.detail.item_unit.id,'text':props.detail.item_unit.unit_name}];
            data.itemUnitSelected = [props.detail.item_unit.id];
        }
         if(props.detail.brand){
            data.brandInitials = [{'id':props.detail.brand.id,'text':props.detail.brand.name}];
            data.brandSelected = [props.detail.brand.id];
        }
        if(props.detail.packing){
            data.packingInitials = [{'id':props.detail.packing.id,'text':props.detail.packing.name}];
            data.packingSelected = [props.detail.packing.id];
        }
        refreshComponent(data,'show');
          calc();
    });
    const updateItem = (id, index, item) =>{
        if(item && item.hsn_code){
            props.detail.hsn_code = item.hsn_code;
            props.detail.item = item;
            if(item.gst_vat == 'G' && item.gst){
                props.detail.gst = item.gst;
                props.detail.gst_id = item.gst.id;
            }
            else if(item.gst_vat == 'V' && item.vat_cst){
                props.detail.vat_cst = item.vat_cst;
                props.detail.vat_cst_id = item.vat_cst.id;
            }
            props.detail.acid_sale = item.sale_ledger_acid;
        }
        if(item.is_liqour == 'Y'){
            props.form.is_liqour = 'Y';
            props.detail.rate_on = 'Q';
            props.form.tcs_per = props.tcs_liquor;
            getItemFixedRate();
        }
        if(item.secondary_unit){
            props.detail.secondary_unit = item.secondary_unit.length > 0 ? item.secondary_unit[0]:null;
        }
        calc();
    }
    const calc=()=>{
      emit('calc');
    }

    const getItemFixedRate  = () =>{
        if(props.form.is_liqour == 'Y' && props.detail.item_id > 0 && props.detail.packing_id > 0){
            axios.post(base_url.value +'/item-fix-rates',{
                'item_id' : props.detail.item_id,
                'packing_id' : props.detail.packing_id,
            })
            .then(function(response){
                if(response.data.rate){
                    props.detail.rate  = response.data.rate;
                }
                else{
                    props.detail.rate  = '';

                }
                calc();
            })
            .catch(function(error){
                console.log(error);
            });
        }
    }
    // const isDisabled = computed(() =>{
    //     if(props.form.sale_order_id > 0){
    //         return true;
    //     }
    //     return false;
    // });
    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_id':
                if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }else{
                    return false;
                }
            case 'unit_id':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'hsn_code':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'brand_id':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'packing_id':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'qty':
                if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'weight':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'rate_on':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'rate':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'discount':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'freight':
                 if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'packing_cost':
                if(props.readonly == true || props.form.sale_order_id > 0){
                    return true;
                }
                else{
                    return false;
                }
            case 'gst_vat_amount':
                return true;
            case 'net_amount':
                return true;
            case 'remove':
                if(props.readonly == true || props.form.sale_order_id > 0){
                    return false;
                }
            default:
                return false;
        }
    }

</script>

<template>
    <tr>
        <td v-text="props.index +1">
        </td>
        <td  v-if="data.show" style=" max-width: 200px;">
            <item-select :key="props.detail.random" v-model="props.detail.item_id" :index="'items_'+props.detail.random"
                :type="props.form.invoice_type == 'vat_invoice' ? 'V':'G'"
                :initials = "data.itemInitials"
                :selected = "data.itemSelected"
                :disabled ="isDisabled('item_id')"
                @updateItem="updateItem"
                :error="props.form.errors.get('invoice_details.'+props.index+'.item_id') ? true :false"
            >
            </item-select>
            <InputError class="mt-1 "  v-if="props.form.errors.get('invoice_details.'+props.index+'.item_id')" :message="props.form.errors.get('invoice_details.'+props.index+'.item_id')" />
        </td>
         <td  v-if="data.show" style="min-width:150px;max-width: 170px;">
            <item-unit-select :key="props.detail.random" v-model="props.detail.unit_id" :index="'items_unit'+props.detail.random"
                :initials = "data.itemUnitInitials"
                :selected = "data.itemUnitSelected"
                :disabled ="isDisabled('unit_id')"
                :error="props.form.errors.get('invoice_details.'+props.index+'.unit_id') ? true :false"
            >
            </item-unit-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.unit_id')" :message="props.form.errors.get('invoice_details.'+props.index+'.unit_id')" />

        </td>
         <td style="min-width:100px;max-width: 150px;">
            <TextInput v-model="props.detail.hsn_code" :disabled ="isDisabled('hsn_code')" :error="props.form.errors.get('invoice_details.'+props.index+'.hsn_code') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.hsn_code')" :message="props.form.errors.get('invoice_details.'+props.index+'.hsn_code')" />
        </td>
        <td  v-if="data.show" style="min-width:100px;max-width: 150px;">
            <brand-select :key="props.detail.random" v-model="props.detail.brand_id" :index="'items_unit'+props.detail.random"
                :initials = "data.brandInitials"
                :selected = "data.brandSelected"
                :disabled ="isDisabled('brand_id')"
                :error="props.form.errors.get('invoice_details.'+props.index+'.brand_id') ? true :false"
            >
            </brand-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.brand_id')" :message="props.form.errors.get('invoice_details.'+props.index+'.brand_id')" />
        </td>
         <td  v-if="data.show" style="min-width: 120px; max-width: 150px;">
            <packing-select :key="props.detail.random" v-model="props.detail.packing_id" :index="'items_unit'+props.detail.random"
                :initials = "data.packingInitials"
                :selected = "data.packingSelected"
                :disabled ="isDisabled('packing_id')"
                :type="props.form.is_liqour =='Y' ? 'Q' : (props.form.packed_loose =='loose' ? 'L': 'O')"
                @updatePacking="getItemFixedRate"
                :error="props.form.errors.get('invoice_details.'+props.index+'.packing_id') ? true :false"
            >
            </packing-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.packing_id')" :message="props.form.errors.get('invoice_details.'+props.index+'.packing_id')" />
        </td>
        <td style="min-width: 150px;">
            <TextInput v-model="props.detail.qty" :disabled ="isDisabled('qty')"   @blur="calc" :error="props.form.errors.get('invoice_details.'+props.index+'.qty') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.qty')" :message="props.form.errors.get('invoice_details.'+props.index+'.qty')" />
        </td>
         <td  style="min-width: 150px;">
            <TextInput v-model="props.detail.weight" :disabled ="isDisabled('weight')" @blur="calc" :error="props.form.errors.get('invoice_details.'+props.index+'.weight') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.weight')" :message="props.form.errors.get('invoice_details.'+props.index+'.weight')" />
        </td>
         <td  style="min-width: 150px;">
           <SelectInput  v-model="props.detail.rate_on" :disabled ="isDisabled('rate_on')"   @change="calc" :options="[{'id':'W','text':'Weight'},{'id':'Q','text':'Quantity'}]" :error="props.form.errors.get('sale_order_details.'+props.index+'.rate_on') ? true :false"> </SelectInput>
            <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.bargain_price_diff')" :message="props.form.errors.get('invoice_details.'+props.index+'.rate_on')" />
        </td>
        <td  style="min-width: 150px;">
           <TextInput v-model="props.detail.rate" :disabled ="isDisabled('rate')" @blur="calc" :error="props.form.errors.get('invoice_details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.rate')" :message="props.form.errors.get('invoice_details.'+props.index+'.rate')" />
        </td>

        <td  style="min-width: 150px;" v-if="props.form.invoice_type == 'gst_invoice'">
           <TextInput v-model="props.detail.discount" @blur="calc" :disabled ="isDisabled('discount')" :error="props.form.errors.get('invoice_details.'+props.index+'.discount') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.discount')" :message="props.form.errors.get('invoice_details.'+props.index+'.discount')" />
        </td>
         <td  style="min-width: 150px;" v-if="props.form.invoice_type == 'gst_invoice'">
           <TextInput v-model="props.detail.freight" @blur="calc" :disabled ="isDisabled('freight')" :error="props.form.errors.get('invoice_details.'+props.index+'.freight') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.freight')" :message="props.form.errors.get('invoice_details.'+props.index+'.freight')" />
        </td>
         <td  style="min-width: 150px;" v-if="props.form.invoice_type == 'gst_invoice'">
           <TextInput v-model="props.detail.packing_cost" @blur="calc" :disabled ="isDisabled('packing_cost')" :error="props.form.errors.get('invoice_details.'+props.index+'.packing_cost') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.packing_cost')" :message="props.form.errors.get('invoice_details.'+props.index+'.packing_cost')" />
        </td>
         <td  style="min-width: 150px;">
           <TextInput v-model="props.detail.gst_vat_amount" :disabled ="isDisabled('gst_vat_amount')" :error="props.form.errors.get('invoice_details.'+props.index+'.gst_vat_amount') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.gst_vat_amount')" :message="props.form.errors.get('invoice_details.'+props.index+'.gst_vat_amount')" />
        </td>
        <td  style="min-width: 150px;">
            <TextInput v-model="props.detail.net_amount" :disabled ="isDisabled('net_amount')" :error="props.form.errors.get('invoice_details.'+props.index+'.net_amount') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.net_amount')" :message="props.form.errors.get('invoice_details.'+props.index+'.net_amount')" />
        </td>
        <td>
             <i v-if="isDisabled('remove')" class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
