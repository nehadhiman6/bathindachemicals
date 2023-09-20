<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="ml-2 inline-flex items-center justify-center px-4 py-2 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-500 focus:outline-none focus:border-slate-700 focus:ring focus:ring-slate-200 active:bg-slate-600 disabled:opacity-25 transition" @click.prevent="$emit('buttonClicked')">
        <slot/>
    </button>
</template>
