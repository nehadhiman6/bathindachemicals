import accounting from 'accounting';

class Utilities {
    static round(number, places = 2) {
        return accounting.toFixed(number, places) * 1;
    }

     static roundString(number, places = 2) {
        return accounting.toFixed(number, places);
     }


    static roundDecimals(value, decimals) {
        return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
    }
    static formatNumber(number, places = 2, curr_sym = "") {
        return accounting.formatMoney(Utilities.round(number), curr_sym, places, "", ".");
    }

    static convertNullOrBlankToZero(val){
        return !val || val =='' ? 0 : val ;
    }

    static currencyNumber(number) {
        var n = Utilities.round(number);
        return n.toLocaleString('en-IN',{
            minimumFractionDigits:2
        });
    }

    static getDrCr(amount,positive_debit = 'Y',places = 2) {
        return Utilities.formatNumber(Math.abs(Utilities.round(amount,places)),places)+((positive_debit == 'Y' && Utilities.round(amount,places) > 0 ) || (positive_debit == 'N' && Utilities.round(amount,places) < 0) ? ' Dr':' Cr');
    }

    static copyProperties(source_obj,target_obj,include_function = 'N',set_properties = null,include_exclude = 'I') {
        for (let field in source_obj) {
            if(typeof(source_obj[field]) != 'object') {
                if(!set_properties || (include_exclude == 'I' && set_properties.includes(field)) || (include_exclude == 'E' && !set_properties.includes(field))) {
                    if(target_obj.hasOwnProperty(field)) {
                        if(include_function == 'Y' || typeof(source_obj[field]) != 'function') {
                            target_obj[field] = source_obj[field];
                        }
                    }
                }
            }
        }
    }

    static getTransGstDetails(gst_details,gst_on,gst_adj,acid_input_output = 'I') {
        var trans_gst_details = [];
        var gst_amt = 0;
        var gst_value = 0;
        var gst_adj_amt = gst_adj;
        var amt = 0;
        var amt1 = 0;
        var acid = 0;
        gst_details.forEach(function(row1) {
            gst_value = Utilities.round((gst_on * Utilities.round(row1.rate, 4)) / 100);
            gst_amt += gst_value;
            trans_gst_details.push({
                gst_det_id: row1.id,
                gst_name: row1.name,
                gst_rate: row1.rate,
                gst_on: gst_on,
                gst_value: gst_value,
                acid_gst: acid_input_output == 'I' ? row1.acid_input:row1.acid_output
            });
        });
        if (gst_adj_amt != 0) {
            gst_value = gst_amt;
            trans_gst_details.forEach(function(row1) {
                amt1 = Utilities.round(row1.gst_value);
                if (Utilities.round(row1.gst_value) == gst_value) {
                    amt = Utilities.round(gst_adj_amt);
                    row1.gst_value = Utilities.round(Utilities.round(row1.gst_value, 2) + amt);
                    } else {
                    amt = Utilities.round((Utilities.round(row1.gst_value, 2) *Utilities.round(gst_adj_amt, 2)) /Utilities.round(gst_amt, 2));
                    row1.gst_value = Utilities.round(Utilities.round(row1.gst_value, 2) + amt);
                }
                gst_adj_amt -= amt;
                gst_adj_amt = Utilities.round(gst_adj_amt);
                gst_value -= amt1;
                gst_value = Utilities.round(gst_value);
            });
        }
        gst_amt += Utilities.round(gst_adj, 2);
        gst_amt = Utilities.round(gst_amt, 2);
        var net_amt = Utilities.round(gst_on + Utilities.round(gst_amt));
        return [trans_gst_details,gst_amt,net_amt];
    }

    static getVatCstDetails(vat_cst,vat_on,local_outside,acid_input_output = 'I') {
        // vat_cst_id, vat_cst, vat_cst_on, vat_cst_rate, vat_cst_amt, surcharge_rate, surcharge_amt, vat_cst_acid, vat_cst_surcharge_acid
        // id, name, vat_rate, cst_rate, sur_on_vat, sur_on_cst, vat_ac_id, cst_ac_id, sur_vat_ac_id, sur_cst_ac_id
        var vat_cst_details = {};
        vat_cst_details['vat_cst_id'] = vat_cst.id;
        vat_cst_details['vat_cst_on'] = vat_on;
        if(local_outside == "L") {
            vat_cst_details['vat_cst'] = 'V';
            vat_cst_details['vat_cst_rate'] = vat_cst.vat_rate;
            vat_cst_details['surcharge_rate'] = vat_cst.sur_on_vat;
            vat_cst_details['vat_cst_acid'] = vat_cst.vat_ac_id;
            vat_cst_details['vat_cst_surcharge_acid'] = vat_cst.sur_vat_ac_id;
        } else {
            vat_cst_details['vat_cst'] = 'C';
            vat_cst_details['vat_cst_rate'] = vat_cst.cst_rate;
            vat_cst_details['surcharge_rate'] = vat_cst.sur_on_cst;
            vat_cst_details['vat_cst_acid'] = vat_cst.cst_ac_id;
            vat_cst_details['vat_cst_surcharge_acid'] = vat_cst.sur_cst_ac_id;
        }
        vat_cst_details['vat_cst_amt'] = Utilities.round(Utilities.round(vat_on)*Utilities.round(vat_cst_details['vat_cst_rate'])/100);
        vat_cst_details['surcharge_amt'] = Utilities.round(Utilities.round(vat_cst_details['vat_cst_amt'])*Utilities.round(vat_cst_details['surcharge_rate'])/100);
        return vat_cst_details;
    }

    static  resetDetails(self,showDetails){
        var windowoffset = window.pageYOffset;
        self[showDetails] = false;
        self.$nextTick(function () {
            self[showDetails] = true;
            window.scroll(0,windowoffset);
        });
    }

    static getRandomNumber(){
        return Math.floor(Math.random() * 100000)+'A'+Math.floor(Math.random() * 1000000);
    }

    static blockUIMessage(msg, time) {
        $.blockUI({
            message: msg,
            baseZ: 4000,
            css: {
                border: 'none',
                padding: '15px',
                background: '#00506c',
                color: '#fff'
            }
        });
        if(time){
            setTimeout(() => {
                $.unblockUI();
            }, time);
        }
    }


    static growlUIMessage(msg,time){
        $.blockUI({
            message: msg,
            fadeIn: 700,
            fadeOut: 700,
            timeout: time,
            showOverlay: false,
            centerY: false,
            baseZ: 2400,
            css: {
                width: '350px',
                top: '100px',
                left: '',
                right: '10px',
                border: 'none',
                padding: '5px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .8,
                color: '#fff',
            }
        });
    }

    static pluck(objs, name) {
        var sol = [];
        for(var i in objs){
            if(objs[i].hasOwnProperty(name)){
                sol.push(objs[i][name]);
            }
        }
        return sol;
    }

    static refreshComponent(ref,showComp){
        ref[showComp] = false;
        setTimeout(() => {
            ref[showComp] = true;
        }, 200);
    }


    static shuffleArray(arra1) {
        var ctr = arra1.length, temp, index;
        // While there are elements in the array
            while (ctr > 0) {
        // Pick a random index
                index = Math.floor(Math.random() * ctr);
        // Decrease ctr by 1
                ctr--;
        // And swap the last element with it
                temp = arra1[ctr];
                arra1[ctr] = arra1[index];
                arra1[index] = temp;
            }
            return arra1;
    }

    static stringDate(date,format = 'MM-DD-YYYY',desired_format='MMMM DD, YYYY'){
        if(moment(date,format).isValid())
            return moment(date,format).format(desired_format);
        else
            return "";
    }


    static getBold(str,bold) {
        return (bold == 'Y' || bold == 'y') ? '<strong>'+str+'</strong>':str;
    }

    static stringTime(time,format='H:m',desired_format ='hh mm a'){
        if(moment(time,format).isValid())
            return moment(time,format).format(desired_format);
        else
            return "";
    }

    static timeDiffHoursAndMiutes(start_date,start_time,end_date,end_time,start_date_format="DD-MM-YYYY",end_date_format="DD-MM-YYYY"){
        var a = moment(start_date+' ,'+ start_time, start_date_format +',HH:mm').format("DD/MM/YYYY HH:mm:ss");
        var b = moment(end_date+' ,'+ end_time , end_date_format+',HH:mm').format("DD/MM/YYYY HH:mm:ss");;

        var diff = moment.duration(moment(b).diff(moment(a)));
        var days = parseInt(diff.asDays()); //84
        var hours = parseInt(diff.asHours()); //2039 hours, but it gives total hours in given miliseconds which is not expacted.
        hours = hours - days*24;  // 23 hours
        var minutes = parseInt(diff.asMinutes()); //122360 minutes,but it gives total minutes in given miliseconds which is not expacted.
        minutes = minutes - (days*24*60 + hours*60);
        return hours > 0 ? hours +' hours, '+minutes+' minutes':minutes+' minutes';
    }

    static  capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    static getSlot(slot) {
        slot = slot.toString();
        var slot_time = slot;
        var ampm = 'AM';
        if(slot*1 == 12 || slot*1 < 9) {
            ampm = 'PM';
        }
        if(slot*1 != 12) {
            return slot+'.00 '+ampm+' - '+(slot*1+1)+'.00 '+ampm;
        } else {
            return '12.00 PM - 1.00 PM';
        }
    }


    // ******************** this function is used to remove underscore from string and upper case first lette for display purpose
    // ************* example string_abc will be converted into String Abc
    static getComputedKey(key){
        var str =  key.replace(/_/g, " ");
        str = str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()});
        return str;
    }

    static showPopMessage(msg="",title="",icon="success",timer='1500',showConfirmButton =false ,position='center'){
        if(timer != 0){
            Swal.fire({
                position: position,
                text:msg,
                icon: icon,
                title:title,
                showConfirmButton: showConfirmButton,
                timer: timer,
            });
        }
        else{
            Swal.fire({
                position: position,
                text:msg,
                icon: icon,
                title:title,
                showConfirmButton: showConfirmButton,
            });
        }

    }

    static showConfirmationPopMessage(title='Do you want to save the changes?',callback,showDenyButton=true,showCancelButton=true,confirmButtonText='Yes',denyButtonText="No",allowOutsideClick=true,allowEscapeKey=true){
        Swal.fire({
            title: title,
            showDenyButton: showDenyButton,
            showCancelButton: showCancelButton,
            confirmButtonText: confirmButtonText,
            denyButtonText: denyButtonText,
            allowOutsideClick: allowOutsideClick,
            allowEscapeKey:allowEscapeKey,
            customClass: {
              cancelButton: 'order-1 right-gap',
              confirmButton: 'order-2',
              denyButton: 'order-3',
            }
          }).then((result) => {
            if (result.isConfirmed) {
                callback(true);
            } else if (result.isDenied) {
                callback(false);
            }
          })
    }

    static getMonths(){
        return [
            '01' ,'02' , '03','04','05','06','07','08','09','10','11','12'
        ]
    }

    static getYears( next=30, previous=1){
        var current =  moment().format('YYYY');
        var arr = [];
        for(var i = current-previous;i<= (parseInt(current)+parseInt(next));i++){
            arr.push(i);
        }
        return arr;
    }

    static sumDataTableCols(api,colsToBeSummed,decimals = 3) {
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };
        $.each(colsToBeSummed,function(key,val) {
            var total = 0;
            total = api
                .column( val)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $( api.column( val ).footer() ).html(total.toFixed(decimals));
        })
    }

    static showToast(ref,type="sucess",msg="success",timeout=5000,hideProgressBar=false,closeButton="button",closeOnClick=true,pauseOnFocusLoss=true,draggable=true,pauseOnHover=false,draggablePercent=0.6,
    showCloseButtonOnHover=false,icon=true,rtl=false) {
        ref.$toast[type](msg, {
            position: "top-right",
            timeout: timeout,
            closeOnClick: closeOnClick,
            pauseOnFocusLoss: pauseOnFocusLoss,
            pauseOnHover: pauseOnHover,
            draggable: draggable,
            draggablePercent:draggablePercent,
            showCloseButtonOnHover: showCloseButtonOnHover,
            hideProgressBar: hideProgressBar,
            closeButton: closeButton,
            icon: icon,
            rtl: rtl
          });
    }

    static Substring(str,length){
        return str.substring(0, length);
    }

    static isEquivalent(a, b) { // check if two objects are identicals
    // Create arrays of property names
    var aProps = Object.getOwnPropertyNames(a);
    var bProps = Object.getOwnPropertyNames(b);

    // If number of properties is different,
    // objects are not equivalent
    if (aProps.length != bProps.length) {
        return false;
    }

    for (var i = 0; i < aProps.length; i++) {
        var propName = aProps[i];

        // If values of same property are not equal,
        // objects are not equivalent
        if (a[propName] !== b[propName]) {
            return false;
        }
    }

    // If we made it this far, objects
    // are considered equivalent
    return true;
}

static setProperCase(str) {
    return str.length > 0 ? str.substring(0,1).toUpperCase()+str.substring(1).toLowerCase():str;
}

static joinArrayAsString(arr=[], seprate_with = ',', add_and= true){
    var result =  "";
    if(arr.length == 1){
        return arr[0];
    }
    if(add_and){
        const last = arr.pop();
        return  result = arr.length > 0 ? arr.join(', ') + ' and ' + last:"";
    }
    return result = arr.length > 0 ? arr.join(', '):"" ;
}

static getLodgingGstId(lodging_gsts,rate) {
    var gst_id = 0;
    lodging_gsts.forEach(ele => {
        if(rate <= ele.rate_upto && gst_id == 0) {
            gst_id = ele.gst_id;
        }
    })
    return gst_id;
}

static getGstDetails(entry_date,gst,lst_cst,gst_prec=-1) {
    var gstdet = [];
    var dtdoc = moment(entry_date,'DD-MM-YYYY');
    var daysdiff = -1;
    var i = 0;
    if(gst && gst.gst_types){
        gst.gst_types.forEach(function(ele){
            if(lst_cst == ele.gst_type){
                var y = dtdoc.diff(moment(ele.wef_date,'DD-MM-YYYY'),'days');
                if(y >= 0) {
                    if(daysdiff == -1)
                        daysdiff = y;
                    if(daysdiff <= y) {
                        gstdet = ele.details;
                    }
                }
            }
        });
    }

    if(gst_prec >= 0) {
        gstdet.forEach(function(ele) {
            if(ele.name == 'cgst' || ele.name == 'sgst') {
                ele.rate = Utilities.round(gst_prec/2);
            }
            if(ele.name == 'igst') {
                ele.rate = Utilities.round(gst_prec);
            }
        });
    }
    return gstdet;
}

    static getTransGstDetails(gst_details,gst_on,gst_adj,acid_input_output = 'I',acid_pur_sale = 0) {
        var trans_gst_details = [];
        var gst_amt = 0;
        var gst_value = 0;
        var gst_adj_amt = gst_adj;
        var amt = 0;
        var amt1 = 0;
        var acid = 0;
        gst_details.forEach(function(row1) {
            gst_value = Utilities.round((gst_on * Utilities.round(row1.rate, 4)) / 100);
            gst_amt += gst_value;
            trans_gst_details.push({
                gst_det_id: row1.id,
                gst_name: row1.name,
                gst_rate: row1.rate,
                gst_on: gst_on,
                gst_value: gst_value,
                acid_pur_sale: acid_pur_sale,
                acid_gst: acid_input_output == 'I' ? row1.acid_input: row1.acid_output
            });
        });
        if (gst_adj_amt != 0) {
            gst_value = gst_amt;
            trans_gst_details.forEach(function(row1) {
                amt1 = Utilities.round(row1.gst_value);
                if (Utilities.round(row1.gst_value) == gst_value) {
                    amt = Utilities.round(gst_adj_amt);
                    row1.gst_value = Utilities.round(Utilities.round(row1.gst_value, 2) + amt);
                    } else {
                    amt = Utilities.round((Utilities.round(row1.gst_value, 2) *Utilities.round(gst_adj_amt, 2)) /Utilities.round(gst_amt, 2));
                    row1.gst_value = Utilities.round(Utilities.round(row1.gst_value, 2) + amt);
                }
                gst_adj_amt -= amt;
                gst_adj_amt = Utilities.round(gst_adj_amt);
                gst_value -= amt1;
                gst_value = Utilities.round(gst_value);
            });
        }
        gst_amt += Utilities.round(gst_adj, 2);
        gst_amt = Utilities.round(gst_amt, 2);
        var net_amt = Utilities.round(gst_on + Utilities.round(gst_amt));
        return [trans_gst_details,gst_amt,net_amt];
    }

    static getTransGstRate(gst_details,gst_on,gst_adj,acid_input_output = 'I',itc_required = 'N',acid_pur_sale = 0) {
        var gst_rate = 0;
        gst_details.forEach(function(row1) {
            gst_rate += Utilities.round(row1.rate);
        });
        return gst_rate;
    }

    static getTransVatCstRate(vat_cst,local_outside){
        if(local_outside == "L"){
            return [vat_cst.vat_rate,vat_cst.sur_on_vat];
        }
        return  [vat_cst.cst_rate,vat_cst.sur_on_cst]
    }

    static getRateDiff(rate, array=[]) {
        for (let i = 0; i < array.length; i++) {
          const { rate_from, rate_to, rate_diff ,packing_id} = array[i];
          if (rate >= parseFloat(rate_from) && rate <= parseFloat(rate_to)) {
            return rate_diff;
          }
        }
        return 0; // Return null if no matching range is found
      }

      static scrollToTop(){
            window.scrollTo(0,0);
      }

      static getVchrType(vcode,vchr_no='') {
        var vchr_type = '';
        switch(vcode.substring(0,2)) {
            case 'SA':
                vchr_type = 'Sale';
                break;
            case 'VC':
                if(vchr_no && vchr_no != '') {
                    vchr_type = vchr_no.substring(0,2);
                } else {
                    vchr_type = 'Voucher';
                }
        }
        return vchr_type;
      }

}

export default Utilities;
