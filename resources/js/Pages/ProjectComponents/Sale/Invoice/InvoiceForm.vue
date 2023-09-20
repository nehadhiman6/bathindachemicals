
<script setup>
    import FormWrapper from '@/Pages/CustomComponents/Sections/FormWrapper.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import DatePicker from '@/Pages/CustomComponents/Forms/DatePicker.vue';
    import AccountSelect from '@/Pages/ProjectComponents/SelectComponents/AccountSelect.vue';
    import IfscSelect from '@/Pages/ProjectComponents/SelectComponents/IfscSelect.vue';
    import PackingSelect from '@/Pages/ProjectComponents/SelectComponents/PackingSelect.vue';
    import BankAccountSelect from '@/Pages/ProjectComponents/SelectComponents/BankAccountSelect.vue';
    import InvoiceDetail from '@/Pages/ProjectComponents/Sale/Invoice/InvoiceDetail.vue';
    import BlDetail  from '@/Pages/ProjectComponents/Sale/Invoice/BlDetail.vue';
    import TableLayout from '@/Pages/CustomComponents/Sections/TableLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import {    ref,  computed,  onMounted, onBeforeMount,   reactive} from 'vue';
    import globalMixin from '../../../../globalMixin';
    import axios from 'axios';
    import { Inertia } from '@inertiajs/inertia';

    const { base_url,copyProperties,refreshComponent} = globalMixin();
    const props = defineProps([
        'form_id',
        'sale_order',
        'branch',
        'credit_limit',
        'balance',
        'readonly',
    ]);
    const emit = defineEmits(['resetForm']);
    const form = reactive( new Form({
            form_id:0,
            vcode:'',
            branch_id:'',
            sale_order_id:'',
            invoice_no:'',
            invoice_no_part:'',
            invoice_date:BCL.today,
            invoice_type:'',
            client_id:'',
            bill_party_id:'',
            ship_party_id:'',
            client_po_no:'',
            client_po_date:'',
            sap_po_no:'',
            sap_po_date:'',
            transport_type:'',
            transport_id:'',
            vehical_no:'',
            gr_lr_no:'',
            gr_lr_date:'',
            permit_no:'',
            permit_date:'',
            excise_pass_no:'',
            excise_pass_date:'',
            l_38_no:'',
            l_38_date:'',
            delivery_terms:'',
            cash_cr:'R',
            supply_type:'B2B',
            d20_no:'',
            d20_date:'',
            remarks:'',
            remarks_2:'',
            benificiary_name:'',
            bank_account_number:'',
            ifsc_id:'',
            bar_code_fees:'',
            freight_per_case:'',
            freight_amount:'',
            court_fee:'',
            reverse_charge_applicable:'N',
            add_excise_per_pl:'',
            add_excise_amount:'',
            less_excise_per_pl:'',
            less_excise_amount:'',
            freight:'',
            export_fee:'',
            gst_id:'',
            vat_cst_id:'',
            gst_vat_amt:'',
            surcharge_amt:'',
            discount_amt:'',
            basic_amount:'',
            tcs_per:'',
            tcs_amount:'',
            round_off:'',
            net_amt:'',
            vessel_name:'',
            packed_loose:'',
            credit_limit:'',
            balance_limit:'',
            is_liqour:'N',
            local_outside:'',
            ethanol_bill:'N',
            ethanol_category:'',
            excise_certificate:'',
            denaturant:'',
            crotonaldehde:'',
            denatonium_saccaride:'',
            denatonium_benzoate:'',
            tanker_seal_no:'',
            uid:Utilities.getRandomNumber(),
            invoice_details:[],
            bl_details:[],
            vat_cst_details:{}
    }));
    const data = reactive({
        create_url:'sale-invoices',
        clientInitials:[],
        clientSelected:[],
        billPartyInitials:[],
        billPartySelected:[],
        shipPartyInitials:[],
        shipPartySelected:[],
        transportInitials:[],
        transportSelected:[],
        bankAccountInitials:[],
        bankAccountSelected:[],
        ifscInitials:[],
        ifscSelected:[],
        show:true,
        showDetail:true,
        showIfsc:true,
        showBlModal:false,
        showBankAccount:true,
        transport_types:[],
        delivery_terms:[],
        tcs_liquor:[]
    });
    const pageTitle = computed(() => props.form_id > 0 ? 'Update  Invoice':'Add Invoice');

    const showBlDetails = computed(() => {
        if(form.invoice_type == 'high_seas_sale'  || form.invoice_type == 'sale_against_bond'){
            return true;
        }
        return false;
    });

    const ethenol_bill = computed(() => {
        var ethenol_bill =false;
        form.invoice_details.forEach(function(detail){
            if(detail.item && detail.item.ethanol_parameters == 'Y'){
                ethenol_bill = true;
                form.ethanol_bill = 'Y';
            }
        });
        if(ethenol_bill == false){
                form.ethanol_bill = 'N';
        }
        return ethenol_bill;
    });

    const updateBankAccount = (id, index,bank_acc) =>{
        if(bank_acc.benificiary_name){
            form.benificiary_name = bank_acc.benificiary_name;
        }
        if(bank_acc.ifsc){
            form.ifsc = bank_acc.ifsc;
            data.ifscInitials = [{'id':bank_acc.ifsc.id ,'text':bank_acc.ifsc.ifsc_code}];
            data.ifscSelected = [bank_acc.ifsc.id];
            refreshComponent(data,'showIfsc');
        }
    }

    onMounted(() => {
        if(props.sale_order){
            setSaleOrderData();
        }
        if(props.form_id > 0){
            getSaleInvoice();
        }
        else if(data.branch){
            form.benificiary_name = data.branch.benificiary_name;
            form.bank_account_number = data.branch.bank_account_number;
            form.ifsc_id = data.branch.ifsc_id;
            if(data.branch.bank_account_number) {
                data.bankAccountInitials = [{'text':data.branch.bank_account_number,'id':data.branch.bank_account_number}];
                data.bankAccountSelected = [data.branch.bank_account_number];
                Utilities.refreshComponent(data,'showBankAccount');
            }
            if(data.branch.ifsc){
                data.ifscInitials = [{'id':data.branch.ifsc.id,'text':data.branch.ifsc.ifsc_code}];
                data.ifscSelected = [data.branch.ifsc.id];
                Utilities.refreshComponent(data,'showIfsc');
            }
        }
        if(form.invoice_details.length == 0){
            form.invoice_details.push(getDetail());
        }
    });

    onBeforeMount(()=>{
        getInitialData();
    })

    const getInitialData = ()=>{
        axios.get(base_url.value+'/initial-data/sale')
        .then(function(response){
            var data = response.data;
            data.transport_types = data['transport_types'];
            data.branch = data['branch']
            data.tcs_liquor = data['tcs_liquor'];
        })
        .catch(function(error){

        })
    }

    const getDetail = () => {
        return {
            id:0,
            invoice_id:0,
            item_id:'',
            unit_id:'',
            brand_id:'',
            packing_id:'',
            hsn_code:'',
            qty:'',
            weight:'',
            rate_on:'W',
            rate:'',
            discount:'',
            freight:'',
            packing_cost:'',
            amt_without_gst:'',
            basic_amount:'',
            final_rate:'',
            gst_id:'',
            vat_cst_id:'',
            gst_vat_amount:'',
            add_less:'',
            surcharge_amount:'',
            net_amount:'',
            acid_sale:0,
            gst:null,
            vat_cst: null,
            item:null,
            packing:null,
            item_unit:null,
            brand:null,
            secondary_unit:null,
            trans_gst_details:[],
            random:Utilities.getRandomNumber()
        }
    }
    const getInvoieBlDetail = () => {
        return {
            id:0,
            invoice_id:0,
            bl_no:'',
            bl_date:'',
            bl_qty:'',
            random:Utilities.getRandomNumber()
        }
    }
    const setSaleOrderData = () =>{
        if(props.sale_order){
            var sale_order = props.sale_order;
            form.sale_order_id = props.sale_order.id;
            Utilities.copyProperties(sale_order, form);
            form.gst_vat_amt = '';
            form.net_amt = '';
            form.surcharge_amt='';
            form.basic_amount='';
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
            // if(sale_order['type'] == 'gst'){
            //     form.invoice_type = 'gst_invoice';
            // }
            // else if(sale_order['type'] == 'vat'){
            //     form.invoice_type = 'vat_invoice';
            // }

            sale_order.item_details.forEach((item_det,sale_order_item_index) => {
                var detail = getDetail();
                console.log()
                if(sale_order_item_index == 0){
                     form.invoice_type = item_det.item  &&  item_det.item.gst_vat =='G'? 'gst_invoice': (
                        item_det.item  &&  item_det.item.gst_vat =='V' ?
                        (sale_order.client.account_yearly.local_outside == 'L'? 'vat_invoice':'retail_invoice')
                        :''
                    );
                }
                // Utilities.copyProperties(item_det,detail);
                detail.item= item_det.item;
                detail.hsn_code = item_det.item.hsn_code;
                detail.qty = item_det.qty;
                detail.weight = item_det.weight;
                detail.brand= item_det.brand;
                detail.rate_on = item_det.rate_on ? item_det.rate_on: 'W';
                detail.rate = item_det.rate ? item_det.rate: 0;
                detail.packing= item_det.packing;
                detail.item_unit= item_det.item_unit;
                detail.unit_id = item_det.item_unit ? item_det.item_unit.id:0;
                detail.item_id = item_det.item ? item_det.item.id:0;
                detail.gst_id= item_det.gst_id;
                detail.gst_vat_id= item_det.gst_vat_id;
                detail.gst= item_det.gst;
                detail.vat_cst= item_det.vat_cst;
                form.invoice_details.push(detail);
            });
            form.credit_limit = props.credit_limit ? props.credit_limit:'';
            form.balance_limit = props.balance ? props.balance:'';
            form.delivery_terms = sale_order.sale_contract.delivery_terms;
            form.sap_po_no = sale_order.sale_contract.sap_po_no;
            form.sap_po_date = sale_order.sale_contract.sap_po_date;
            form.client_po_no = sale_order.sale_contract.cust_po_no;
            form.client_po_date = sale_order.sale_contract.cust_po_date;
            Utilities.refreshComponent(data,'show');
            Utilities.refreshComponent(data,'showDetail');
            form.uid = Utilities.getRandomNumber();
            calc();
        }
    }
    const submitForm = () =>{
        form['postForm'](base_url.value+'/'+data.create_url)
        .then(function(response){
            if(response.success){
                Utilities.showPopMessage("Your data has been saved successfully!")
                if(props.sale_order){
                    Inertia.get(base_url.value+'/sale-invoices')
                }
                else{
                    emit('resetForm');
                }

            }
        })
        .then(function(response){
            data.showDetail = true;
        })
        .catch(function(error){
            Utilities.showPopMessage(error.message,"Invalid Data","error",'6000',true)
        });
    }
    const getSaleInvoice = () =>{
        axios.get(base_url.value+'/sale-invoices/'+props.form_id+'/edit')
        .then(function(response){
            if(response.data.success){
                let sale_invoice = response.data.sale_invoice;
                let credit_limit = response.data.credit_limit;
                let balance_limit = response.data.balance;
                copyProperties(sale_invoice,form);
                if(sale_invoice.client){
                    data.clientInitials = [{'text':sale_invoice.client.name,'id':sale_invoice.client.id}];
                    data.clientSelected = [sale_invoice.client.id];
                }
                if(sale_invoice.bill_party){
                    data.billPartyInitials = [{'text':sale_invoice.bill_party.name,'id':sale_invoice.bill_party.id}];
                    data.billPartySelected = [sale_invoice.bill_party.id];
                }
                if(sale_invoice.ship_party){
                    data.shipPartyInitials = [{'text':sale_invoice.ship_party.name,'id':sale_invoice.ship_party.id}];
                    data.shipPartySelected = [sale_invoice.ship_party.id];
                }
                if(sale_invoice.transport){
                    data.transportInitials = [{'text':sale_invoice.transport.name,'id':sale_invoice.transport.id}];
                    data.transportSelected = [sale_invoice.transport.id];
                }

                if(sale_invoice.account_number){
                    data.bankAccountInitials = [{'text':sale_invoice.account_number.bank_account_number,'id':sale_invoice.account_number.bank_account_number}];
                    data.bankAccountSelected = [sale_invoice.account_number.bank_account_number];
                    if(sale_invoice.account_number.ifsc){
                        data.ifscInitials = [{'text':sale_invoice.account_number.ifsc.ifsc_code,'id':sale_invoice.account_number.ifsc.id}];
                        data.ifscSelected = [sale_invoice.account_number.ifsc.id];
                    }
                }
                form.local_outside = sale_invoice.client && sale_invoice.client.account_yearly ? sale_invoice.client.account_yearly.local_outside:'';
                form.invoice_details = [];
                sale_invoice.invoice_details.forEach(element => {
                    var detail = getDetail();
                    copyProperties(element,detail);
                    detail.item = element.item;
                    detail.brand = element.brand;
                    detail.item_unit = element.item_unit;
                    detail.packing = element.packing;
                    detail.item_id = element.item_id;
                    detail.packing_id = element.packing_id;
                    detail.qty = element.qty;
                    detail.weight = element.weight;
                    detail.rate = element.rate;
                    detail.brand_id = element.brand_id;
                    if(element.item.gst_vat == 'G'){
                        detail.gst = element.gst;
                    }
                    if(element.item.gst_vat == 'V'){
                        detail.vat_cst = element.vat_cst;
                    }
                    form.invoice_details.push(detail);
                });
                if(credit_limit){
                    form.credit_limit =  credit_limit;
                }
                  if(balance_limit){
                    form.balance_limit =  balance_limit;
                }
                refreshComponent(data,'show');
                refreshComponent(data,'showIfsc');
                refreshComponent(data,'showBankAccount');

            }
            form.form_id  = props.form_id;
            form.uid = Utilities.getRandomNumber();
            refreshComponent(data,'showDetail');
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
            form.invoice_details.splice(index,1);
        }
        else{
            form.invoice_details.push(getDetail());
        }
    }
    const changeInBlDetails = (type= 'add',index) => {
        if(type =='remove'){
            form.splice(index,1);
        }
        else{
            form.bl_details.push(getInvoieBlDetail());
        }
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
    const showDetails = () =>{
        form['postForm'](base_url.value+'/sale-orders-detail')
        .then(function(response){
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
                form.credit_limit = response.data.limit;
                form.balance_limit = response.data.balance;
            }
        })
        .catch((error)=>{
            console.log(error);
        })
    }
    const calc1 =()=>{
        var acid_gst_type = 'O';
        form.invoice_details.forEach(invoice_det => {
            if(invoice_det.gst_id > 0){
                var gst_on = Utilities.round(Utilities.round(invoice_det.basic_amount));
                invoice_det.gst_details = Utilities.getGstDetails(form.invoice_date, invoice_det.gst, form.local_outside);
                let arr = Utilities.getTransGstDetails(invoice_det.gst_details, gst_on,0, acid_gst_type);
                invoice_det.trans_gst_details = arr[0];
                invoice_det['gst_vat_amount'] = arr[1];
                // det['net_amt'] = arr[2];
                // self.form.gst_amt += Utilities.round( det.gst_amt );
            }
        });
        //Delivery Terms
    }

    const calc =()=>{
        //DETAIL PACKS CALCULATIONS STARTS
        var tot_amt = 0;
        var gst_vat_amt = 0;
        var surcharge = 0;
        var det_gst_amt = 0;
        var det_amt = 0;
        var total_qty = 0;
        var qty_pl = 0;
        var gst_vat = 'G';
        var gst_on = 0;
        var first_key = -1;
        var acid_gst_type = 'O';
        var vat_cst = null;
        var net_amt_without_round = '';
        form.vat_cst_details=[];
        if(form.invoice_type == 'vat_invoice' || form.invoice_type == 'retail_invoice') {
            gst_vat = 'V';
        }
        form.invoice_details.forEach((invoice_detail,key) => {
            if(!vat_cst && gst_vat == 'V') {
                vat_cst = invoice_detail.vat_cst;
            }
            if(invoice_detail['rate_on'] == 'W'){
                invoice_detail['basic_amount'] = invoice_detail['rate'] * invoice_detail['weight'];
            }
            else{
                invoice_detail['basic_amount'] = invoice_detail['rate'] * invoice_detail['qty'];
            }
            if(first_key == -1 && Utilities.round(invoice_detail['basic_amount']) != 0) {
                first_key = key;
            }
            invoice_detail.amt_without_gst = Utilities.round(invoice_detail.basic_amount)+  Utilities.round(invoice_detail.freight) + Utilities.round(invoice_detail.packing_cost);
            tot_amt += Utilities.round(invoice_detail.amt_without_gst);
            total_qty += invoice_detail['qty'] * 1;
            if(invoice_detail.secondary_unit){
                let item_pl = Utilities.round(invoice_detail.secondary_unit.multiplier * invoice_detail['qty']);
                qty_pl += item_pl*1;
            }
        });

        form.add_excise_amount =  Utilities.round(qty_pl * form.add_excise_per_pl);
        form.less_excise_amount = Utilities.round( qty_pl * form.less_excise_per_pl);
        form.freight_amount = total_qty * form.freight_per_case;
        if(gst_vat == 'G') {
            var total_add_less = Utilities.round(form.discount_amt) +  Utilities.round(form.freight) +  Utilities.round(form.export_fee);
            var left_adjust = total_add_less;
            if(total_add_less != 0 && tot_amt != 0) {
                form.invoice_details.forEach(invoice_detail => {
                    invoice_detail.add_less =  Utilities.round(invoice_detail.amt_without_gst * total_add_less/tot_amt);
                    left_adjust -= invoice_detail.add_less;
                });
            }
            // left_adjust = Utilities.round(left_adjust);
            if(first_key != -1) {
                form.invoice_details[first_key].add_less += Utilities.round(left_adjust);
            }
        }
        form.invoice_details.forEach(invoice_det => {
            // if(left_adjust != 0) {
            //     invoice_detail.add_less += left_adjust;
            //     left_adjust = 0;
            // }
            invoice_det.trans_gst_details = [];
            if(gst_vat == 'G') {
                gst_on = Utilities.round(Utilities.round(invoice_det.amt_without_gst) + Utilities.round(invoice_det.add_less));
                if(invoice_det.gst_id > 0){
                    invoice_det.gst_details = Utilities.getGstDetails(form.invoice_date, invoice_det.gst, form.local_outside);
                    let arr = Utilities.getTransGstDetails(invoice_det.gst_details, gst_on, 0,acid_gst_type);
                    invoice_det.trans_gst_details = arr[0];
                    invoice_det['gst_vat_amount'] = arr[1] ;
                    invoice_det['net_amount'] = arr[2];
                }
                gst_vat_amt += Utilities.round(invoice_det.gst_vat_amount);
            }
            else{
                invoice_det['net_amount'] =Utilities.round(Utilities.round(invoice_det.amt_without_gst) + Utilities.round(invoice_det.add_less));
            }
        });
        form.basic_amount = Utilities.round(tot_amt);
        form.gst_vat_amt = Utilities.round(gst_vat_amt);
        var net_amt = 0;
        net_amt = Utilities.round(Utilities.round(form.basic_amount)+Utilities.round(form.discount_amt)+Utilities.round(form.freight)+Utilities.round(form.freight_amount)+Utilities.round(form.bar_code_fees)+Utilities.round(form.export_fee)+Utilities.round(form.gst_vat_amt)+Utilities.round(form.add_excise_amount));
        //CALCULATE VAT IN CASE OF VAT INVOICE AND LIQOUR ITEM TYPE
        // vat_cst_id, vat_cst, vat_cst_on, vat_cst_rate, vat_cst_amt, surcharge_rate, surcharge_amt, vat_cst_acid, vat_cst_surcharge_acid, created_by, updated_by, created_at, updated_at
        if(gst_vat == 'V'){
            form.vat_cst_details = Utilities.getVatCstDetails(vat_cst,net_amt,form.local_outside,acid_gst_type);

            form.gst_vat_amt = Utilities.round(Utilities.round(form.vat_cst_details['vat_cst_amt']));
            form.surcharge_amt = Utilities.round(Utilities.round(form.vat_cst_details['surcharge_amt']));
            net_amt += Utilities.round(form.gst_vat_amt)+Utilities.round(form.surcharge_amt);
        }
        // VAT CALCULATION END HERE
        net_amt -=Utilities.round(form.less_excise_amount);
        form.tcs_amount = Utilities.round(net_amt*Utilities.round(form.tcs_per)/100);
        net_amt_without_round = Utilities.round(net_amt*1+form.tcs_amount);


        // console.log("net_amt_without_round")
        // console.log(net_amt_without_round)
        var netAmount = parseFloat(net_amt_without_round);
        var roundedNetAmount = Math.round(netAmount);

        var roundOffAmount = roundedNetAmount-netAmount;
        // if(roundedNetAmount > netAmount){
        //     roundOffAmount = roundedNetAmount - netAmount;
        // }
        if (isNaN(netAmount)) {
            netAmount = 0;
        }

        form.round_off = parseFloat(roundOffAmount.toFixed(2));
        form.net_amt = (netAmount *1) + (roundOffAmount*1);

        // var netAmount = parseFloat(net_amt_without_round);
        // var roundedNetAmount = Math.round(netAmount);
        // var roundOffAmount = Math.abs(netAmount - roundedNetAmount);

        // if (isNaN(netAmount)) {
        //     netAmount = 0;
        // }

        // form.round_off = parseFloat(roundOffAmount.toFixed(2));
        // form.net_amt = Math.round(netAmount + roundOffAmount);


        //Delivery Terms
    }

    const checkShow = (field_name) =>{
        if(field_name == 'sap_po_no' || field_name == 'sap_po_date' ||  field_name == 'd20_no' ||
          field_name == 'd20_date' || field_name == 'remarks2'
        ){
            if(data.branch && data.branch.type =='D' ){
                return true;
            }
            else{
                form[field_name] = '';
            }
        }

        else if(field_name == 'excise_pass_no' || field_name == 'excise_pass_date' ||field_name =='l_38_no' || field_name =='l_38_date' || field_name == 'surcharge_amt'){
            if(form.invoice_type == 'vat_invoice'){
                return true;
            }
            else{
                form[field_name] = '';
            }
        }
        return false;
    }

    const showBl = () =>{
        if(form.bl_details.length ==0){
            form.bl_details.push(getInvoieBlDetail());
        }
        data.showBlModal = true;
    }

    // const isDisabled = computed(() =>{
    //     if(form.sale_order_id > 0){
    //         return true;
    //     }
    //     return false;
    // });

    const isDisabled=(value)=>{
        switch(value) {
            case 'button':
                return props.readonly == false ? true:false;
            case 'invoice_date':
                return true
            case 'invoice_no':
                return props.readonly;
            case 'client_id':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'bill_party_id':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'ship_party_id':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'transport_id':
                return props.readonly;
            case 'transport_type':
                return props.readonly;
            case 'vehical_no':
                return props.readonly;
            case 'invoice_type':
                if(props.readonly == true || props.sale_order && (props.sale_order.type == 'gst'|| props.sale_order.type == 'vat') || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'sap_po_no':
                return props.readonly;
            case 'sap_po_date':
                return props.readonly;
            case 'gr_lr_no':
                return props.readonly;
            case 'gr_lr_date':
                return props.readonly
            case 'permit_no':
                return props.readonly
            case 'permit_date':
                return props.readonly;
            case 'excise_pass_no':
                return props.readonly;
            case 'excise_pass_date':
                return props.readonly;
            case 'd20_no':
                return props.readonly;
            case 'd20_date':
                return props.readonly;
            case 'l_38_no':
                return props.readonly;
            case 'l_38_date':
                return props.readonly;
            case 'client_po_no':
                return props.readonly;
            case 'client_po_date':
                return props.readonly;
            case 'cash_cr':
                return props.readonly;
            case 'delivery_terms':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'supply_type':
                return props.readonly;
            case 'bank_account_number':
                return props.readonly;
            case 'benificiary_name':
                return true;
            case 'ifsc_id':
                return true;
            case 'bar_code_fees':
                return props.readonly;
            case 'freight_per_case':
                return props.readonly;
            case 'freight_amount':
                return true;
            case 'freight_per_case':
                return props.readonly;
            case 'court_fee':
                 return props.readonly;
            case 'reverse_charge_applicable':
                return props.readonly;
            case 'add_excise_per_pl':
                return props.readonly;
            case 'add_excise_amount':
                return true;
            case 'less_excise_per_pl':
                return props.readonly;
            case 'less_excise_amount':
                return true;
            case 'vessel_name':
                return props.readonly;
            case 'packed_loose':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'credit_limit':
                return true;
            case 'balance_limit':
                return true;
            case 'ethanol_category':
                return props.readonly;
            case 'denaturant':
                return props.readonly;
            case 'crotonaldehde':
                return props.readonly;
            case 'denatonium_saccaride':
                return props.readonly;
            case 'denatonium_benzoate':
                return props.readonly;
            case 'tanker_seal_no':
                return props.readonly;
            case 'remarks':
                return props.readonly;
            case 'excise_certificate':
                return props.readonly;
            case 'discount_amt':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'freight':
                if(props.readonly == true || form.sale_order_id > 0){
                    return true
                }
                else{
                    return false;
                }
            case 'export_fee':
                return props.readonly;
            case 'basic_amount':
                return true;
            case 'gst_vat_amt':
                return true;
            case 'surcharge_amt':
                return true;
            case 'tcs_per':
                return props.readonly;
            case 'tcs_amount':
                return true
            case 'round_off':
                return true
            case 'net_amt':
                return true
            default:
                return false;
        }
    }

</script>


<template>
<div>
    <FormWrapper :title="pageTitle">
        <Modal :show="data.showBlModal" max-width="4xl">
            <div class="px-6 p-4">
                <div class="text-lg font-medium text-gray-900">
                    BL Details
                </div>
                <div class="flex flex-wrap items-end -mx-3">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" width="100%">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <th  scopescope="col" class="px-6 py-3">#</th>
                            <th scopescope="col" class="px-6 py-3">BL Date</th>
                            <th class="px-6 py-3">BL no</th>
                            <th  scope="col" class="px-6 py-3">BL Qty</th>
                            <th  scope="col" class="px-6 py-3">Action</th>
                        </thead>
                        <tbody>
                            <bl-detail v-for="(bl_detail,ind) in form.bl_details" :key="bl_detail.random"
                                :bl_detail="bl_detail"
                                :form ="form"
                                :index="ind"
                                :readonly="props.readonly"
                                @changeInBlDetails="changeInBlDetails"
                            >
                            </bl-detail>
                        </tbody>
                        <div class="mt-3" v-if="isDisabled('button')">
                            <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInBlDetails">  New</i>
                        </div>
                    </table>
                </div>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <div class="mt-1">
                        <ButtonComp @buttonClicked="data.showBlModal = false" type="save" v-if="isDisabled('button')">Save</ButtonComp>
                        <ButtonComp @buttonClicked="data.showBlModal = false" type="cancel">Cancel</ButtonComp>
                    </div>
                </div>
            </div>
        </Modal>
        <div class="flex flex-wrap items-end -mx-3">
            <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                <div class="mb-1">
                    <InputLabel for="invoice_date" value="Invoice Date" />
                    <date-picker :disabled="isDisabled('invoice_date')" v-model="form.invoice_date" :error="form.errors.get('invoice_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('invoice_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 " v-if="form.form_id >0">
                <div class="mb-1">
                    <InputLabel for="invoice_no" value="Sale Invoice No." />
                    <TextInput v-model="form.invoice_no" :disabled="isDisabled('invoice_no')" type="text" disabled  :error="form.errors.get('invoice_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('invoice_no')" />
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
                    <account-select   v-model="form.transport_id" index="transport_id"  :error="form.errors.get('transport_id') ? true :false"
                        :initials="data.transportInitials"
                        :disabled="isDisabled('transport_id')"
                        :selected="data.transportSelected"></account-select>
                    <InputError class="mt-2" :message="form.errors.get('transport_id')" />
                </div>
            </div>

             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="transport_type" value="Transport Type" />
                    <SelectInput :disabled="isDisabled('transport_type')" v-model="form.transport_type" :options="data.transport_types" :error="form.errors.get('transport_type') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('transport_type')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="vehical_no" value="Vehicle No." />
                    <TextInput :disabled="isDisabled('vehical_no')" v-model="form.vehical_no" type="text" required :error="form.errors.get('vehical_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vehical_no')" />
                </div>
            </div>


            <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6">
                <div class="mb-1">
                    <InputLabel for="invoice_type" value="Invoice type" />
                    <SelectInput   v-model="form.invoice_type" :disabled="isDisabled('invoice_type')"  :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'gst_invoice','text':'GST Invoice'},
                        {'id':'vat_invoice','text':'VAT Invoice'},
                        {'id':'retail_invoice','text':'Retail Invoice'},
                        {'id':'high_seas_sale','text':'High Seas Sale'},
                        {'id':'sale_against_bond','text':'Sale Against Bond'},
                    ]" :error="form.errors.get('invoice_type') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('invoice_type')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('sap_po_no')">
                <div class="mb-1">
                    <InputLabel for="sap_po_no" value="SAP PO No." />
                    <TextInput  v-model="form.sap_po_no" type="text" :disabled="isDisabled('sap_po_no')" required :error="form.errors.get('sap_po_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('sap_po_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('sap_po_date')">
                <div class="mb-1">
                    <InputLabel for="sap_po_date" value="SAP PO Date" />
                    <date-picker class-name="sap_po_date" v-model="form.sap_po_date" :disabled="isDisabled('sap_po_date')" :error="form.errors.get('sap_po_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('sap_po_date')" />
                </div>
            </div>
                  <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="gr_lr_no" value="GR LR No." />
                    <TextInput  v-model="form.gr_lr_no" type="text" :disabled="isDisabled('gr_lr_no')" required :error="form.errors.get('gr_lr_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('gr_lr_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="gr_lr_date" value="GR LR Date" />
                    <date-picker class-name="gr_lr_date" v-model="form.gr_lr_date" :disabled="isDisabled('gr_lr_date')" :error="form.errors.get('gr_lr_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('gr_lr_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="permit_no" value="Permit No." />
                    <TextInput  v-model="form.permit_no" type="text" :disabled="isDisabled('permit_no')" required :error="form.errors.get('permit_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('permit_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="permit_date" value="Permit Date" />
                    <date-picker class-name="permit_date" v-model="form.permit_date" :disabled="isDisabled('permit_date')" :error="form.errors.get('permit_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('permit_date')" />
                </div>
            </div>
               <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('excise_pass_no')">
                <div class="mb-1">
                    <InputLabel for="excise_pass_no" value="Excise Pass No." />
                    <TextInput   v-model="form.excise_pass_no" type="text" required :disabled="isDisabled('excise_pass_no')" :error="form.errors.get('excise_pass_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('excise_pass_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('excise_pass_date')" >
                <div class="mb-1">
                    <InputLabel for="excise_pass_date" value="Excise Pass Date" />
                    <date-picker class-name="excise_pass_date" v-model="form.excise_pass_date" :disabled="isDisabled('excise_pass_date')" :error="form.errors.get('excise_pass_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('excise_pass_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('d20_no')">
                <div class="mb-1">
                    <InputLabel for="d20_no" value="D20 No." />
                    <TextInput   v-model="form.d20_no" type="text" :disabled="isDisabled('d20_no')" required :error="form.errors.get('d20_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('d20_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('d20_date')">
                <div class="mb-1">
                    <InputLabel for="d20_date" value="D20 Date" />
                    <date-picker class-name="d20_date" v-model="form.d20_date" :disabled="isDisabled('d20_date')" :error="form.errors.get('d20_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('d20_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('l_38_no')">
                <div class="mb-1">
                    <InputLabel for="l38_no" value="L38 No." />
                    <TextInput v-model="form.l_38_no" type="text" :disabled="isDisabled('l_38_no')" required :error="form.errors.get('l_38_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('l_38_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="checkShow('l_38_no')">
                <div class="mb-1">
                    <InputLabel for="l38_date" value="L38 Date" />
                    <date-picker class-name="l_38_date" v-model="form.l_38_date" :disabled="isDisabled('l_38_date')" :error="form.errors.get('l_38_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('l_38_date')" />
                </div>
            </div>
                  <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="client_po_no" value="Client PO no." />
                    <TextInput v-model="form.client_po_no" :disabled="isDisabled('client_po_no')" type="text" required :error="form.errors.get('client_po_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('client_po_no')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="client_po_date" value="Client PO Date" />
                    <date-picker class-name="client_po_date" v-model="form.client_po_date" :disabled="isDisabled('client_po_date')" :error="form.errors.get('client_po_date') ? true :false"></date-picker>
                    <InputError class="mt-2" :message="form.errors.get('client_po_date')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/6 lg:w-1/6">
                <div class="mb-1">
                    <InputLabel for="cash_cr" value="Cash/Credit" />
                    <SelectInput   v-model="form.cash_cr" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'C','text':'Cash'},
                        {'id':'R','text':'Credit'},
                    ]" :error="form.errors.get('cash_cr') ? true :false" :disabled="isDisabled('cash_cr')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('cash_cr')" />
                </div>
            </div>
                <div class="w-full max-w-full px-3 md:w-1/4 lg:w-1/4">
                <div class="mb-1">
                    <InputLabel for="delivery_terms" value="Delivery Terms" />
                    <SelectInput  :disabled="isDisabled('delivery_terms')" v-model="form.delivery_terms" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'F','text':'FOR'},
                        {'id':'M','text':'EX-MILL'},
                        {'id':'K','text':'EX-KANDLA'},
                    ]" :error="form.errors.get('delivery_terms') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('delivery_terms')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-1/4 lg:w-1/4" v-if="form.invoice_type == 'gst_invoice'">
                <div class="mb-1">
                    <InputLabel for="supply_type" value="Supply Type" />
                    <SelectInput   v-model="form.supply_type" :disabled="isDisabled('supply_type')" :options="[
                        {'id':'','text':'SELECT'},
                        { 'id':'B2B' , 'text':'B2B'},
                        { 'id':'B2C' , 'text':'B2C'},
                        { 'id':'SEZWP' , 'text':'SEZWP'},
                        { 'id':'SEZWOP' , 'text':'SEZWOP'},
                        { 'id':'EXPWP' , 'text':'EXPWP'},
                        { 'id':'EXPWOP' , 'text':'EXPWOP'},
                        { 'id':'DEXP' , 'text':'DEXP'},
                    ]" :error="form.errors.get('supply_type') ? true :false"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('supply_type')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3 md:w-1/4">
                <div class="mb-1" v-if="data.showBankAccount">
                    <InputLabel for="bank_account_number" value="Bank Account Number" />
                    <bank-account-select v-model="form.bank_account_number" @updateBankAccount="updateBankAccount"
                        :initials="data.bankAccountInitials"
                        :selected="data.bankAccountSelected"
                        :disabled="isDisabled('bank_account_number')"
                      > </bank-account-select>
                    <!-- <TextInput v-model="form.bank_account_number" type="text"  :error="form.errors.get('bank_account_number') ? true :false" /> -->
                    <InputError class="mt-2" :message="form.errors.get('bank_account_number')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3 md:w-1/4">
                <div class="mb-1">
                    <InputLabel for="benificiary_name" value="Benificiary Name" />
                    <TextInput :disabled="isDisabled('benificiary_name')" v-model="form.benificiary_name" type="text"  :error="form.errors.get('benificiary_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('benificiary_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 md:w-1/4">
                 <div class="mb-1" v-if="data.showIfsc">
                    <InputLabel for="ifsc_id" value="Ifsc" />
                    <ifsc-select :disabled="isDisabled('ifsc_id')" v-model="form.ifsc_id" :key="-1" :initials="data.ifscInitials" :selected="data.ifscSelected"></ifsc-select>
                    <InputError class="mt-2" :message="form.errors.get('ifsc_id')" />
                </div>
             </div>
            <div class="w-full max-w-full px-3  md:w-1/4"  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="bar_code_fees" value="Bar Code Fees" />
                    <TextInput :disabled="isDisabled('bar_code_fees')" v-model="form.bar_code_fees" type="text"  :error="form.errors.get('bar_code_fees') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('bar_code_fees')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 "  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="freight_per_case" value="Freight Per Case" />
                    <TextInput :disabled="isDisabled('freight_per_case')" v-model="form.freight_per_case" type="text"  @blur="calc" :error="form.errors.get('freight_per_case') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('freight_per_case')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 "  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="freight_amount" value="Freight Amount" />
                    <TextInput :disabled="isDisabled('freight_amount')" v-model="form.freight_amount" type="text"  :error="form.errors.get('freight_amount') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('freight_amount')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="court_fee" value="Court Fee" />
                    <TextInput  v-model="form.court_fee" :disabled="isDisabled('court_fee')" type="text"  :error="form.errors.get('court_fee') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('court_fee')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " >
                <div class="mb-1">
                    <InputLabel for="reverse_charge_applicable" value="Reverse Charges Applicable" />
                    <SelectInput    v-model="form.reverse_charge_applicable" :options="[
                        {'id':'','text':'SELECT'},
                        {'id':'N','text':'No'},
                        {'id':'Y','text':'Yes'},
                    ]" :error="form.errors.get('reverse_charge_applicable') ? true :false" :disabled="isDisabled('reverse_charge_applicable')"> </SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('reverse_charge_applicable')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 "  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="add_excise_per_pl" value="Add Excise Per PL" />
                    <TextInput @blur="calc" :disabled="isDisabled('add_excise_per_pl')" v-model="form.add_excise_per_pl" type="text"  :error="form.errors.get('add_excise_per_pl') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('add_excise_per_pl')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 "  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="add_excise_amount" value="Add Excise Amount" />
                    <TextInput  v-model="form.add_excise_amount" :disabled="isDisabled('add_excise_amount')" type="text"  :error="form.errors.get('add_excise_amount') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('add_excise_amount')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 "  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="less_excise_per_pl" value="Less Excise Per PL" />
                    <TextInput  @blur="calc" v-model="form.less_excise_per_pl" type="text" :disabled="isDisabled('less_excise_per_pl')"  :error="form.errors.get('less_excise_per_pl') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('less_excise_per_pl')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 "  v-if="form.is_liqour =='Y' && form.invoice_type == 'vat_invoice'">
                <div class="mb-1">
                    <InputLabel for="less_excise_amount" value="Less Excise Amount" />
                    <TextInput  v-model="form.less_excise_amount"  type="text" :disabled="isDisabled('less_excise_amount')" :error="form.errors.get('less_excise_amount') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('less_excise_amount')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="vessel_name" value="Vessel name" />
                    <TextInput :disabled="isDisabled('vessel_name')" v-model="form.vessel_name" type="text"  :error="form.errors.get('vessel_name') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('vessel_name')" />
                </div>
            </div>
            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="packed_loose" value="Packed/Loose" />
                    <SelectInput :disabled="isDisabled('packed_loose')"  v-model="form.packed_loose" :error="form.errors.get('packed_loose')" :options ="[{'id':'packed','text':'Packed'},{'id':'loose','text':'Loose'}]"  ></SelectInput>
                    <InputError class="mt-2" :message="form.errors.get('packed_loose')" />
                </div>
            </div>

            <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 " v-if="showBlDetails">
                <div class="mb-2" >
                    <ButtonComp type="save" size="sm" @click="showBl()" v-if="isDisabled('button')">BL Details</ButtonComp>
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="credit_limit" value="Credit Limit" />
                    <TextInput  v-model="form.credit_limit" type="text" :disabled="isDisabled('credit_limit')"  :error="form.errors.get('credit_limit') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('credit_limit')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/6 lg:w-1/6 ">
                <div class="mb-1">
                    <InputLabel for="balance_limit" value="Balance Limit" />
                    <TextInput  v-model="form.balance_limit" type="text" :disabled="isDisabled('balance_limit')"  :error="form.errors.get('balance_limit') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('balance_limit')" />
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-end -mx-3" v-if="ethenol_bill">
              <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                <div class="mb-1">
                    <InputLabel for="ethanol_category" value="Ethanol Category" />
                    <TextInput  v-model="form.ethanol_category" :disabled="isDisabled('ethanol_category')" type="text"  :error="form.errors.get('ethanol_category') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('ethanol_category')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                <div class="mb-1">
                    <InputLabel for="excise_certificate" value="Excise Certificate" />
                    <TextInput  v-model="form.excise_certificate" type="text" :disabled="isDisabled('excise_certificate')"  :error="form.errors.get('excise_certificate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('excise_certificate')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                <div class="mb-1">
                    <InputLabel for="denaturant" value="Denaturant" />
                    <TextInput  v-model="form.denaturant" type="text" :disabled="isDisabled('denaturant')" :error="form.errors.get('denaturant') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('denaturant')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/4 lg:w-1/4 ">
                <div class="mb-1">
                    <InputLabel for="crotonaldehde" value="Crotonaldehde" />
                    <TextInput  v-model="form.crotonaldehde" type="text" :disabled="isDisabled('crotonaldehde')" :error="form.errors.get('crotonaldehde') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('crotonaldehde')" />
                </div>
            </div>

        </div>
        <div class="flex flex-wrap items-end -mx-3" v-if="ethenol_bill">
              <div class="w-full max-w-full px-3  md:w-1/3 lg:w-1/3 ">
                <div class="mb-1">
                    <InputLabel for="denatonium_saccaride" value="Denatonium Saccaride " />
                    <TextInput  v-model="form.denatonium_saccaride" type="text" :disabled="isDisabled('denatonium_saccaride')" :error="form.errors.get('denatonium_saccaride') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('denatonium_saccaride')" />
                </div>
            </div>
             <div class="w-full max-w-full px-3  md:w-1/3 lg:w-1/3 ">
                <div class="mb-1">
                    <InputLabel for="denatonium_benzoate" value="Denatonium Benzoate " />
                    <TextInput  v-model="form.denatonium_benzoate" type="text" :disabled="isDisabled('denatonium_benzoate')" :error="form.errors.get('denatonium_benzoate') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('denatonium_benzoate')" />
                </div>
            </div>
              <div class="w-full max-w-full px-3  md:w-1/3 lg:w-1/3 ">
                <div class="mb-1">
                    <InputLabel for="tanker_seal_no" value="Tanker Seal No" />
                    <TextInput  v-model="form.tanker_seal_no" type="text" :disabled="isDisabled('tanker_seal_no')" :error="form.errors.get('tanker_seal_no') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('tanker_seal_no')" />
                </div>
            </div>

        </div>
        <div class="flex flex-wrap items-end -mx-3">

             <div class="w-full max-w-full px-3  md:w-full lg:w-full ">
                <div class="mb-1">
                    <InputLabel for="remarks" value="Remarks" />
                    <TextInput  v-model="form.remarks" type="text"  :disabled="isDisabled('remarks')" :error="form.errors.get('remarks') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks')" />
                </div>
            </div>
             <!-- <div class="w-full max-w-full px-3  md:w-full lg:w-full " v-if="checkShow('remarks2')">
                <div class="mb-1">
                    <InputLabel for="remarks2" value="Ethenol Remarks" />
                    <TextInput  v-model="form.remarks2" type="text" :disabled="true"  :error="form.errors.get('remarks2') ? true :false" />
                    <InputError class="mt-2" :message="form.errors.get('remarks2')" />
                </div>
            </div> -->
         </div>
        <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0" v-if="data.showDetail">
            <legend class="text-base rounded font-semibold bg-primary text-white px-3">Sale Invoice Details</legend>
            <TableLayout >
                <template #thead>
                    <tr>
                        <th>Sr</th>
                        <th>Item</th>
                        <th>Item Unit</th>
                        <th>HSN Code</th>
                        <th>Brand</th>
                        <th>Packing</th>
                        <th>Qty</th>
                        <th>Weight</th>
                        <th>Rate On</th>
                        <th>Rate</th>
                        <th v-if="form.invoice_type == 'gst_invoice'">Discount</th>
                        <th v-if="form.invoice_type == 'gst_invoice'">Freight</th>
                        <th v-if="form.invoice_type == 'gst_invoice'">Packing Cost</th>
                        <th>GST Amt</th>
                        <th>Net Amt</th>
                        <th v-if="isDisabled('button')"></th>
                    </tr>
                </template>
                <invoice-detail v-for="(sale_order_det,index) in form.invoice_details" :key="sale_order_det.random"
                    :detail = "sale_order_det"
                    :index = "index"
                    :form="form"
                    :tcs_liquor="data.tcs_liquor"
                    :readonly ="props.readonly"
                    @changeInDetails="changeInDetails"
                    @calc="calc"
                >
                </invoice-detail>


            </TableLayout>
            <div class="mt-3" v-if="form.sale_order_id == 0 || form.sale_order_id == null || isDisabled('button') ">
                <i class="mr-1 fas fa-square-plus text-slate-700 edit-item" aria-hidden="true" @click="changeInDetails">  New</i>
            </div>
            <InputError class="mt-2" :message="form.errors.get('invoice_details')" />
        </fieldset>
         <fieldset class="border rounded bg-gray-50 p-4 mb-1 min-w-0">
                <legend class="text-base rounded font-semibold bg-primary text-white px-3">Amount Details</legend>
                <div class="flex flex-wrap items-end -mx-3">
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="discount_amt" value="Discount" />
                            <TextInput  :disabled ="isDisabled('discount_amt')" v-model="form.discount_amt" @blur="calc" type="text" required :error="form.errors.get('discount_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('discount_amt')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="freight" value="Freight" />
                            <TextInput :disabled ="isDisabled('freight')"  v-model="form.freight"  @blur="calc" type="text" required :error="form.errors.get('freight') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('freight')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="export_fee" value="Export Fee" />
                            <TextInput  v-model="form.export_fee" :disabled ="isDisabled('export_fee')"  @blur="calc" type="text" required :error="form.errors.get('export_fee') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('export_fee')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="basic_amount" value="Basic Amount" />
                            <TextInput   v-model="form.basic_amount" type="text" :disabled ="isDisabled('basic_amount')" required :error="form.errors.get('basic_amount') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('basic_amount')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="gst_vat_amt" value="GST VAT Amount" />
                            <TextInput   v-model="form.gst_vat_amt" :disabled ="isDisabled('gst_vat_amt')" type="text" required :error="form.errors.get('gst_vat_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('gst_vat_amt')" />
                        </div>
                    </div>
                </div>
                    <div class="flex flex-wrap items-end -mx-3" >
                         <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 "  v-if="checkShow('surcharge_amt')">
                        <div class="mb-1">
                            <InputLabel for="surcharge_amt" value="Surcharge" />
                            <TextInput   v-model="form.surcharge_amt" :disabled ="isDisabled('surcharge_amt')" type="text" required :error="form.errors.get('surcharge_amt') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('surcharge_amt')" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="tcs_per" value="TCS %" />
                            <TextInput   v-model="form.tcs_per" type="text" :disabled ="isDisabled('tcs_per')"  @blur="calc" required :error="form.errors.get('tcs_per') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('tcs_per')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="tcs_amount" value="TCS Amount" />
                            <TextInput   v-model="form.tcs_amount" :disabled ="isDisabled('tcs_amount')" type="text" required :error="form.errors.get('tcs_amount') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('tcs_amount')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="round_off" value="Round Off" />
                            <TextInput  @blur="calc" :disabled ="isDisabled('round_off')"   v-model="form.round_off" type="text" required :error="form.errors.get('round_off') ? true :false" />
                            <InputError class="mt-2" :message="form.errors.get('round_off')" />
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3  md:w-1/5 lg:w-1/5 ">
                        <div class="mb-1">
                            <InputLabel for="net_amt" value="Final Net Amount" />
                            <TextInput   v-model="form.net_amt" type="text" :disabled ="isDisabled('net_amt')" :error="form.errors.get('net_amt') ? true :false" />
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
                <ButtonComp @buttonClicked="showDetails" type="save" v-if="isDisabled('button')">Add</ButtonComp>
                <ButtonComp @buttonClicked="emit('resetForm')" type="cancel">Cancel</ButtonComp>
             </div>
            </div>
        </div>
    </FormWrapper>

</div>
</template>

<style>

</style>
