
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import PayTermSelect from '@/Pages/ProjectComponents/SelectComponents/PayTermSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import SaleOrderDetail from '@/Pages/ProjectComponents/Sale/SaleOrder/SaleOrderDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import axios from 'axios';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps(['form_id','transport_types','readonly']);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
            form_id:0,
            sale_order_no:'',
            branch_id:'',
            sale_order_date:BCL.today,
            client_id:0,
            bill_party_id:0,
            ship_party_id:0,
            transport_id:0,
            vehical_no:'',
            transport_type:'',
            discount_amt:'',
            freight_amt:'',
            export_fee:'',
            basic_amount:'',
            gst_vat_amt:'',
            tcs_per:'',
            tcs_amt:'',
            net_amt:'',
            packed_loose:'',
            local_outside:'',
            delivery_terms:'',
            limit:'',
            own_vehicle:'N',
            freight:'',
            dispatch_advice:'N',
            uid:Utilities.getRandomNumber(),
            sale_order_details:[]
    }));
    const data = reactive({
        create_url:'sale-orders',
        clientInitials:[],
        clientSelected:[],
        billPartyInitials:[],
        billPartySelected:[],
        shipPartyInitials:[],
        shipPartySelected:[],
        transportInitials:[],
        transportSelected:[],
        show:true,
        showDetail:false
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Sale Order':'Add Sale Order');

    onMounted(() => {
        if(props.form_id > 0){
            getSaleOrder();
        }
        if(form.sale_order_details.length == 0){
            form.sale_order_details.push(getDetail());
        }
    });

    const getDetail = () => {
        return {
            id:0,
            sale_order_id:0,
            item_id:0,
            unit_id:0,
            packing_id:0,
            pending_qty:'',
            sale_contract_id:0,
            qty:'',
            weight:'',
            rate_on:'',
            rate:'',
            discount:'',
            freight:'',
            packing_cost:'',
            basic_amount:'',
            gst_vat_id:0,
            gst:null,
            vat_cst:null,
            gst_vat_rate:'',
            gst_vat_amount:'',
            net_amount:'',
            foc_item_id:0,
            foc_packing_id:0,
            foc_brand_id:0,
            foc_brand:null,
            foc_qty:'',
            foc_weight:'',
            final_rate:"",
            add_less:"",
            amt_without_gst:'',
            sale_contract:null,
            item:null,
            sale_contract_item:null,
            calcData:[],
            focCalcData:[],
            sale_order_packs:[],
            random:Utilities.getRandomNumber()
        }
    }
    const getSaleOrderPackDetail = () => {
        return {
            id:0,
            sale_order_det_id:0,
            packing_id:0,
            qty:'',
            weight:'',
            discount:'',
            brand_id:0,
            brand_name:"",
            final_rate:"",
            net_rate:"",
            amt_without_gst:"",
            add_less:"",
            packing:null,
            net_amount:0,
            net_amt:0,
            packing_formula:null,
            random:Utilities.getRandomNumber()
        }
    }
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
        .then(function(response){
            console.log(response);
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                emit('resetForm');
            }
        })
        .catch(function(error){
            Utilities.showPopMessage(error.message,"Invalid Data","error",'6000',true)
        });
    }
    const getSaleOrder = () =>{
        axios.get(base_url.value+'/sale-orders/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let sale_order = response.data.sale_order;
                if(sale_order.client){
                    data.clientInitials = [{'text':sale_order.client.name,'id':sale_order.client.id}];
                    data.clientSelected = [sale_order.client.id];
                    form.local_outside = sale_order.client && sale_order.client.account_yearly ? sale_order.client.account_yearly.local_outside:'';
                }
                if(sale_order.bill_party){
                    data.billPartyInitials = [{'text':sale_order.bill_party.name,'id':sale_order.bill_party.id}];
                    data.billPartySelected = [sale_order.bill_party.id];
                }
                if(sale_order.ship_party){
                    data.shipPartyInitials = [{'text':sale_order.ship_party.name,'id':sale_order.ship_party.id}];
                    data.shipPartySelected = [sale_order.ship_party.id];
                }
                if(sale_order.transport){
                    data.transportInitials = [{'text':sale_order.transport.name,'id':sale_order.transport.id}];
                    data.transportSelected = [sale_order.transport.id];
                }
                copyProperties(sale_order,form);
                form.sale_order_details = [];
                sale_order.sale_order_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    detail.item = element.item;
                    detail.unit_id = form.packed_loose == 'packed' ? element.item.item_unit_id:detail.unit_id;
                    detail.sale_contract = element.sale_contract;
                    detail.calcData = element.calcData ? element.calcData :null;
                    detail.focCalcData = element.focCalcData ? element.focCalcData :null;
                    detail.sale_contract_item = element.sale_contract_item ?  element.sale_contract_item :null;
                    detail.packing = element.packing ?  element.packing :null;
                    detail.foc_item = element.foc_item;
                    detail.foc_packing = element.foc_packing;
                    detail.foc_item_id = element.foc_item_id;
                    detail.foc_packing_id = element.foc_packing_id;
                    detail.foc_qty = element.foc_qty;
                    detail.foc_weight = element.foc_weight;
                    detail.foc_brand_id = element.foc_brand_id;
                    detail.foc_brand = element.foc_brand ? element.foc_brand :null;
                    if(element.item.gst_vat == 'G'){
                        detail.gst = element.gst;
                        detail.vat_cst = null;
                    }
                    if(element.item.gst_vat == 'V'){
                        detail.vat_cst = element.vat_cst;
                        detail.gst = null;
                    }
                    if(form.packed_loose == 'packed') {
                        setPackDetails(detail);
                    }
                    element.sale_order_packs.forEach(ele => {
                        detail['sale_order_packs'].forEach(ele1 => {
                            if(ele1.packing_id == ele.packing_id && ele1.brand_id == ele.brand_id) {
                                Utilities.copyProperties(ele,ele1,'N',['id','qty','discount','sale_order_det_id']);
                                ele1.weight =  Utilities.round(ele1.qty*ele1.packing_formula.weight,3);
                                ele1.net_amt = Utilities.round((ele1.qty * ele1.final_rate) + Utilities.round(ele1.discount));
                            }
                        })
                        // var pack_detail = getSaleOrderPackDetail();
                        // copyProperties(ele,pack_detail);
                        // pack_detail.packing_name = ele.packing.name;
                        // pack_detail.brand_name = ele.brand.name;
                        // pack_detail.net_amt =Utilities.round((ele.qty * ele.final_rate) + Utilities.round(ele.discount));
                        // detail['sale_order_packs'].push(pack_detail);
                    });
                    form.sale_order_details.push(detail);
                });
                refreshComponent(data,'show');
                Utilities.scrollToTop();
            }
            form.form_id  = props.form_id;
            form.uid = Utilities.getRandomNumber();
            data.showDetail = true;
        })
        .then(function(){
            calc();
        })
        .catch(function(error){
            console.log(error);
        });
    }

    const changeInDetails = (type= 'add',index) => {
        if(type =='remove'){
            form.sale_order_details.splice(index,1);
        }
        else{
            form.sale_order_details.push(getDetail());
        }
        calc();
    }

    const setPackDetails = (so_detail) =>{
        var sale_order_packs = [];
        console.log('detail');
        console.log(so_detail);
        if(so_detail.calcData && so_detail.calcData['pack_formula']){
            so_detail.calcData['pack_formula'].forEach(element => {
            var detail = getSaleOrderPackDetail();
            detail.packing_id = element.packing_id;
            detail.brand_id = element.brand_id;
            detail.brand_name = element.brand.name;
            detail.packing_name= element.packing.name;
            detail.packing_formula = element;
            if(detail.packing_formula){
                var rate =so_detail.calcData['sale_contract_det'] ? so_detail.calcData['sale_contract_det']['rate']:0;
                var bargain_rate =so_detail.calcData['sale_contract_det'] ? so_detail.calcData['sale_contract_det']['bargain_price_diff']:0;
                var rate_diff = element.packing.rate_diff_applicable == 'Y'? Utilities.getRateDiff(rate,so_detail.calcData['rate_diff']):0;
                var bargain_rate_diff =  Utilities.round(bargain_rate) - Utilities.round(rate_diff) ;
                var final_rate = Utilities.round(rate) + Utilities.round(bargain_rate_diff);
                if(detail.packing_formula.packing_id != so_detail.sale_contract.packing_id){
                    final_rate += Utilities.round(detail.packing_formula.conversion) + Utilities.round(detail.packing_formula.tin_cost) +Utilities.round(detail.packing_formula.extra);
                    final_rate = Utilities.round(final_rate*detail.packing_formula.muliplier/detail.packing_formula.divisor);
                    final_rate += Utilities.round(detail.packing_formula.packing_cost) +  Utilities.round(detail.packing_formula.freight);
                }
                detail.final_rate = Utilities.round(final_rate);
            }
            sale_order_packs.push(detail);
        });
        }
        so_detail['sale_order_packs'] = sale_order_packs;
    }


    const getSaleContractItem = () =>{
        axios.get('party-category-packing/'+form.ac_id)
        .then(function(response){
            if(response.data.packing){
                data.packingInitials = [{'id':response.data.packing.id,'text':response.data.packing.name}];
                data.packingSelected = [response.data.packing.id];
                refreshComponent(data,'showPacking');
                form.packing_id = response.data.packing.id;
            }
        })
        .catch(function(error){
            console.log(error);
        });
    }

    // const isDisabled = () =>{
    //     return data.showDetail;
    // }

    const showDetails = () =>{
        form['postForm'](base_url.value+'/sale-orders-detail')
        .then(function(response){
            console.log("response");
            data.showDetail = true;
        })
        .catch(function(error){
            console.log("catch")
        })
    }

    const updateAccount= (id,index,account) =>{
        // if(account){
        //     form.local_outside = account.local_outside;
        // }
    }

    const updateBillAccount = (id,index,account) =>{
        if(account){
            form.local_outside = account.local_outside;
        }
        axios.get(base_url.value +'/sale-order-client/'+id )
        .then(function(response){
            if(response.data && response.data.limit){
                form.limit = response.data.limit;
            }
        })
        .catch((error)=>{
            console.log(error);
        })
    }

    const calc =()=>{
        console.log("CALC");
        //DETAIL PACKS CALCULATIONS STARTS
        var tot_amt = 0;
        var gst_vat = 0;
        var delivery_terms = [];
        var det_gst_amt = 0;
        var det_amt = 0;
        form.sale_order_details.forEach(sale_order_det => {
            var pending_qty = sale_order_det.calcData && sale_order_det.calcData.sale_contract_det ?
            sale_order_det.calcData.sale_contract_det.qty :0 ;
            console.log("ITEM ID: "+sale_order_det['item']['id']+ " ITEM : " +sale_order_det['item']['item_name'] + "TOTAL QTY" , pending_qty);
            if(sale_order_det && sale_order_det.calcData && sale_order_det.calcData['sale_contract_det'] &&  sale_order_det.calcData['sale_contract_det']['consumed']){
                sale_order_det.calcData['sale_contract_det']['consumed'].forEach(consumed_sale_order => {
                    // console.log("consumed_sale_order");
                    // console.log(consumed_sale_order);
                    let consumed_packs = 0;
                    let term_w_q = consumed_sale_order == 'W' ? 'weight':'qty';
                    console.log(consumed_sale_order);
                    if(consumed_sale_order['sale_order_id'] !=  form.form_id && sale_order_det['item_id'] == consumed_sale_order['item_id']){
                        if(sale_order_det.sale_contract.packed_loose == 'packed') {
                            consumed_sale_order.sale_order_packs.forEach(pack => {
                                consumed_packs +=  Utilities.round(pack[term_w_q]);
                            });
                        } else {
                            consumed_packs = consumed_sale_order['rate_on'] == 'W' ? consumed_sale_order['weight'] :consumed_sale_order['qty'];
                        }
                    }
                    pending_qty = pending_qty - consumed_packs;
                });
            }
            sale_order_det.pending_qty = pending_qty;
            if(sale_order_det.focCalcData && sale_order_det.focCalcData){
                var packing_foc = sale_order_det.focCalcData.find((formul)=>{
                    return  (formul.packing_id == sale_order_det.foc_packing_id && formul.brand_id == sale_order_det.foc_brand_id);
                });
                if(packing_foc){
                     sale_order_det.foc_weight =packing_foc.weight * sale_order_det.foc_qty;
                }
            }
            console.log( JSON.parse(JSON.stringify(sale_order_det)));
            getTransGst(sale_order_det);
            console.log( JSON.parse(JSON.stringify(sale_order_det)));

            det_gst_amt = 0;
            det_amt = 0;
            var gst_terms = sale_order_det.sale_contract && sale_order_det.sale_contract.gst_terms == 'E' ? 'E':'I';
            var div_factor = 100+((gst_terms == 'E') ? 0:sale_order_det.gst_vat_rate);
            if(sale_order_det.sale_contract){
                let term = sale_order_det.sale_contract.delivery_terms == 'F' ? 'For':( sale_order_det.sale_contract.delivery_terms == 'M' ? 'EX-MILL' :( sale_order_det.sale_contract.delivery_terms == 'K'? 'EX-KANDLA':'' ));
                if (!delivery_terms.includes(term)) {
                    delivery_terms.push(term);
                }
            }
            if(form.packed_loose == 'packed'){
                sale_order_det.sale_order_packs.forEach(packing_det => {
                    if(packing_det.packing_formula){
                        var weight =  packing_det.qty * packing_det.packing_formula.weight;
                        packing_det.weight  = weight;
                    }
                    packing_det.add_less =  0;
                    packing_det.net_amt  =  Utilities.round((packing_det.qty * packing_det.final_rate) + Utilities.round(packing_det.discount * packing_det.qty ));
                    packing_det.gst_vat_amount = Utilities.round(packing_det.net_amt * sale_order_det.gst_vat_rate /div_factor);
                    packing_det.amt_without_gst = packing_det.net_amt-1*((gst_terms == 'E') ? 0:Utilities.round(packing_det.gst_vat_amount));
                    packing_det.net_rate = Utilities.round(packing_det.amt_without_gst/packing_det.qty);
                    gst_vat += Utilities.round(packing_det.gst_vat_amount);
                    tot_amt += Utilities.round(packing_det.amt_without_gst);
                    det_gst_amt += Utilities.round(packing_det.gst_vat_amount);
                    det_amt += Utilities.round(packing_det.amt_without_gst);
                    packing_det.net_amount = Utilities.round(packing_det.amt_without_gst*1+packing_det.gst_vat_amount*1);
                    // consumed += Utilities.round(packing_det['qty']);
                    // console.log('consumed', consumed);
                    console.log('packing_det[qty]', packing_det['qty']);
                });
            } else {
                var qty_weight = sale_order_det.rate_on == 'W' ? sale_order_det.weight : sale_order_det.qty;
                sale_order_det.add_less = 0;
                if(Utilities.round(qty_weight) != 0) {
                    sale_order_det.basic_amount = Utilities.round(qty_weight * Utilities.round((Utilities.round(sale_order_det.rate,5)+Utilities.round(sale_order_det.discount))));
                    sale_order_det.gst_vat_amount = Utilities.round(sale_order_det.basic_amount * sale_order_det.gst_vat_rate /div_factor);
                    sale_order_det.amt_without_gst = sale_order_det.basic_amount-1*((gst_terms == 'E') ? 0:Utilities.round(sale_order_det.gst_vat_amount));
                    sale_order_det.amt_without_gst += Utilities.round(sale_order_det.freight) + Utilities.round(sale_order_det.packing_cost);
                    sale_order_det.final_rate = Utilities.round(sale_order_det.amt_without_gst/qty_weight);
                    sale_order_det.gst_vat_amount = Utilities.round(sale_order_det.amt_without_gst * sale_order_det.gst_vat_rate /100);
                    gst_vat += Utilities.round(sale_order_det.gst_vat_amount);
                    tot_amt += Utilities.round(sale_order_det.amt_without_gst);
                    sale_order_det.net_amount = Utilities.round(sale_order_det.amt_without_gst*1+sale_order_det.gst_vat_amount*1);
                    // consumed = sale_order_det['qty'];
                }
            }
            if(form.packed_loose == 'packed'){
                sale_order_det.gst_vat_amount = det_gst_amt;
                sale_order_det.net_amount = det_amt;
            }

        });
        form.delivery_terms = Utilities.joinArrayAsString(delivery_terms);
        var total_add_less = Utilities.round(form.discount_amt) +  Utilities.round(form.freight_amt) +  Utilities.round(form.export_fee);
        var left_adjust = total_add_less;
        if(total_add_less != 0 && tot_amt != 0) {
            gst_vat = 0;
            det_gst_amt = 0;
            if(form.packed_loose == 'packed'){
                form.sale_order_details.forEach(sale_order_det => {
                    sale_order_det.sale_order_packs.forEach(packing_det => {
                        packing_det.add_less =  Utilities.round(packing_det.amt_without_gst * total_add_less/tot_amt);
                        packing_det.gst_vat_amount = Utilities.round((packing_det.amt_without_gst*1+packing_det.add_less*1) * sale_order_det.gst_vat_rate /100);
                        det_gst_amt += Utilities.round(packing_det.gst_vat_amount);
                        left_adjust -= packing_det.add_less;
                        gst_vat += Utilities.round(packing_det.gst_vat_amount);
                        packing_det.net_amount = Utilities.round(packing_det.amt_without_gst*1+packing_det.gst_vat_amount*1);
                    });
                    sale_order_det.gst_vat_amount = det_gst_amt;
                });
                left_adjust = Utilities.round(left_adjust);
                if(left_adjust != 0) {
                    form.sale_order_details.forEach(sale_order_det => {
                        sale_order_det.sale_order_packs.forEach(packing_det => {
                            if(left_adjust != 0) {
                                packing_det.add_less += left_adjust;
                                gst_vat -= Utilities.round(packing_det.gst_vat_amount);
                                det_gst_amt -= Utilities.round(packing_det.gst_vat_amount);
                                packing_det.gst_vat_amount = Utilities.round((packing_det.amt_without_gst*1+packing_det.add_less*1) * sale_order_det.gst_vat_rate /100);
                                det_gst_amt += Utilities.round(packing_det.gst_vat_amount);
                                gst_vat += Utilities.round(packing_det.gst_vat_amount);
                                left_adjust = 0;
                                packing_det.net_amount = Utilities.round(packing_det.amt_without_gst*1+packing_det.gst_vat_amount*1);
                                sale_order_det.gst_vat_amount = det_gst_amt;
                            }
                        });
                    });
                }
            } else {
                form.sale_order_details.forEach(sale_order_det => {
                    sale_order_det.add_less =  Utilities.round(sale_order_det.amt_without_gst * total_add_less/tot_amt);
                    sale_order_det.gst_vat_amount = Utilities.round((sale_order_det.amt_without_gst*1+sale_order_det.add_less*1) * sale_order_det.gst_vat_rate /100);
                    left_adjust -= sale_order_det.add_less;
                    gst_vat += Utilities.round(sale_order_det.gst_vat_amount);
                    sale_order_det.net_amount = Utilities.round(sale_order_det.amt_without_gst*1+sale_order_det.gst_vat_amount*1);
                });
                left_adjust = Utilities.round(left_adjust);
                if(left_adjust != 0) {
                    form.sale_order_details.forEach(sale_order_det => {
                        if(left_adjust != 0) {
                            sale_order_det.add_less += left_adjust;
                            gst_vat -= Utilities.round(sale_order_det.gst_vat_amount);
                            sale_order_det.gst_vat_amount = Utilities.round((sale_order_det.amt_without_gst*1+sale_order_det.add_less*1) * sale_order_det.gst_vat_rate /100);
                            gst_vat += Utilities.round(packing_det.gst_vat_amount);
                            sale_order_det.net_amount = Utilities.round(sale_order_det.amt_without_gst*1+sale_order_det.gst_vat_amount*1);
                            left_adjust = 0;
                        }
                    });
                }
            }
        }
        form.gst_vat_amt = Utilities.round(gst_vat);
        form.basic_amount = Utilities.round(tot_amt);
        var net_amt = 0;
        net_amt = Utilities.round(form.basic_amount)+Utilities.round(form.discount_amt)+Utilities.round(form.freight_amt)+Utilities.round(form.export_fee)+Utilities.round(form.gst_vat_amt);
        form.tcs_amt = Utilities.round(net_amt*Utilities.round(form.tcs_per)/100);
        form.net_amt = Utilities.round(net_amt*1+form.tcs_amt);
        //Delivery Terms
    }

    const getTransGst = (sale_order_det)=>{
        try{
            var gst_rate= 0;
            if(sale_order_det.gst){
                var gst_details = Utilities.getGstDetails(form.sale_order_date,sale_order_det.gst,form.local_outside);
                var gst_rate = Utilities.getTransGstRate(gst_details,sale_order_det.basic_amount);
            }
            else if(sale_order_det.vat_cst){
                var vat_cal = Utilities.getTransVatCstRate(sale_order_det.vat_cst,form.local_outside);
                let rate = vat_cal[0];
                let surcharge = vat_cal[1];
                var gst_rate = Utilities.round(rate*(1+ Utilities.round(surcharge /100)));
            }
            sale_order_det.gst_vat_rate = gst_rate;
            // if(sale_order_det.sale_contract && sale_order_det.sale_contract.gst_terms == 'E'){
            //     sale_order_det.gst_vat_amount = Utilities.round((sale_order_det.basic_amount + Utilities.round(sale_order_det.add_less)) * gst_rate /100);
            //     sale_order_det.amt_without_gst = Utilities.round(sale_order_det.basic_amount);
            //       console.log("amt_without_gst ===== Utilities.round(sale_order_det.basic_amount)");
            //     console.log(Utilities.round(sale_order_det.basic_amount));
            //     sale_order_det.final_rate = Utilities.round(sale_order_det.rate);
            // }
            // else{
            //     sale_order_det.gst_vat_amount = Utilities.round((sale_order_det.basic_amount  + Utilities.round(sale_order_det.add_less)) * gst_rate /(100+gst_rate));
            //     console.log("amt_without_gst ===== Utilities.round(sale_order_det.basic_amount  - sale_order_det.gst_vat_amount)");
            //     console.log(Utilities.round(sale_order_det.basic_amount ),sale_order_det.gst_vat_amount);
            //     sale_order_det.amt_without_gst = Utilities.round( sale_order_det.basic_amount - sale_order_det.gst_vat_amount);
            //     sale_order_det.final_rate = Utilities.round(sale_order_det.amt_without_gst/sale_order_det.rate);
            // }
        }
        catch(error){
            console.log(error);
        }
    }

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'sale_order_date':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'sale_order_no':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'client_id':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'bill_party_id':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'ship_party_id':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'transport_id':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'transport_type':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'vehical_no':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'packed_loose':
                if(props.readonly == true || data.showDetail == true){
                    return true
                }
            case 'dispatch_advice':
                return props.readonly;
            case 'delivery_terms':
                return true;
            case 'own_vehicle':
                return props.readonly;
            case 'freight':
                if(props.readonly == true || form.own_vehicle =='Y'){
                    return true
                }
            case 'delivery_terms':
                return true;
            case 'discount_amt':
                return props.readonly;
            case 'freight_amt':
                return props.readonly;
            case 'export_fee':
                return props.readonly;
            case 'basic_amount':
                return true;
            case 'gst_vat_amt':
                return true;
            case 'tcs_per':
                return props.readonly;
            case 'tcs_amt':
                return true;
            case 'net_amt':
                return true;
            default:
                return false;
        }
    }
</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                <div class="mb-1">
                    <InputLabel for="sale_order_date" value="Sale Order Date" />
                    <date-picker :disabled="isDisabled('sale_order_date')" v-model="form.sale_order_date" :error="form.errors.get('sale_order_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('sale_order_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 " v-if="form.form_id >0">
                <div class="mb-1">
                    <InputLabel for="sale_order_no" value="Sale Order No." />
                    <TextInput v-model="form.sale_order_no" :disabled="isDisabled('sale_order_no')" type="text" disabled  :error="form.errors.get('sale_order_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sale_order_no')" />
                </div>
            </div>
            <div :class="form.form_id >0 ? 'w-full max-w-full px-3  md:w-2/4 lg:w-2/4 ':'w-full max-w-full px-3  md:w-3/4 lg:w-3/4 '">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="client_id" value="Client" />
                    <account-select :disabled="isDisabled('client_id')"    v-model="form.client_id" index="-2" :key="-2" :error="form.errors.get('client_id') ? true :false"
                        :initials="data.clientInitials"
                        :selected="data.clientSelected"
                        @updateAccount = "updateAccount"
                    ></account-select>
                    <InputError class="mt-2" :message="form.errors.get('client_id')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  md:w-1/2 lg:w-1/2 ">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="bill_party_id" value="Bill Party" />
                    <account-select  :disabled="isDisabled('bill_party_id')" v-model="form.bill_party_id" index="bill_party_id"  :error="form.errors.get('bill_party_id') ? true :false"
                        :initials="data.billPartyInitials"
                        :selected="data.billPartySelected"
                        @updateAccount="updateBillAccount"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('bill_party_id')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/2 lg:w-1/2 ">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="ship_party_id" value="Ship Party" />
                    <account-select  :disabled="isDisabled('ship_party_id')" v-model="form.ship_party_id" index="ship_party_id"  :error="form.errors.get('ship_party_id') ? true :false"
                        :initials="data.billPartyInitials"
                        :selected="data.billPartySelected"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('ship_party_id')" />
                </div>
            </div>

        </div>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  md:w-3/6 lg:w-3/6 ">
                <div class="mb-1"  v-if="data.show">
                    <InputLabel for="transport_id" value="Transport" />
                    <account-select  :disabled="isDisabled('transport_id')" v-model="form.transport_id" index="transport_id"  :error="form.errors.get('transport_id') ? true :false"
                        :initials="data.transportInitials"
                        :selected="data.transportSelected"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('transport_id')" />
                </div>
            </div>

             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="transport_type" value="Transport Type" />
                    <SelectInput  :disabled="isDisabled('transport_type')"  v-model="form.transport_type" :options="props.transport_types" :error="form.errors.get('transport_type') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('transport_type')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="vehical_no" value="Vehicle No." />
                    <TextInput  :disabled="isDisabled('vehical_no')" v-model="form.vehical_no" type="text" required :error="form.errors.get('vehical_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vehical_no')" />
                </div>
            </div>


             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 md:flex-0">
                <div class="mb-1">
                    <InputLabel for="packed_loose" value="Packed/Loose" />
                    <SelectInput :disabled="isDisabled('packed_loose')"  v-model="form.packed_loose" :error="form.errors.get('packed_loose')" :options ="[{'id':'packed','text':'Packed'},{'id':'loose','text':'Loose'}]"  ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('packed_loose')" />
                </div>
            </div>
                  <div class="w-full max-w-full px-3 shrink-0 md:w-1/6 lg:w-1/6 md:flex-0" v-if="form.packed_loose =='packed'">
                <div class="mb-1">
                    <InputLabel for="dispatch_advice" value="Dispatch Advise Required" />
                    <SelectInput    v-model="form.dispatch_advice" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'N','text':'No'},
                        {'id':'Y','text':'Yes'},
                    ]" :error="form.errors.get('dispatch_advice') ? true :false" :disabled="isDisabled('dispatch_advice')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('dispatch_advice')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="delivery_terms" value="Limit" />
                    <TextInput  :disabled="isDisabled('delivery_terms')" v-model="form.limit" type="text"  />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="own_vehicle" value="Own Vehicle" />
                    <SelectInput :disabled="isDisabled('own_vehicle')" v-model="form.own_vehicle" @change="form.freight ==''" required :options="[ {'id':'','text':'Select'},{'id':'Y','text':'Yes'},{'id':'N','text':'No'}]" :error="form.errors.get('own_vehicle') ? true :false"></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('own_vehicle')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " >
                <div class="mb-1">
                    <InputLabel for="freight" value="Freight Paid" />
                    <TextInput  :disabled="isDisabled('freight')" v-model="form.freight" type="text" required :error="form.errors.get('freight') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('freight')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-2/6 lg:w-2/6">
                <div class="mb-1">
                    <InputLabel for="delivery_terms" value="Delivery Terms" />
                    <TextInput  :disabled="isDisabled('delivery_terms')" v-model="form.delivery_terms" type="text"  />
                </div>
            </div>
         </div>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0" v-if="data.showDetail">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Sale Order Details</legend>
                <tableLayout >
                    <template #thead>
                        <tr>
                            <th>Sr</th>
                            <th>Item</th>

                            <th>Sale Contract</th>
                            <th v-if="form.packed_loose == 'loose'">Packing</th>
                            <th>Pending Qty/Weight</th>
                            <th v-if="form.packed_loose == 'loose'">Qty</th>
                            <th v-if="form.packed_loose == 'loose'">Weight</th>
                            <th v-if="form.packed_loose == 'loose'">Rate On</th>
                            <th v-if="form.packed_loose == 'loose'">Rate</th>
                            <th v-if="form.packed_loose == 'loose'">Discount Per Unit</th>
                            <th v-if="form.packed_loose == 'loose'">Freight in Total</th>
                            <th v-if="form.packed_loose == 'loose'">Packing Cost in Total</th>
                            <th>GST Amt</th>
                            <th>Net Amt</th>
                            <!-- <th>Free Item</th>
                            <th>Free Item Packing</th>
                            <th>Free Qty</th> -->
                            <th v-if="form.packed_loose == 'packed'">Actions</th>
                            <th v-if="isDisabled('button')"></th>
                        </tr>
                    </template>
                    <sale-order-detail v-for="(sale_order_det,index) in form.sale_order_details" :key="sale_order_det.random"
                        :detail = "sale_order_det"
                        :index = "index"
                        :form="form"
                        :readonly="props.readonly"
                        @changeInDetails="changeInDetails"
                        @calc="calc"
                        @setPackDetails="setPackDetails"
                    >
                    </sale-order-detail>

                </tableLayout>
                <div class="mt-3" v-if="isDisabled('button')">
                    <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
                </div>
                <InputError class="mt-2" :message="form.errors.get('sale_order_details')" />
        </fieldset>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0"  v-if="data.showDetail">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Amount Details</legend>
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="discount_amt" value="Discount" />
                            <TextInput  :disabled="isDisabled('discount_amt')" v-model="form.discount_amt" @blur="calc" type="text" required :error="form.errors.get('discount_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('discount_amt')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="freight_amt" value="Freight" />
                            <TextInput  :disabled="isDisabled('freight_amt')" v-model="form.freight_amt"  @blur="calc" type="text" required :error="form.errors.get('freight_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('freight_amt')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="export_fee" value="Export Fee" />
                            <TextInput :disabled="isDisabled('export_fee')"  v-model="form.export_fee"  @blur="calc" type="text" required :error="form.errors.get('export_fee') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('export_fee')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="basic_amount" value="Basic Amount" />
                            <TextInput  :disabled="isDisabled('basic_amount')" v-model="form.basic_amount" type="text" required :error="form.errors.get('basic_amount') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('basic_amount')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="gst_vat_amt" value="GST VAT Amount" />
                            <TextInput   v-model="form.gst_vat_amt"  :disabled="isDisabled('gst_vat_amt')" type="text" required :error="form.errors.get('gst_vat_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('gst_vat_amt')" />
                        </div>
                    </div>
                </div>
                    <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="tcs_per" value="TCS %" />
                            <TextInput   v-model="form.tcs_per" type="text" :disabled="isDisabled('tcs_per')" @blur="calc" required :error="form.errors.get('tcs_per') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('tcs_per')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="tcs_amt" value="TCS Amount" />
                            <TextInput   v-model="form.tcs_amt" :disabled="isDisabled('tcs_amt')" type="text" required :error="form.errors.get('tcs_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('tcs_amt')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="net_amt" value="Final Net Amount" />
                            <TextInput   v-model="form.net_amt" type="text" :disabled="isDisabled('net_amt')" :error="form.errors.get('net_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('net_amt')" />
                        </div>
                    </div>
                </div>
        </fieldset>


        <div class="flex flex-wrap items-end -mx-3 mt-3" v-if="data.showDetail">
           <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4 ">
             <div class="mb-1">
                <ButtonComp @buttonClicked="submitForm" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3 mt-3" v-else>
           <div class="w-full max-w-full px-3  lg:w-1/4 md:w-1/4 ">
             <div class="mb-1">
                <ButtonComp @buttonClicked="showDetails" type="save">Add</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
