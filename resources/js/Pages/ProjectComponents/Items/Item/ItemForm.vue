
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import IconButton from '@/Pages/CustomComponents/Buttons/IconButton.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemGroupSelect from '@/Pages/ProjectComponents/SelectComponents/ItemGroupSelect.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import StoreSelect from '@/Pages/ProjectComponents/SelectComponents/StoreSelect.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import BranchSelect from '@/Pages/ProjectComponents/SelectComponents/BranchSelect.vue';
    import GstSelect from '@/Pages/ProjectComponents/SelectComponents/GstSelect.vue';
    import VatCstSelect from '@/Pages/ProjectComponents/SelectComponents/VatCstSelect.vue';
    import InputError from '@/Components/InputError.vue';
    import ItemUnitConversion from '@/Pages/ProjectComponents/Items/Item/ItemUnitConversion.vue';
    import {    ref,  computed,  onBeforeMount,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';

    const { base_url,refreshComponent,copyProperties} = globalMixin();
    const props = defineProps(['form_id','readonly']);
    const form = reactive( new Form({
            form_id: 0,
            item_name:'',
            item_code:'',
            main_group_id:0,
            sub_group_id:0,
            other_sub_group_id:0,
            item_unit_id:0,
            store_item:'',
            minimum_level:'',
            maximum_level:'',
            reorder_level:'',
            item_type:'',
            item_type2:'',
            pur_ledger_acid:0,
            sale_ledger_acid:0,
            tsf_pur_ledger_acid:0,
            tsf_sale_ledger_acid:0,
            rebate_acid:0,
            quality_check:'',
            hsn_code:'',
            gst_vat:'',
            gst_id:0,
            vat_cst_id:0,
            vat_rate:'',
            tcs_applicable:'N',
            tcs_acid:0,
            ethanol_parameters:'N',
            brokerage_per_unit:'',
            remarks:'',
            ethenol_parameters_remarks:'',
            active:'Y',
            is_liqour:'N',
            branches:[],
            stores:[],
            is_distillery:'N',
            secondary_unit:[]
    }));

    const getItemSecondaryUnit = ()=>{
        return {
            item_unit_id:0,
            multiplier:'',
            divider:'',
            conversion_factor:'',
            item_unit:null
        }
    }
    const data = reactive({ create_url:'items',
        mainGroupInitials:[],mainGroupSelected:[],subGroupInitials:[], subGroupSelected:[], otherSubGroupInitials:[],
        otherSubGroupSelected:[], itemUnitInitials:[], itemUnitSelected:[], storeInitials:[], storeSelected:[],
        purLedgerAcInitials:[], purLedgerAcSelected:[], purLedgerAcInitials:[], purLedgerAcSelected:[],
        saleLedgerAcInitials:[], saleLedgerAcSelected:[], tsfPurchaseLedgerInitials:[], tsfPurchaseLedgerSelected:[],
        tsfSaleLedgerInitials:[], tsfSaleLedgerSelected:[], rebateAcInitials:[], rebateAcSelected:[], branchInitials:[],
        branchSelected:[], gstInitials:[], gstSelected:[],vatCstInitials:[],vatCstSelected:[],tcsAcountInitials:[],tcsAcountSelected:[],
        show:true,showConversion:false
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update Item ':'Add Item ');
    const emit = defineEmits(['resetForm']);
    onBeforeMount(() => {
        if(props.form_id > 0){
            getItem();
        }
        if(form.secondary_unit.length == 0)
        form.secondary_unit.push(getItemSecondaryUnit());
    });
    const submitForm = () =>{
        form['postForm'](data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
        });
    }
    const getItem = () =>{
        axios.get(base_url.value+'/items/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let item = response.data.item;
                copyProperties(item,form);
                refreshComponent(data,'show');
                setSelectedData(item);
                if(item.secondary_unit.length == 0){
                    form.secondary_unit.push(getItemSecondaryUnit());
                }
            }
            form.form_id  = props.form_id;
        })
        .catch(function(error){
        });
    }

    const updateBranch =(branches)=>{
        form.branches = branches;
        checkDistellery();

    }


    const checkDistellery = ()=>{
        axios.post(base_url.value+'/get-branches',{
            'branches':form.branches
        })
        .then(function(response){
            var branches = response.data.branches;
            form.is_distillery = 'N';
            branches.forEach(branch => {
                if(branch.type == 'D'){
                    form.is_distillery = 'Y';
                }
            });
        })
        .catch(function(error){
        });

    }

    const updateStore =(stores)=>{
        form.stores = stores;
    }
    const setSelectedData = (item) =>{
        try{
            if(item.item_branches){
                item.item_branches.forEach(element => {
                    data.branchInitials = [{'id':element.branch.id,'text':element.branch.name}];
                    data.branchSelected = [element.branch.id];
                });
            }
            if(item.item_stores){
                item.item_stores.forEach(element => {
                    data.storeInitials = [{'id':element.store.id,'text':element.store.name}];
                    data.storeSelected = [element.store.id];
                });
            }
            if(item.main_group){
                data.mainGroupInitials = [{'id':item.main_group.id,'text':item.main_group.name}];
                data.mainGroupSelected = [item.main_group.id];
            }
            if(item.sub_group){
                data.subGroupInitials = [{'id':item.sub_group.id,'text':item.sub_group.name}];
                data.subGroupSelected = [item.sub_group.id];
            }
            if(item.other_sub_group){
                data.otherSubGroupInitials = [{'id':item.other_sub_group.id,'text':item.other_sub_group.name}];
                data.otherSubGroupSelected = [item.other_sub_group.id];
            }
            if(item.item_unit){
                data.itemUnitInitials = [{'id':item.item_unit.id,'text':item.item_unit.unit_name}];
                data.itemUnitSelected = [item.item_unit.id];
            }
            if(item.pur_ledger_account){
                data.purLedgerAcInitials = [{'id':item.pur_ledger_account.id,'text':item.pur_ledger_account.name}];
                data.purLedgerAcSelected = [item.pur_ledger_account.id];
            }
            if(item.sale_ledger_account){
                data.saleLedgerAcInitials = [{'id':item.sale_ledger_account.id,'text':item.sale_ledger_account.name}];
                data.saleLedgerAcSelected = [item.sale_ledger_account.id];
            }
            if(item.tsf_pur_ledger_account){
                data.tsfPurchaseLedgerInitials = [{'id':item.tsf_pur_ledger_account.id,'text':item.tsf_pur_ledger_account.name}];
                data.tsfPurchaseLedgerSelected = [item.tsf_pur_ledger_account.id];
            }
            if(item.tsf_sale_ledger_account){
                data.tsfSaleLedgerInitials = [{'id':item.tsf_sale_ledger_account.id,'text':item.tsf_sale_ledger_account.name}];
                data.tsfSaleLedgerSelected = [item.tsf_sale_ledger_account.id];
            }
            if(item.rebate_account){
                data.rebateAcInitials = [{'id':item.rebate_account.id,'text':item.rebate_account.name}];
                data.rebateAcSelected = [item.rebate_account.id];
            }
             if(item.tcs_account){
                data.tcsAcountInitials = [{'id':item.tcs_account.id,'text':item.tcs_account.name}];
                data.tcsAcountSelected = [item.tcs_account.id];
            }
            if(item.gst){
                data.gstInitials = [{'id':item.gst.id,'text':item.gst.name}];
                data.gstSelected = [item.gst.id];
            }
            if(item.vat_cst){
                data.vatCstInitials = [{'id':item.vat_cst.id,'text':item.vat_cst.name}];
                data.vatCstSelected = [item.vat_cst.id];
            }
        }
        catch(error){
            console.log(error);
        }
        Utilities.refreshComponent(data,'show');
    }

    const hideConversionModal = () =>{
        data.showConversion = false;
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_name':
                return props.readonly;
            case 'item_code':
                return true;
            case 'main_group_id':
                return props.readonly;
            case 'sub_group_id':
                return props.readonly;
            case 'other_sub_group_id':
                return props.readonly;
            case 'item_unit_id':
                return props.readonly;
            case 'store_item':
                return props.readonly;
            case 'stores':
                return props.readonly;
            case 'minimum_level':
                return props.readonly;
            case 'maximum_level':
                return props.readonly;
            case 'reorder_level':
                return props.readonly;
            case 'item_type':
                return props.readonly;
            case 'item_type2':
                return props.readonly;
            case 'pur_ledger_acid':
                return props.readonly;
            case 'sale_ledger_acid':
                return props.readonly;
            case 'tsf_pur_ledger_acid':
                return props.readonly;
            case 'tsf_sale_ledger_acid':
                return props.readonly;
            case 'rebate_acid':
                return props.readonly;
            case 'branches':
                return props.readonly;
            case 'quality_check':
                return props.readonly;
            case 'hsn_code':
                return props.readonly;
            case 'gst_vat':
                return props.readonly;
            case 'gst_id':
                return props.readonly;
            case 'vat_cst_id':
                return props.readonly;
            case 'tcs_applicable':
                return props.readonly;
            case 'tcs_acid':
                return props.readonly;
            case 'is_liqour':
                return  form.is_distillery =='N' && props.readonly;
            case 'remarks':
                return props.readonly;
            case 'brokerage_per_unit':
                return props.readonly;
            case 'ethanol_parameters':
                return props.readonly;
            case 'ethenol_parameters_remarks':
                return props.readonly;
            case 'active':
                return props.readonly;
            default:
                return false;
        }
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
    <item-unit-conversion :index="0" :form="form" v-if="data.showConversion" @submitForm="hideConversionModal"></item-unit-conversion>

        <div class="flex flex-wrap items-end -mx-3">
            <div :class="form.form_id > 0 ? 'w-full max-w-full px-3 shrink-0 md:w-3/4 lg:w-3/4 md:flex-0':'w-full max-w-full px-3 shrink-0 md:w-1/1 lg:w-1/1 md:flex-0'" >
                <div class="mb-1">
                    <InputLabel for="item_name" value="Name" />
                    <TextInput v-model="form.item_name" type="text" required autofocus :disabled="isDisabled('item_name')" :error="form.errors.get('item_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('item_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/4 md:flex-0" v-if="form.form_id > 0">
                <div class="mb-1">
                    <InputLabel for="item_code" value="Item Code" />
                    <TextInput v-model="form.item_code" type="text" required :disabled="isDisabled('item_code')" :error="form.errors.get('item_code') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('item_code')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="main_group_id" value="Main Group" />
                    <item-group-select  v-model="form.main_group_id" :key="-1" index="main_group_id" :initials="data.mainGroupInitials" :selected="data.mainGroupSelected" :disabled="isDisabled('main_group_id')"></item-group-select>
                    <InputError class="mt-2" :message="form.errors.get('main_group_id')" />
                </div>
             </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="sub_group_id" value="Sub Group" />
                    <item-group-select  v-model="form.sub_group_id" type="s1" :key="-1" index="sub_group_id" :initials="data.subGroupInitials" :selected="data.subGroupSelected" :disabled="isDisabled('sub_group_id')"></item-group-select>
                    <InputError class="mt-2" :message="form.errors.get('sub_group_id')" />
                </div>
             </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-1/3 lg:w-1/3 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="other_sub_group_id" value="Other Sub Group" />
                    <item-group-select  v-model="form.other_sub_group_id" :disabled="isDisabled('other_sub_group_id')" type="s2" :key="-1" index="other_sub_group_id" :initials="data.otherSubGroupInitials" :selected="data.otherSubGroupSelected" ></item-group-select>
                    <InputError class="mt-2" :message="form.errors.get('other_sub_group_id')" />
                </div>
             </div>
        </div>
         <div class="flex flex-wrap items-end -mx-3">
              <div class="w-full max-w-full px-3 shrink-0 md:w-2/12 lg:w-2/12 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="item_unit_id" value="Unit" />
                    <item-unit-select  v-model="form.item_unit_id" :key="-1" :disabled="isDisabled('item_unit_id')" index="item_unit_id" :initials="data.itemUnitInitials" :selected="data.itemUnitSelected" ></item-unit-select>
                    <InputError class="mt-2" :message="form.errors.get('item_unit_id')" />
                </div>
             </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/12 lg:w-1/12 md:flex-0">
                 <div class="mb-3" v-if="data.show">
                  <IconButton @buttonClicked="data.showConversion = true" icon="fa fa-plus-square"> Unit</IconButton>
                </div>
             </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-2/12  lg:w-2/12  md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="store_item" value="Store Item" :required="true"/>
                    <SelectInput  v-model="form.store_item" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('store_item') ? true :false" :disabled="isDisabled('store_item')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('store_item')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-7/12 lg:w-7/12 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="stores" value="Store Locations" />
                    <store-select  :multiple="true" @updateStore="updateStore" :key="-1" :disabled="isDisabled('stores')" :initials="data.storeInitials" :selected="data.storeSelected" ></store-select>
                    <InputError class="mt-2" :message="form.errors.get('stores')" />
                </div>
             </div>
         </div>
          <div class="flex flex-wrap items-end -mx-3">
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 lg:w-1/5 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="minimum_level" value="Minimum Level" />
                    <TextInput v-model="form.minimum_level" type="text" required :disabled="isDisabled('minimum_level')" :error="form.errors.get('minimum_level') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('minimum_level')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 lg:w-1/5 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="maximum_level" value="Maximum Level" />
                    <TextInput v-model="form.maximum_level" type="text" required :disabled="isDisabled('maximum_level')" :error="form.errors.get('maximum_level') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('maximum_level')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 lg:w-1/5 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="reorder_level" value="Reorder Level" />
                    <TextInput v-model="form.reorder_level" type="text" required :disabled="isDisabled('reorder_level')" :error="form.errors.get('reorder_level') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('reorder_level')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 lg:w-1/5  md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="item_type" value="Item Type" />
                    <SelectInput  v-model="form.item_type" :options="[
                         {'id':'','text':'Select'},
                        {'id':'stockable','text':'STOCKABLE'},
                        {'id':'service','text':'SERVICE'},
                    ]" :error="form.errors.get('item_type') ? true :false" :disabled="isDisabled('item_type')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('item_type')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-1/5  lg:w-1/5  md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="item_type2" value="Item Type 2" />
                    <SelectInput  v-model="form.item_type2" :options="[
                        {'id':'','text':'Select'},
                        {'id':'goods','text':'GOODS'},
                        {'id':'service','text':'SERVICE'},
                        {'id':'capital','text':'CAPITAL'},
                    ]" :error="form.errors.get('item_type2') ? true :false" :disabled="isDisabled('item_type2')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('item_type2')" />
                </div>
            </div>
               <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="stores" value="Purchase Ledger Account " />
                    <account-select  v-model="form.pur_ledger_acid" type="gl" :disabled="isDisabled('pur_ledger_acid')" :key="-1" index="pur_ledger_acid" :initials="data.purLedgerAcInitials" :selected="data.purLedgerAcSelected" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('pur_ledger_acid')" />
                </div>
             </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="sale_ledger_acid" value="Sale ledger Account" />
                    <account-select  v-model="form.sale_ledger_acid" type="gl" :key="-1" :disabled="isDisabled('sale_ledger_acid')" index="sale_ledger_acid" :initials="data.saleLedgerAcInitials" :selected="data.saleLedgerAcSelected" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('sale_ledger_acid')" />
                </div>
             </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="tsf_pur_ledger_acid" value="Transfer purchase ledger Account" />
                    <account-select  v-model="form.tsf_pur_ledger_acid" type="gl" :key="-1" :disabled="isDisabled('tsf_pur_ledger_acid')" index="tsf_pur_ledger_acid" :initials="data.tsfPurchaseLedgerInitials" :selected="data.tsfPurchaseLedgerSelected" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('tsf_pur_ledger_acid')" />
                </div>
             </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="tsf_sale_ledger_acid" value="Transfer sale ledger Account" />
                    <account-select  v-model="form.tsf_sale_ledger_acid" type="gl" :key="-1" :disabled="isDisabled('tsf_sale_ledger_acid')" index="tsf_sale_ledger_acid" :initials="data.tsfSaleLedgerInitials" :selected="data.tsfSaleLedgerSelected" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('tsf_sale_ledger_acid')" />
                </div>
             </div>
                <div class="w-full max-w-full px-3 shrink-0 md:w-1/2 lg:w-1/2 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="rebate_acid" value="Rebate Account" />
                    <account-select  v-model="form.rebate_acid" type="gl" :key="-1" index="rebate_acid" :disabled="isDisabled('rebate_acid')" :initials="data.rebateAcInitials" :selected="data.rebateAcSelected" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('rebate_acid')" />
                </div>
             </div>
              <div class="w-full max-w-full px-3 shrink-0 md:w-2/2 lg:w-1/2 md:flex-0">
                 <div class="mb-1" v-if="data.show">
                    <InputLabel for="stores" value="Branches" />
                    <branch-select @updateBranch="updateBranch" :key="-1" index="branches" :multiple="true" :disabled="isDisabled('branches')" :initials="data.branchInitials" :selected="data.branchSelected" ></branch-select>
                    <InputError class="mt-2" :message="form.errors.get('branches')" />
                </div>
             </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="quality_check" value="Quality Check" :required="true"/>
                    <SelectInput  v-model="form.quality_check" :options="[
                        {'id':'','text':'Select'},
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('quality_check') ? true :false" :disabled="isDisabled('quality_check')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('quality_check')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="hsn_code" value="HSN CODE" :required="true"/>
                    <TextInput v-model="form.hsn_code" type="text" required :disabled="isDisabled('hsn_code')" :error="form.errors.get('hsn_code') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('hsn_code')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="gst_vat" value="GST/VAT" :required="true"/>
                    <SelectInput  v-model="form.gst_vat" :options="[
                        {'id':'','text':'Select'},
                        {'id':'G','text':'GST'},
                        {'id':'V','text':'VAT'},
                        {'id':'E','text':'EXEMPT'},
                    ]" :error="form.errors.get('gst_vat') ? true :false" :disabled="isDisabled('gst_vat')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('gst_vat')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1"  v-if="form.gst_vat == 'G' && data.show">
                    <InputLabel for="gst_id" value="GST" :required="true"/>
                    <gst-select v-model="form.gst_id" index="gst_id" :disabled="isDisabled('gst_id')" :initials="data.gstInitials" :selected="data.gstSelected"></gst-select>
                    <InputError class="mt-2" :message="form.errors.get('gst_id')" />
                </div>
                  <div class="mb-1" v-if=" form.gst_vat == 'V' && data.show">
                    <InputLabel for="vat_cst_id" value="VAT Rate" :required="true"/>
                    <vat-cst-select v-model="form.vat_cst_id" :disabled="isDisabled('vat_cst_id')" index="vat_cst_id" :initials="data.vatCstInitials" :selected="data.vatCstSelected"></vat-cst-select>
                    <InputError class="mt-2" :message="form.errors.get('vat_cst_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="tcs_applicable" value="TCS Applicable" :required="true"/>
                    <SelectInput  v-model="form.tcs_applicable" :options="[
                        {'id':'','text':'Select'},
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('tcs_applicable') ? true :false" :disabled="isDisabled('tcs_applicable')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('tcs_applicable')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-full md:flex-0" v-if="data.show &&  form.tcs_applicable == 'Y'" >
                <div class="mb-1">
                    <InputLabel for="tcs_acid" value="TCS Account" :required="true"/>
                   <account-select  v-model="form.tcs_acid" type="gl" :key="-1" index="tcs_acid" :disabled="isDisabled('tcs_acid')" :initials="data.tcsAcountInitials" :selected="data.tcsAcountSelected" ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('tcs_acid')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="is_liqour" value="Liqour Item" :required="true"/>
                    <!-- :disabled="form.is_distillery =='N'" -->
                    <SelectInput  v-model="form.is_liqour" :options="[
                        {'id':'','text':'Select'},
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('is_liqour') ? true :false" :disabled="isDisabled('is_liqour')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('is_liqour')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-4/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="remarks" value="Remarks" :required="true"/>
                    <TextInput v-model="form.remarks" type="text" required :disabled="isDisabled('remarks')" :error="form.errors.get('remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="brokerage_per_unit" value="Brokerage per Unit" />
                    <TextInput v-model="form.brokerage_per_unit" type="text" required :disabled="isDisabled('brokerage_per_unit')" :error="form.errors.get('brokerage_per_unit') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('brokerage_per_unit')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="ethanol_parameters" value="Ethanol Partameters" :required="true"/>
                    <SelectInput  v-model="form.ethanol_parameters" :options="[
                        {'id':'Y','text':'Yes'},
                        {'id':'N','text':'No'},
                    ]" :error="form.errors.get('ethanol_parameters') ? true :false" :disabled="isDisabled('ethanol_parameters')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('ethanol_parameters')" />
                </div>
            </div>

              <div class="w-full max-w-full px-3 shrink-0 md:w-4/5 md:flex-0" v-if="form.ethanol_parameters =='Y'">
                <div class="mb-1">
                    <InputLabel for="ethenol_parameters_remarks" value="Ethenol Remarks" :required="true"/>
                    <TextInput v-model="form.ethenol_parameters_remarks" type="text" :disabled="isDisabled('ethenol_parameters_remarks')" required  :error="form.errors.get('ethenol_parameters_remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('ethenol_parameters_remarks')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 shrink-0 md:w-1/5 md:flex-0" >
                <div class="mb-1">
                    <InputLabel for="active" value="Item Status" :required="true"/>
                    <SelectInput  v-model="form.active" :options="[
                        {'id':'Y','text':'Active'},
                        {'id':'N','text':'Inactive'},
                    ]" :error="form.errors.get('active') ? true :false" :disabled="isDisabled('active')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('active')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 shrink-0  md:w-1/3 lg:w-1/4 md:flex-0">
             <div class="mb-1">
                <ButtonComp @buttonClicked="submitForm" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>
</div>
</template>

<style>

</style>
