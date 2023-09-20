<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    idName:String,
    active: Boolean,
    icon: String,
    colorClass: String,
});

const classes = computed(() => {
    return props.active
        ? 'dark:text-white bg-indigo-600/[0.13] dark:opacity-80 py-3 text-sm  my-0 mx-2 flex items-center whitespace-nowrap px-4 font-semibold transition-colors'
        : 'dark:text-white dark:opacity-80 py-3 text-sm  my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors';
});

const icon_classes = computed(() => {
    return 'relative top-0 leading-normal '+props.colorClass+' '+ props.icon +' text-sm';
});
</script>

<template>
    <a :class="classes" href="javascript:void(0)" :aria-controls="props.idName" :data-collapse-toggle="props.idName" :aria-expanded="props.active ? true:false">
        <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
            <i :class="icon_classes"></i>
        </div>
        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease"><slot /></span>
        <svg sidebar-toggle-item="" class="ml-auto w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
    </a>
</template>
