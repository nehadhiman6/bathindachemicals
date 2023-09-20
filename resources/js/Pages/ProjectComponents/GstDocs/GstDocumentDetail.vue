<script setup>
    const { base_url,copyProperties,refreshComponent} = globalMixin();
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../globalMixin';
     import InputError from '@/Components/InputError.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import ItemUnitSelect from '@/Pages/ProjectComponents/SelectComponents/ItemUnitSelect.vue';
    import ItemSelect from '@/Pages/ProjectComponents/SelectComponents/ItemSelect.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import BrandSelect from '@/Pages/ProjectComponents/SelectComponents/BrandSelect.vue';

    const props = defineProps(['index','entry','form','createUrl','uId','readonly']);
    const emit = defineEmits(['changeInDetails','updateBill']);


    const data = reactive({
        uniqueId:'',
        accountInitials:[],
        accountSelected:[],
        itemInitials:[],
        itemSelected:[],
        packingInitials:[],
        packingSelected:[],
        brandSelected:[],
        brandInitials:[],
        shortName:'',
        gst:{},
        item:{},
        show:false,
        showItem: false,
        accountShow:false,
    });

    const netDiscount = computed(() => {
        var net_disc = 0;
        if(amount.value > 0){
            let amt = Utilities.round(Utilities.round(props.entry.rate) * Utilities.round(props.entry.qty));
            net_disc = Utilities.round(props.entry.disc_prec * amt / 100);
              if(props.entry.disc_amt >0 ){
                net_disc +=  parseFloat(props.entry.disc_amt);
                // props.entry.net_disc = net_disc;
              }

        }
        return Utilities.round(net_disc);
    });

    const amount = computed(() => {
        let rate_on = props.entry.rate_on == 'Q' ?  props.entry.qty : props.entry.weight;
        var amount = Utilities.round(props.entry.rate * rate_on) - Utilities.round(props.entry.net_disc);
        return Utilities.round(amount);
    });
    onMounted(() => {
        if(props.entry.item){
            data.itemInitials.push({'id': props.entry.item.id, 'text': props.entry.item.item_name});
            data.itemSelected.push(props.entry.item.id);
            data.item = props.entry.item;
            data.itemFocus = false;
            selectedItem();
        }
        if(props.entry.doc_account){
            data.accountInitials.push({
                'id':props.entry.doc_account.id, 'text': props.entry.doc_account.name,
            });
            data.accountSelected.push(props.entry.doc_account.id);

        }
        if(props.entry.gst){
            data.gst = props.entry.gst;
            props.entry.gst_name=data.gst.name;
            setGstDetail();
        }

        if(props.entry.brand){
            data.brandInitials = [{'id':props.entry.brand.id,'text':props.entry.brand.name}];
            data.brandSelected = [props.entry.brand.id];
        }
        if(props.entry.packing){
            data.packingInitials = [{'id':props.entry.packing.id,'text':props.entry.packing.name}];
            data.packingSelected = [props.entry.packing.id];
        }
        refreshComponent(data,'show');
        refreshComponent(data,'showItem');
        refreshComponent(data,'accountShow');
    });


    const updateItem = (id, index, item) =>{
        if(item && item.hsn_code){
            props.entry.hsn_code = item.hsn_code;
            props.entry.item = item;
            props.entry.gst = item.gst;
            props.entry.gst_id = item.gst.id;
            selectedItem();
        }
    }
    // const calc=()=>{
    //   emit('calc');
    // }

    const selectedItem  = () =>{
        if(props.form.ac_id > 0 &&  props.entry.item_id > 0 ){
            axios.get('item/'+ props.entry.item_id + '/details')
            .then(function(response){
                props.entry.hsn_code = response.data.item.hsn_code;
                // props.entry.item_desc = response.data.item.item_desc;
                props.entry.gst_id = response.data.item.gst.id;
                props.entry.gst_name = response.data.item.gst.name;
                props.entry.gst = response.data.item.gst;
                setGstDetail();
                setBillAmount();
            });
        }
    }

    const setBillAmount  = () =>{
        console.log(amount,netDiscount);
        props.entry.amount = amount;
        props.entry.net_disc = netDiscount;
        emit('updateBill',props.index);
    }

    const setGstDetail  = () =>{
        props.entry.gst_details=Utilities.getGstDetails(props.form.doc_date,props.entry.gst,props.form.l_o_type);
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'item_id':
                return props.readonly;
            case 'item_desc':
                return props.readonly;
            case 'packing_id':
                return props.readonly;
            case 'brand_id':
                return props.readonly;
            case 'qty':
                return props.readonly;
            case 'weight':
                return props.readonly;
            case 'rate':
                return props.readonly;
            case 'rate_on':
                return props.readonly;
            case 'disc_prec':
                return props.readonly;
            case 'net_disc':
                return true;
            case 'hsn_code':
                return props.readonly;
            case 'amount':
                return true;
            case 'gst_id':
                return true;
            case 'gst_adj_amt':
                return props.readonly;
            case 'gst_amt':
                return true;
            case 'net_amt':
                return true;
            default:
                return false;
        }
    }

</script>

<template>
    <tr>
        <td v-text="props.index +1">
        </td>
        <td  v-if="data.accountShow" style=" max-width: 400px;">
            <account-select v-if="data.show" :disabled="isDisabled('acid_doc')"
                :key="props.entry.random"
                v-model="props.entry.acid_doc"
                type="gl"
                :index="'account_'+props.entry.random"
                :error="props.form.errors.get('acid_doc') ? true :false"
                :initials="data.accountInitials"
                :selected="data.accountSelected"
            ></account-select>
            <InputError class="mt-1 "  v-if="props.form.errors.get('details.'+props.index+'.acid_doc')" :message="props.form.errors.get('details.'+props.index+'.acid_doc')" />
        </td>
        <td   style=" max-width: 400px;">
            <item-select :key="props.entry.random" v-if="data.show"
                 v-model="props.entry.item_id"
                 :index="'items_'+props.entry.random"
                 :s_p_type="props.form.pur_sale_type"
                 :bill_no="props.form.bill_no"
                 :session="props.form.fyear"
                :initials = "data.itemInitials"
                :selected = "data.itemSelected"
                :disabled ="isDisabled('item_id')"
                @updateItem="updateItem"
                :error="props.form.errors.get('details.'+props.index+'.item_id') ? true :false"
            >
            </item-select>
            <InputError class="mt-1 "  v-if="props.form.errors.get('details.'+props.index+'.item_id')" :message="props.form.errors.get('details.'+props.index+'.item_id')" />
        </td>
        <td>
            <TextInput v-model="props.entry.item_desc" :disabled ="isDisabled('item_desc')" :error="props.form.errors.get('details.'+props.index+'.item_desc') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.item_desc')" :message="props.form.errors.get('details.'+props.index+'.item_desc')" />
        </td>

        <td>
            <brand-select v-if="data.show" :key="props.entry.random" v-model="props.entry.brand_id" :index="'items_unit'+props.entry.random"
                :initials = "data.brandInitials"
                :selected = "data.brandSelected"
                :s_p_type="props.form.pur_sale_type"
                :item_id="props.entry.item_id"
                :bill_no="props.form.bill_no"
                :session="props.form.fyear"
                :disabled ="isDisabled('brand_id')"
                :error="props.form.errors.get('details.'+props.index+'.brand_id') ? true :false"
            >
            </brand-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.brand_id')" :message="props.form.errors.get('invoice_details.'+props.index+'.brand_id')" />
        </td>
         <td>
            <packing-select v-if="data.show" :key="props.entry.random" v-model="props.entry.packing_id" :index="'items_unit'+props.entry.random"
                :initials = "data.packingInitials"
                :selected = "data.packingSelected"
                :s_p_type="props.form.pur_sale_type"
                :item_id="props.entry.item_id"
                :bill_no="props.form.bill_no"
                :session="props.form.fyear"
                :disabled ="isDisabled('packing_id')"
                :error="props.form.errors.get('details.'+props.index+'.packing_id') ? true :false"
            >
            </packing-select>
             <InputError class="mt-1"  v-if="props.form.errors.get('invoice_details.'+props.index+'.packing_id')" :message="props.form.errors.get('invoice_details.'+props.index+'.packing_id')" />
        </td>

        <td style="min-width:100px;max-width: 150px;">
            <TextInput v-model="props.entry.qty" @blur="setBillAmount()" :disabled ="isDisabled('qty')" :error="props.form.errors.get('details.'+props.index+'.qty') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.qty')" :message="props.form.errors.get('details.'+props.index+'.qty')" />
        </td>

         <td style="min-width:100px;max-width: 150px;">
            <TextInput v-model="props.entry.weight" :disabled ="isDisabled('weight')" :error="props.form.errors.get('details.'+props.index+'.weight') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.weight')" :message="props.form.errors.get('details.'+props.index+'.weight')" />
        </td>

        <td style="min-width: 150px;">
            <TextInput v-model="props.entry.rate" @blur="setBillAmount()" :disabled ="isDisabled('rate')" :error="props.form.errors.get('details.'+props.index+'.rate') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.rate')" :message="props.form.errors.get('details.'+props.index+'.rate')" />
        </td>

         <td  style="min-width: 150px;">
           <SelectInput  v-model="props.entry.rate_on" :disabled ="isDisabled('rate_on')" @blur="setBillAmount()" :options="[{'id':'W','text':'Weight'},{'id':'Q','text':'Quantity'}]" :error="props.form.errors.get('details.'+props.index+'.rate_on') ? true :false"> </SelectInput>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.rate_on')" :message="props.form.errors.get('details.'+props.index+'.rate_on')" />
        </td>

        <td  style="min-width: 150px;">
           <TextInput v-model="props.entry.disc_prec" :disabled ="isDisabled('disc_prec')" @blur="setBillAmount()" :error="props.form.errors.get('details.'+props.index+'.disc_prec') ? true :false"/>
        <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.disc_prec')" :message="props.form.errors.get('details.'+props.index+'.disc_prec')" />
        </td>
         <td  style="min-width: 150px;">
           <TextInput v-model="props.entry.disc_amt" @blur="setBillAmount()"  :disabled ="isDisabled('disc_amt')" :error="props.form.errors.get('details.'+props.index+'.disc_amt') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.disc_amt')" :message="props.form.errors.get('details.'+props.index+'.disc_amt')" />
        </td>
         <td  style="min-width: 150px;">
           <TextInput v-model="props.entry.net_disc"  :disabled ="isDisabled('net_disc')" />
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.net_disc')" :message="props.form.errors.get('details.'+props.index+'.net_disc')" />
        </td>
        <td  style="min-width: 150px;">
           <TextInput v-model="props.entry.amount" @blur="setBillAmount()" :disabled ="isDisabled('amount')" :error="props.form.errors.get('details.'+props.index+'.amount') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.amount')" :message="props.form.errors.get('details.'+props.index+'.amount')" />
        </td>
         <!-- <td  style="min-width: 150px;">
           <TextInput v-model="props.entry.gst_adj_amt" :disabled ="isDisabled('gst_adj_amt')" :error="props.form.errors.get('details.'+props.index+'.gst_adj_amt') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.gst_adj_amt')" :message="props.form.errors.get('details.'+props.index+'.gst_adj_amt')" />
        </td> -->
        <td  style="min-width: 150px;">
            <TextInput v-model="props.entry.hsn_code" :disabled ="isDisabled('hsn_code')" :error="props.form.errors.get('details.'+props.index+'.hsn_code') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.hsn_code')" :message="props.form.errors.get('details.'+props.index+'.hsn_code')" />
        </td>
        <td  style="min-width: 150px;">
            <TextInput v-model="props.entry.gst_name" :disabled ="isDisabled('gst_id')" :error="props.form.errors.get('details.'+props.index+'.gst_id') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.gst_id')" :message="props.form.errors.get('details.'+props.index+'.gst_id')" />
        </td>
        <td  style="min-width: 150px;">
            <TextInput v-model="props.entry.gst_adj_amt" :disabled ="isDisabled('gst_adj_amt')" :error="props.form.errors.get('details.'+props.index+'.gst_adj_amt') ? true :false" @blur="setBillAmount()"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.gst_adj_amt')" :message="props.form.errors.get('details.'+props.index+'.gst_adj_amt')" />
        </td>
        <td  style="min-width: 150px;">
            <TextInput v-model="props.entry.gst_amt" :disabled ="isDisabled('gst_amt')" :error="props.form.errors.get('details.'+props.index+'.gst_amt') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.gst_amt')" :message="props.form.errors.get('details.'+props.index+'.gst_amt')" />
        </td>
        <td  style="min-width: 150px;">
            <TextInput v-model="props.entry.net_amt" :disabled ="isDisabled('net_amt')" :error="props.form.errors.get('details.'+props.index+'.net_amt') ? true :false"/>
            <InputError class="mt-1"  v-if="props.form.errors.get('details.'+props.index+'.net_amt')" :message="props.form.errors.get('details.'+props.index+'.net_amt')" />
        </td>
        <td>
             <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i>
        </td>
    </tr>
</template>
