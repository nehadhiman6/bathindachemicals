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
        ? 'py-2 relative bg-white/20 dark:text-white dark:opacity-80 text-sm  my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 font-semibold text-white transition-colors before:absolute before:left-1/2 before:bottom-[-8px] before:-translate-x-1/2 before:h-0 before:w-0 before:border-x-8 before:border-x-transparent before:border-t-[8px] before:border-b-white/15 before:opacity-20'
        : 'py-2 relative dark:text-white dark:opacity-80 text-sm  my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4  text-white transition-colors opacity-60';
});

const icon_classes = computed(() => {
    return 'relative top-0 leading-normal '+props.colorClass+' '+ props.icon +' text-sm';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <!-- <div class="mr-1 flex h-8 w-8 items-center  rounded-lg bg-center stroke-0 text-center xl:p-2.5">
            <i :class="icon_classes"></i>
        </div> -->
        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease"> <slot /></span>
    </Link>
</template>
