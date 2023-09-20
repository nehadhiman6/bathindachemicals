<template>
    <div>
        <select :class="'form-control ref_'+id" :disabled="disabled"></select>
    </div>
</template>
<script>
export default {
    props:['index','initials','selected','disabled','url','ignoreRef','ignoreVcode'],
    data:function(){
        return{
            id:'',
            createUrl:'bill-reference-keys/filtered'
        }
    },
    created:function(){
        this.id = this._uid;
    },
    mounted:function(){
        if(this.url != '' && typeof(this.url) != "undefined"){
            this.createUrl = this.url;
        }
        this.intialiseSelect();
    },
    methods:{
        intialiseSelect:function(){
            var self = this;
            var refComp = $('.ref_'+self.id);
            var refKeyComp= refComp.select2({
                    dropdownAutoWidth : true,
                    placeholder: "Select key",
                    width: '100%',
                    data:self.initials,
                    ajax: {
                        method:'post',
                        url: function() {
                            if(typeof(self.url) == 'function') {
                                return self.url();
                            }
                            this.createUrl
                        },
                        delay: 250,
                        dataType: 'json',
                        cache: true,
                        data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1,
                                ignore_ref:self.ignoreRef,
                                ignore_vcode:self.ignoreVcode,
                            }
                            // Query parameters will be ?search=[term]&page=[page]
                            return query;
                        },
                        processResults: function (data,params) {
                            params.page = params.page || 1;
                            // Tranforms the top-level key of the response object from 'items' to 'results'
                            data.results.forEach(function(ele){
                                ele.text = ele.ref_key;
                            });
                            // this is a mondatory object that's why i added here
                            data.pagination = {
                                "more": (params.page * 30) < data.count_filtered
                            };
                            return data;
                        }
                    },
                    templateResult: self.setName,
                    templateSelection: self.selection,
                })
                .on('change', function () {

                })

                .on('select2:select', function (e) {
                    var item = $('.ref_'+self.id).val();
                    self.selected_data = e.params.data;

                    if( self.selected_data != {}){
                        self.$emit('updateKey',self.selected_data);
                    }
                })
                .on('select2:open',function () {
                    $('.select2-container').addClass('reference');
                })
                .on('select2:closing',function () {
                    $('.select2-container').removeClass('reference');
                });
                $('.ref_'+self.id).val(self.selected).trigger('change');
            },
            setName :function(details) {
                    if (!details.id) { return details.text; }
                    var $details = $('<div class="row"><div class="col-md-6">' + details.text + '</div><div class="col-md-4">'+details.amount+' '+details.dr_cr+'<div></div>');
                    return $details;
            },
            selection :function(details) {
                var self= this;
                if (!details.id) { return details.text; }
                    var $details = $('<div class="row"><div class="col-md-8">' + details.text + '</div></div>');
                   return $details;
            }
    }
}
</script>
