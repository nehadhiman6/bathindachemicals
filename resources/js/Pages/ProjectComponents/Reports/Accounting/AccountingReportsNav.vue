

<script setup>
    import { Link } from '@inertiajs/vue3';
    import UpperNavLink from '@/Pages/CustomComponents/Layout/UpperNavLink.vue';
    import globalMixin from '../../../../globalMixin';
    const { isLinkActive,canAny} = globalMixin();
</script>
<template>
<ul class="flex flex-wrap w-full mb-4 px-5">
    <li class="m-0">
        <UpperNavLink :href="$page.props.base_url +'/ledger-report'"  color-class="text-white-500" :active="isLinkActive(['ledger-report'])" data-drawer-hide="drawer-navigation">
            Ledger Report
        </UpperNavLink>

    </li>
    <li class="m-0">
        <UpperNavLink :href="$page.props.base_url +'/trial-balance-report'"  color-class="text-white-500" :active="isLinkActive(['trial-balance-report'])" data-drawer-hide="drawer-navigation">
            Trial balance Report
        </UpperNavLink>

    </li>
</ul>
</template>
