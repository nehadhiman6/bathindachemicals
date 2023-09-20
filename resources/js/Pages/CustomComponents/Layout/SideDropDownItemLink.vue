<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'relative flex items-center w-full text-sm p-2 transition duration-75 rounded-lg pl-16 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 font-bold before:absolute before:left-[35px] before:top-[15px] before:w-4 before:h-0.5 before:bg-primary'
        : 'relative flex items-center w-full text-sm p-2 transition duration-75 rounded-lg pl-16 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700';
});
</script>

<template>
    <li>
        <Link :href="href"  :class="classes"><slot /></Link>
    </li>
</template>
