<script setup>
import { onMounted, ref,computed } from 'vue';

const props = defineProps({
    modelValue: [String,Number],
    options:Array,
    error:Boolean
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

const classes = computed(() => {
    return props.error ? 'border-red-400 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-sm block w-full'
                        :'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-sm block w-full';
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <select
        ref="input"
        :class="classes"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
        <option v-for="option in options" :key="option.id" :value="option.id">{{option.text}}</option>
    </select>
</template>
