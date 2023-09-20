<script setup>
   import {   ref,  computed,  onMounted,  onUnmounted,  reactive} from 'vue';
    import globalMixin from '../../../globalMixin';
    const { base_url} = globalMixin();
    const emit = defineEmits(['updateUqc','update:modelValue']);

    const props = defineProps(
    {
        modelValue: [String,Number,Array],
        index: {default:-1 ,type: [String,Number]},
        initials: {default: () => [],type: Array},
        selected: {default: () => [],type: Array},
        url:{default:'uqcs/filtered',type: String},
        getIndex: {default:false , type: Boolean},
        disabled: {default:false , type: Boolean},
        customClass: {default:'selectItem',type:String},
        focus: {default:false, type: Boolean},
        enableNew: {default:false, type:Boolean},
        placeholder:{default:'Select Uqc',type:String},
        type:{default:'all',type:String},
        multiple:{default:false,type:Boolean},
        allowClear:{default:true,type:Boolean},
        projectId:{default:0,type:[Number,String]},
        searchType:{default:'',type:String},
        error:{default:false},
        }
    );
    var selected_data = ref(0);

    const classes = computed(() => {
        return props.error ? 'uqc_'+props.index+' border-red-400 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-sm block w-full'
                            :'uqc_'+props.index+' border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-sm block w-full';
    });

    onMounted(() => {
        intialiseSelect();
    });


    const intialiseSelect = ()=>{
        console.log("base_url");
        console.log(base_url.value);
        var itemComponent = $('.uqc_'+props.index);
        var item = itemComponent.select2({
            dropdownAutoWidth : true,
            placeholder: props.placeholder,
            allowClear:props.allowClear,
            width: '100%',
            data:props.initials,
            dropdownParent:$('body'),
            dropdownCssClass: "uqc_drop_"+props.index,
            ajax: {
                method:'POST',
                url: function() {
                    return base_url.value+'/'+props.url;
                },
                delay: 250,
                dataType: 'json',
                cache: true,
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        type : props.type,
                        search_type:props.searchType
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data,params) {
                    params.page = params.page || 1;
                    // Tranforms the top-level key of the response object from 'salesmen' to 'results'
                    data.results.forEach(function(ele){
                        ele.text = ele.uqc_name;
                    });
                    // this is a mondatory object that's why i added here
                    data.pagination = {
                        "more": (params.page * 30) < data.count_filtered
                    };
                    return data;
                }
            },
            templateResult: props.setName,
            templateSelection: props.selection,
        })
        .on('change', function () {
            var item = $('.uqc_'+ props.index).val();
            if(props.multiple == false){
               emit('update:modelValue', item);
            }
            else if(props.multiple == true){
               emit('updateUqc',item,props.index);
            }
        })
        .on('select2:select', function (e) {
            var item = $('.uqc_'+ props.index).val();
            selected_data = e.params.data;
            if( selected_data != {}){
               emit('updateUqc',item,props.index,selected_data);
            }
        })
        .on('select2:clear',function(e){
            var item = $('.uqc_'+ props.index).val();
            if(props.multiple == false){
                var val = item ? item :0;
               emit('input', val);
            }
        })
        .on('select2:open', (e) => {
            if(props.multiple == false){
                document.querySelector('.uqc_drop_'+props.index+' .select2-search__field').focus();
            }
       });

        if(typeof(props.index) !="undefined"){
            $('.uqc_'+props.index).val(props.selected).change();
        }
    }

    const setName = () =>{
        if (!details.id) { return details.text; }
        var $details = $('<div class="flex flex-wrap -mx-3"><div class="w-full md:w-1/1 px-3 md:mb-0">' + details.text + '</div></div>');
        return $details;
    }

    const selection = () =>{
        if (!details.id) { return details.text; }
        var $details = $('<div class="flex flex-wrap -mx-3"><div class="w-full md:w-1/1 px-3 mb-6 md:mb-0">' + details.text + '</div></div>');
        return $details;
    }

</script>
<template>
    <select
        :multiple="multiple"
        ref="input"
        :class="classes"
        :value="modelValue"
        :disabled="disabled"
    >
    </select>

</template>
