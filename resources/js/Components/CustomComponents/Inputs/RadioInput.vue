<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked','changed']);

const props = defineProps({
    modelValue: String,
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    name: {
        type: [String,Number],
        default: null,
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
        emit('update:modelValue', val);
    },
});
</script>


<template>
    <input
        type="radio"
        :value="modelValue"
        :id = "id"
        :name="name"
        @change="emit('changed',$event,value)"
        class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 ml-2 cursor-pointer"
    >
</template>
