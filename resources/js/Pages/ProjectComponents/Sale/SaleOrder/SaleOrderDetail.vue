<script setup>
    import globalMixin from '../../../../globalMixin';
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,onBeforeMount,    reactive} from 'vue';
    import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';
    import SaleOrderDetailPacking from '@/Pages/ProjectComponents/Sale/SaleOrder/SaleOrderDetailPacking.vue';
    import SaleContractSelect from '@/Pages/ProjectComponents/SelectComponents/SaleContractSelect.vue';
      import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    const props = defineProps(['detail','index','form','close_qty','readonly']);
    const emit = defineEmits(['changeInDetails','calc','setPackDetails']);
    // const getSaleOrderPackDetail = () => {
    //     return {
    //         id:0,
    //         sale_order_det_id:0,
    //         packing_id:0,
    //         qty:'',
    //         weight:'',
    //         discount:'',
    //         brand_id:0,
    //         brand_name:"",
    //         packing_name:"",
    //         final_rate:"",
    //         packing:null,
    //         net_amt:0,
    //         packing_formula:null,
    //         random:Utilities.getRandomNumber()
    //     }
    // }
    const data = reactive({ freeBrandInitials:[], freeBrandSelected:[], showBrandFoc:true,showPackingFoc:true,showPackingSelect:true,itemInitials:[],itemSelected:[],freeItemInitials:[],freeItemSelected:[],freePackingInitials:[],freePackingSelected:[],saleContractInitials:[],saleContractSelected:[],show:true,showItem:true,showPacking:false,showFree:false,showFreeModal:false,
    packingInitials:[],packingSelected:[]});
    const updateItem = (id,index,item)=>{
        data.saleContractInitials =[];
        data.saleContractSelected =[];
        if(item){
            props.detail.item = item;
            props.detail.gst_vat_id =  item.gst_vat != 'E' ? ( item.gst_vat == 'G'? item.gst_id : item.vat_cst_id):0;
            props.detail.gst = item.gst_vat == 'G' ?  item.gst :null;
            props.detail.vat_cst = item.gst_vat == 'V'  ?  item.vat_cst :null;
            if(props.form.packed_loose == 'packed') {
                props.detail.unit_id = item.item_unit_id;
            }
        }

        refreshComponent(data,'show');
    }
    const updateSaleContract = (id,index,sale_contract)=>{
        props.detail.sale_contract = sale_contract;
        if(props.form.packed_loose == 'loose') {
            props.detail.unit_id = sale_contract.unit_id;
        }
        getPacksRate();
    }
    const setContractItemData = (sale_contract_item)=>{
        props.detail.sale_contract_item  = sale_contract_item;
        if(props.form.packed_loose == 'loose'){
            props.detail.rate = sale_contract_item.rate;
        }
    }

    onBeforeMount(() => {
        if(props.detail.item){
            data.itemInitials = [{'id':props.detail.item.id,'text':props.detail.item.item_name}];
            data.itemSelected = [props.detail.item.id];
        }
        if(props.detail.sale_contract){
            data.saleContractInitials = [{'id':props.detail.sale_contract.id,'text':props.detail.sale_contract.contract_no}];
            data.saleContractSelected = [props.detail.sale_contract.id];
        }
         if(props.detail.foc_item){
            data.freeItemInitials = [{'id':props.detail.foc_item.id,'text':props.detail.foc_item.item_name}];
            data.freeItemSelected = [props.detail.foc_item.id];
        }
         if(props.detail.foc_packing){
            data.freePackingInitials = [{'id':props.detail.foc_packing.id,'text':props.detail.foc_packing.name}];
            data.freePackingSelected = [props.detail.foc_packing.id];
        }
        if(props.detail.packing){
            data.packingInitials = [{'id':props.detail.packing.id,'text':props.detail.packing.name}];
            data.packingSelected = [props.detail.packing.id];
        }
        if(props.detail.foc_brand){
            data.freeBrandInitials = [{'id':props.detail.foc_brand.id,'text':props.detail.foc_brand.name}];
            data.freeBrandSelected  = [props.detail.foc_brand.id];
        }
        refreshComponent(data,'show');
        refreshComponent(data,'showPackingSelect');
        refreshComponent(data,'showItem');
        refreshComponent(data,'showFree');

    });

    const getDisabled = () =>{
        return props.form.packed_loose == 'packed';
    }
    const hidePackingModal=()=>{
        // getTransGst();
        data.showPacking = false;
        calc();
    }

    const getPacksRate = () =>{
        if(props.detail.item_id > 0 && props.detail.sale_contract_id){
            axios.post(base_url.value +'/sale-order-packs-rate',{
                'sale_order_date':props.form.sale_order_date,
                'item_id':props.detail.item_id,
                'sale_contract_id':props.detail.sale_contract_id,
                'bill_party_id': props.form.bill_party_id
            })
            .then(function(response){
                props.detail.calcData = response.data[0];
                setContractItemData(response.data[1]);
                setPackDetails();
                calc();
            })
            .catch(function(error){
                console.log(error);
                if(error.response && error.response.data &&  error.response.data.message){
                    Utilities.showPopMessage(error.response.data.message,'Error','error',5000,true);
                }
            });
        }
    }

    const includeOnly = (type="packing_id") =>{
        var ids = [];
        if(props.detail.focCalcData){
            props.detail.focCalcData.forEach(packing_formula => {
                if(packing_formula){
                    if(type == 'brand_id'){
                        if(packing_formula.packing_id == props.detail.foc_packing_id){
                            ids.push(packing_formula[type]);
                        }
                    }
                    else{
                        ids.push(packing_formula[type]);
                    }
                }
            });
        }
        return ids;
    }

    const setPackDetails = () =>{
        emit('setPackDetails',props.detail);
        // props.detail.sale_order_packs = [];
        // props.detail.calcData['pack_formula'].forEach(element => {
        //     var detail = getSaleOrderPackDetail();
        //     detail.packing_id = element.packing_id;
        //     detail.brand_id = element.brand_id;
        //     detail.brand_name = element.brand.name;
        //     detail.packing_name= element.packing.name;
        //     detail.packing_formula = element;
        //     props.detail.sale_order_packs.push(detail);

        //     if(detail.packing_formula){
        //         var rate =props.detail.calcData['sale_contract_det']?props.detail.calcData['sale_contract_det']['rate']:0;
        //         var bargain_rate =props.detail.calcData['sale_contract_det'] ?props.detail.calcData['sale_contract_det']['bargain_price_diff']:0;
        //         var rate_diff = element.packing.rate_diff_applicable == 'Y'? Utilities.getRateDiff(rate,props.detail.calcData['rate_diff']):0;
        //         var bargain_rate_diff =  Utilities.round(bargain_rate) - Utilities.round(rate_diff) ;
        //         var final_rate = Utilities.round(rate) + Utilities.round(bargain_rate_diff);
        //         if(detail.packing_formula.packing_id != props.detail.sale_contract.packing_id){
        //             final_rate += Utilities.round(detail.packing_formula.conversion) + Utilities.round(detail.packing_formula.tin_cost) +Utilities.round(detail.packing_formula.extra);
        //             final_rate = Utilities.round(final_rate*detail.packing_formula.muliplier/detail.packing_formula.divisor);
        //             final_rate += Utilities.round(detail.packing_formula.packing_cost) +  Utilities.round(detail.packing_formula.freight);
        //         }
        //         detail.final_rate = Utilities.round(final_rate);
        //     }
        // });
    }

    const calc=()=>{
      emit('calc');
    }


   const updateFocItem = (id,index,item) =>{
        if(item ){
            props.detail.foc_item= item;
            if(props.detail.foc_item){
                data.freeItemInitials = [{'id':props.detail.foc_item.id,'text':props.detail.foc_item.item_name}];
                data.freeItemSelected = [props.detail.foc_item.id];
            }
            data.freePackingInitials = [];
            data.freePackingSelected = [];
            data.freeBrandInitials = [];
            data.freeBrandSelected = [];
            refreshComponent(data,'showBrandFoc');
            refreshComponent(data,'showPackingFoc');
            getFocCalData();
            calc();
        }
    }

    const getFocCalData = () =>{
        axios.post(base_url.value +'/sale-order-foc-packings',{
            'item_id':props.detail.foc_item_id,
            'sale_contract_id':props.detail.sale_contract_id,
        })
        .then(function(response){
            if( response.data &&  response.data.packing_formulas)
            props.detail.focCalcData = response.data.packing_formulas;

        })
        .then(function(){
            props.detail.packing_id = 0;
            refreshComponent(data,'showPackingFoc')
        })
        .catch(function(error){
            if(error.response && error.response.data &&  error.response.data.message){
                Utilities.showPopMessage(error.response.data.message,'Error','error',5000,true);
            }
        });
    }


    const focUpdatePacking = (id,index,packing_data) =>{
        if(packing_data){
            props.detail.foc_packing = packing_data;
            data.freePackingInitials = [{'id':packing_data.id,'text':packing_data.name}];
            data.freePackingSelected = [packing_data.id];
        }
        props.detail.foc_brand_id = 0;
        data.freeBrandInitials = [];
        data.freeBrandSelected = [];
        calc();
        refreshComponent(data,'showBrandFoc')
    }

  const updateBrand = (id,index,brand) =>{
        if(brand){
            props.detail.foc_brand = brand;
            data.freeBrandInitials = [{'id':brand.id,'text':brand.name}];
            data.freeBrandSelected = [brand.id];
        }
        calc();
    }



    const showFocModal =()=>{
        data.showFreeModal = true;
        refreshComponent(data,'showFree');
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_id':
                return props.readonly;
            case 'sale_contract_id':
                return props.readonly;
            case 'packing_id':
                return props.readonly;
            case 'qty':
                if(props.readonly == true || getDisabled() == true){
                    return true
                }
            case 'weight':
                if(props.readonly == true || getDisabled() == true){
                    return true
                }
            case 'rate_on':
                return props.readonly;
            case 'rate':
                return true
            case 'discount':
                return props.readonly;
            case 'freight':
                return props.readonly;
            case 'packing_cost':
                return props.readonly;
            case 'gst_vat_rate':
                return true;
            // case 'packing_cost':
            //     return props.readonly;
            // case 'gst_vat_rate':
            //     return true;
            case 'foc_item_id':
                return props.readonly;
            case 'foc_packing_id':
                return props.readonly;
            case 'foc_brand_id':
                return props.readonly;
            case 'foc_qty':
                return props.readonly;
            case 'foc_weight':
                return true;
            default:
                return false;
        }
    }
</script>
// props.detail.foc_weight  = weight;
<template>
    <sale-order-detail-packing v-if="data.showPacking"
        :detail="props.detail"
        :index="props.index"
        :form="props.form"
        :readonly="props.readonly"
        @hidePackingModal="hidePackingModal"
        @calc="calc">
    </sale-order-detail-packing>
    <Modal :show="data.showFreeModal" max-width="2xl">
        <div class="px-6 p-4">
            <div class="text-lg font-medium text-gray-900">
                Free Item Details
            </div>

            <div class="flex flex-wrap items-end -mx-3">
                <div class="w-full max-w-full px-3 md:w-full lg:w-1/1">
                    <div class="mb-1">
                        <InputLabel for="item" value="Free Item" />
                        <item-select :key="props.detail.random+'_foc_item'" v-model="props.detail.foc_item_id" :index="'free_items_'+props.detail.random"
                            :initials = "data.freeItemInitials"
                            :selected = "data.freeItemSelected"
                            :disabled="isDisabled('foc_item_id')"
                            @updateItem="updateFocItem"
                            :error="form.errors.get('sale_order_details.'+props.index+'.foc_item_id') ? true :false"
                        ></item-select>
                        <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_item_id')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_item_id')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 md:w-full lg:w-full">
                    <div class="mb-1" v-if="data.showPackingFoc">
                        <InputLabel for="foc_packing_id" value="Free Packing" />
                        <packing-select :key="props.detail.random+'_foc_packing'" v-model="props.detail.foc_packing_id" :index="'reepacking_'+props.detail.random"
                            :initials = "data.freePackingInitials"
                            :selected = "data.freePackingSelected"
                            :include-only = "includeOnly('packing_id')"
                            :disabled="isDisabled('foc_packing_id')"
                            :strict-include ="true"
                            @updatePacking= "focUpdatePacking"
                            :error="form.errors.get('sale_order_details.'+props.index+'.foc_packing_id') ? true :false"
                        ></packing-select>
                        <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_packing_id')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_packing_id')" />
                    </div>
                </div>

                <div class="w-full max-w-full px-3 md:w-full lg:w-full">
                    <div class="mb-1" v-if="data.showBrandFoc">
                        <InputLabel for="foc_brand_id" value="Brand Name" />
                        <brand-select :index="'foc_brand_id'+props.detail.random"
                            v-model="props.detail.foc_brand_id"
                            :initials = "data.freeBrandInitials"
                            :selected = "data.freeBrandSelected"
                            :disabled="isDisabled('foc_brand_id')"
                            :include-only = "includeOnly('brand_id')"
                            :strict-include ="true"
                            @updateBrands = "updateBrand"
                        ></brand-select>
                        <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_brand_id')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_brand_id')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2">
                    <div class="mb-1">
                        <InputLabel for="foc_qty" value="Qty" />
                        <TextInput v-model="props.detail.foc_qty" :disabled="isDisabled('foc_qty')" @blur="calc" :error="form.errors.get('sale_order_details.'+props.index+'.foc_qty') ? true :false" />
                        <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_qty')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_qty')" />
                    </div>
                </div>
                <div class="w-full max-w-full px-3 md:w-1/2 lg:w-1/2">
                    <div class="mb-1">
                        <InputLabel for="foc_weight" value="Weight" />
                        <TextInput v-model="props.detail.foc_weight" :disabled="isDisabled('foc_weight')" :error="form.errors.get('sale_order_details.'+props.index+'.foc_weight') ? true :false" />
                        <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_weight')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_weight')" />
                    </div>
                </div>
            </div>
            <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                <div class="mt-1">
                    <ButtonComp @buttonClicked="data.showFreeModal = false" type="save">Save</ButtonComp>
                    <ButtonComp @buttonClicked="data.showFreeModal = false" type="cancel">Cancel</ButtonComp>
                </div>
            </div>
        </div>
    </Modal>
    <tr>
        <td >
            <span v-text="props.index +1"></span>
        </td>
         <td  v-if="data.showItem" style=" max-width: 150px;">
            <item-select  :key="props.detail.random" v-model="props.detail.item_id" :index="'items_'+props.detail.random"
                :initials = "data.itemInitials"
                :selected = "data.itemSelected"
                :disabled="isDisabled('item_id')"
                url ="sale-contract-items/filtered"
                :error="form.errors.get('sale_order_details.'+props.index+'.item_id') ? true :false"
                @updateItem = "updateItem"
            ></item-select>
            <InputError class="mt-1 "  v-if="form.errors.get('sale_order_details.'+props.index+'.item_id')" :message="form.errors.get('sale_order_details.'+props.index+'.item_id')" />
        </td>
        <td  v-if="data.show">
            <TextInput v-if="!props.detail.item_id > 0" disabled placeholder="SELECT Item FIRST"></TextInput>
            <sale-contract-select v-else :key="props.detail.random" v-model="props.detail.sale_contract_id" :index="'sale_contract'+props.detail.random"
                :initials = "data.saleContractInitials"
                :selected = "data.saleContractSelected"
                :packed_loose = 'form.packed_loose'
                :disabled="isDisabled('sale_contract_id')"
                :url ="'items-sale-contracts/filtered/'+props.detail.item_id+'/'+props.form.client_id"
                :error="form.errors.get('sale_order_details.'+props.index+'.sale_contract_id') ? true :false"
                @updateSaleContract="updateSaleContract"
            >
            </sale-contract-select>
            <InputError class="mt-1 "  v-if="form.errors.get('sale_order_details.'+props.index+'.sale_contract_id')" :message="form.errors.get('sale_order_details.'+props.index+'.sale_contract_id')" />
        </td>
         <td v-if="props.form.packed_loose == 'loose' && data.showPackingSelect">
            <packing-select :key="props.detail.random+'_packing'" v-model="props.detail.packing_id" :index="'packing_'+props.detail.random"
                url="packings/filtered/L"
                :initials = "data.packingInitials"
                :selected = "data.packingSelected"
                :disabled="isDisabled('packing_id')"
                :error="form.errors.get('sale_order_details.'+props.index+'.packing_id') ? true :false"
            ></packing-select>
         </td>
        <td>
            <TextInput v-model="props.detail.pending_qty" :disabled="true"/>
        </td>
        <td style=" min-width: 120px;" v-if="!getDisabled()">
            <TextInput v-model="props.detail.qty" @blur="calc" :disabled="isDisabled('qty')" :error="form.errors.get('sale_order_details.'+props.index+'.qty') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.qty')" :message="form.errors.get('sale_order_details.'+props.index+'.qty')" />
        </td>

         <td style=" min-width: 120px;"  v-if="!getDisabled()">
            <TextInput v-model="props.detail.weight" @blur="calc" :disabled="isDisabled('weight')"  :error="form.errors.get('sale_order_details.'+props.index+'.weight') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.weight')" :message="form.errors.get('sale_order_details.'+props.index+'.weight')" />
        </td>
          <td style=" min-width: 120px;" v-if="!getDisabled()">
            <SelectInput  v-model="props.detail.rate_on"   @change="calc" :disabled="isDisabled('rate_on')" :options="[{'id':'W','text':'Weight'},{'id':'Q','text':'Quantity'}]" :error="form.errors.get('sale_order_details.'+props.index+'.rate_on') ? true :false"> </SelectInput>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.rate_on')" :message="form.errors.get('sale_order_details.'+props.index+'.rate_on')" />
        </td>
        <td style=" min-width: 120px;"  v-if="!getDisabled()">
            <TextInput v-model="props.detail.rate"  @blur="calc" :disabled="isDisabled('rate')" :error="form.errors.get('sale_order_details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.rate')" :message="form.errors.get('sale_order_details.'+props.index+'.rate')" />
        </td>
         <td style=" min-width: 120px;"  v-if="!getDisabled()">
            <TextInput v-model="props.detail.discount" @blur="calc" :disabled="isDisabled('discount')" :error="form.errors.get('sale_order_details.'+props.index+'.discount') ? true :false"/>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.discount')" :message="form.errors.get('sale_order_details.'+props.index+'.discount')" />
        </td>
        <td style=" min-width: 120px;"  v-if="!getDisabled()">
           <TextInput v-model="props.detail.freight" :disabled="isDisabled('freight')" :error="form.errors.get('sale_order_details.'+props.index+'.freight') ? true :false" />
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.freight')" :message="form.errors.get('sale_order_details.'+props.index+'.freight')" />
        </td>
        <td style=" min-width: 120px;"  v-if="!getDisabled()">
           <TextInput v-model="props.detail.packing_cost" @blur="calc" :disabled="isDisabled('packing_cost')" :error="form.errors.get('sale_order_details.'+props.index+'.packing_cost') ? true :false" />
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.packing_cost')" :message="form.errors.get('sale_order_details.'+props.index+'.packing_cost')" />
        </td>
         <td style=" min-width: 120px;">
           <TextInput v-model="props.detail.gst_vat_amount"  :disabled="isDisabled('gst_vat_rate')" :error="form.errors.get('sale_order_details.'+props.index+'.gst_vat_rate') ? true :false" />
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.gst_vat_rate')" :message="form.errors.get('sale_order_details.'+props.index+'.gst_vat_rate')" />
        </td>
        <td style=" min-width: 120px;">
           <TextInput v-model="props.detail.net_amount" :disabled="isDisabled('gst_vat_rate')" :error="form.errors.get('sale_order_details.'+props.index+'.net_amt') ? true :false" />
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.net_amt')" :message="form.errors.get('sale_order_details.'+props.index+'.net_amt')" />
        </td>
        <!-- <td v-if="data.showFree">
            <item-select :key="props.detail.random+'_foc_item'" v-model="props.detail.foc_item_id" :index="'free_items_'+props.detail.random"
                :initials = "data.freeItemInitials"
                :selected = "data.freeItemSelected"
                :error="form.errors.get('sale_order_details.'+props.index+'.foc_item_id') ? true :false"
            ></item-select>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_item_id')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_item_id')" />
        </td>
        <td v-if="data.showFree">
            <packing-select :key="props.detail.random+'_foc_packing'" v-model="props.detail.foc_packing_id" :index="'reepacking_'+props.detail.random"
                :initials = "data.freePackingInitials"
                :selected = "data.freePackingSelected"
                :error="form.errors.get('sale_order_details.'+props.index+'.foc_packing_id') ? true :false"
            ></packing-select>
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_packing_id')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_packing_id')" />
        </td>
        <td style=" min-width: 120px;">
            <TextInput v-model="props.detail.foc_qty" :error="form.errors.get('sale_order_details.'+props.index+'.foc_qty') ? true :false" />
            <InputError class="mt-1"  v-if="form.errors.get('sale_order_details.'+props.index+'.foc_qty')" :message="form.errors.get('sale_order_details.'+props.index+'.foc_qty')" />
        </td> -->
        <td v-if="form.packed_loose == 'packed'">
             <ButtonComp v-if="props.detail.item_id> 0 &&  props.detail.sale_contract_id >0" @buttonClicked="data.showPacking= true" type="save" size="sm">Packings</ButtonComp>
             <span class="ml-1" >
                <ButtonComp v-if="props.detail.item_id> 0 &&  props.detail.sale_contract_id >0" @buttonClicked="showFocModal()" type="save" size="sm">Free <span v-if="props.detail.foc_item_id > 0" >(1)</span></ButtonComp>
             </span>

         </td>
        <td>
            <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)" v-if="isDisabled('button')"></i>
        </td>
    </tr>
</template>
