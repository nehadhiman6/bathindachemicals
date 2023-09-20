<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked','changed']);

const props = defineProps({
    checked: {
        type: [Array, Boolean,String],
        default: false,
    },
    trueValue: {
        type: [String,Number,Boolean],
        default: true,
    },
    falseValue: {
        type: [String,Number,Boolean],
        default: false,
    },
    disabled: {
        type: [Boolean],
        default: false,
    },
    id: {
        type: [String,Number],
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit('update:checked', val);
    },
});
</script>


<template>
<div class="flex items-center">
  <input
        v-model="proxyChecked"
        type="checkbox"
        :true-value="trueValue"
        :false-value="falseValue"
        :disabled="disabled"
        :id = id
        @change="emit('changed',$event,value)"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
    >
    <label :for="id" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"><slot/></label>
</div>

</template>
