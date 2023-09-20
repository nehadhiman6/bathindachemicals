<script setup>
    import {  ref, watch, onMounted,reactive} from 'vue';
    import Datepicker from 'flowbite-datepicker/Datepicker';
    const selectedDate = ref(props.range ? [] : null);
    const emit = defineEmits(['update:modelValue']);
    const state = reactive({
       options:null
    });

    const props = defineProps({
        modelValue: String,
        autohide:{
            type: Boolean,
            default: true,
        },
        beforeShowDay:{
            default:null
        },
        beforeShowDecade:{
            default: null,
        },
        beforeShowMonth:{
            default: null,
        },
        beforeShowYear:{
            default: null,
        },
        calendarWeeks:{
            default: false,
        },
        clearBtn:{
            default: false,
        },
        dateDelimiter:{
            default: ',',
        },
        datesDisabled:{
            default: [],
        },
        daysOfWeekDisabled:{
            default: [],
        },
        daysOfWeekHighlighted:{
            default: [],
        },
        defaultViewDate:{
            default: undefined,
        },
        disableTouchKeyboard:{
            default: false,
        },
        format:{
            default: 'dd-mm-yyyy',
        },
        language:{
            default: 'en',
        },
        maxDate:{
            default: null,
        },
        maxNumberOfDates:{
            default: 1,
        },
        maxView:{
            default: 3,
        },
        minDate:{
            default: null,
        },
        orientation:{
            default: 'auto',
        },
        pickLevel:{
            default: 0,
        },
        showDaysOfWeek:{
            default: true,
        },
        showOnClick:{
            default: true,
        },
        showOnFocus:{
            default: true,
        },
        startView:{
            default: 0,
        },
        title:{
            default: '',
        },
        todayBtn:{
            default: false,
        },
        todayBtnMode:{
            default: 0,
        },
        todayHighlight:{
            default: false,
        },
        updateOnBlur:{
            default: true,
        },
        weekStart:{
            default: 0,
        },
        disabled:{
            default: false,
            type:Boolean
        },
        className:{
            default:'date-picker'
        }
    });

    let pickerEl;

    onMounted(()=>{
        // return;
        setStateOptions();
        const datepickerEl = document.getElementsByClassName(props.className)[0];
        pickerEl = new Datepicker(datepickerEl, state.options)

        pickerEl.setDate(props.modelValue);
        datepickerEl.addEventListener('changeDate', (e) => {
            // console.log(pickerEl.getDate(props.format));
            emit('update:modelValue', pickerEl.getDate(props.format))
        });
    });

    const setStateOptions = ()=>{
        state.options =  {
            autohide: props.autohide,
            beforeShowDay: props.beforeShowDay,
            beforeShowDecade: props.beforeShowDecade,
            beforeShowMonth: props.beforeShowMonth,
            beforeShowYear: props.beforeShowYear,
            calendarWeeks: props.calendarWeeks,
            clearBtn: props.clearBtn,
            dateDelimiter: props.dateDelimiter,
            datesDisabled: props.datesDisabled,
            daysOfWeekDisabled: props.daysOfWeekDisabled,
            daysOfWeekHighlighted: props.daysOfWeekHighlighted,
            defaultViewDate: props.defaultViewDate,
            disableTouchKeyboard: props.disableTouchKeyboard,
            format: props.format,
            language: props.language,
            maxDate: props.maxDate,
            maxNumberOfDates: props.maxNumberOfDates,
            maxView: props.maxView,
            minDate: props.minDate,
            orientation: props.orientation,
            pickLevel: props.pickLevel,
            showDaysOfWeek: props.showDaysOfWeek,
            showOnClick: props.showOnClick,
            showOnFocus: props.showOnFocus,
            startView: props.startView,
            title: props.title,
            todayBtn: props.todayBtn,
            todayBtnMode: props.todayBtnMode,
            todayHighlight: props.todayHighlight,
            updateOnBlur: props.updateOnBlur,
            weekStart: props.weekStart,
        }
    }

    watch(() => props.minDate, (newMinDate) => {
        setStateOptions();
        // pickerEl.options.minDate = '2024-01-01'; // Example: Changing the minimum date

        // Step 3: Apply the changes
        pickerEl.setOptions(state.options); // FOR SETTING NEW OPTIONS WHEN PROP CHANGES
        pickerEl.refresh();
    });

</script>
<template>
  <!-- <div> -->
    <div class="relative max-w-sm">
  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
  </div>
  <input :disabled="props.disabled" :class ="props.className" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date" v-bind:value="props.modelValue">
</div>
</template>
