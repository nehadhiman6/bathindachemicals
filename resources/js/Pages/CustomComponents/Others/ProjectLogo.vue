<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
});

const classes = computed(() => {
    return 'iw-logo block px-4 py-4 m-0 text-sm whitespace-nowrap dark:text-white text-slate-700';
});

</script>

<template>
    <!-- <Link :href="href" :class="classes">
        <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
            <i :class="icon_classes"></i>
        </div>
        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease"> <slot /></span>
    </Link> -->
    <div class="h-19 sticky top-0 z-30 bg-white">
        <!-- <i data-drawer-hide="drawer-navigation" aria-controls="drawer-navigation" class="absolute top-0 right-0 p-4 opacity-50 cursor-pointer fas fa-xmark dark:text-white text-slate-400 xl:hidden" sidenav-close="" aria-hidden="true"></i> -->
        <Link :href="href" :class="classes">
            <img :src="$page.props.base_url +'/assets/img/bcl-logo.png'" class="iw-logo-desktop inline h-full max-w-full transition-all duration-200 dark:hidden ease-nav-brand max-h-12" alt="main_logo">
            <!-- <img :src="$page.props.base_url +'/assets/img/bcl-logo.png'" class="hidden h-full max-w-full transition-all duration-200 dark:inline ease-nav-brand max-h-12" alt="main_logo"> -->
            <img :src="$page.props.base_url +'/assets/img/logo-sm.png'" class="iw-logo-collapsed inline h-full max-w-full transition-all duration-200 dark:inline ease-nav-brand max-h-12" alt="main_logo">
        </Link>
    </div>
</template>
