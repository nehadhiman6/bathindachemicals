<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    icon: String,
    colorClass: String,
});

const classes = computed(() => {
    return props.active
        ? 'py-3 bg-indigo-600/[0.13] dark:text-white dark:opacity-80 text-sm  my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 font-semibold text-slate-700 transition-colors'
        : 'py-3 dark:text-white dark:opacity-80 text-sm  my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4  text-slate-700 transition-colors';
});

const icon_classes = computed(() => {
    return 'relative top-0 leading-normal '+props.colorClass+' '+ props.icon +' text-sm';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
            <i :class="icon_classes"></i>
        </div>
        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease"> <slot /></span>
    </Link>
</template>
