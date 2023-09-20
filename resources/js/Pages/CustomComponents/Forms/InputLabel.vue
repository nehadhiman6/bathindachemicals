<script setup>
defineProps({
    value: String,
    required:{
        type:Boolean,
        default:false
    }
});
</script>

<template>
    <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700" :class="required ? 'required':''">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
