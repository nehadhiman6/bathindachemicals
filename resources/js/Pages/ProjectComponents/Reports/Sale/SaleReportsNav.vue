

<script setup>
    import { Link } from '@inertiajs/vue3';
    import UpperNavLink from '@/Pages/CustomComponents/Layout/UpperNavLink.vue';
    import globalMixin from '../../../../globalMixin';
    const { isLinkActive,canAny} = globalMixin();
</script>
<template>
<ul class="flex flex-wrap w-full mb-4 px-5">
    <li class="m-0" v-if="canAny($page.props.granted_permissions,['sale-report'])" >
        <UpperNavLink :href="$page.props.base_url +'/sale-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['sale-report'])">
            Sale Report
        </UpperNavLink>

    </li>
    <li class="m-0" v-if="canAny($page.props.granted_permissions,['contract-booking-report' ])" >
        <UpperNavLink :href="$page.props.base_url +'/contract-booking-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['contract-booking-report'])">
            Contract Booking Report
        </UpperNavLink>
    </li>
    <li class="m-0" v-if="canAny($page.props.granted_permissions,['contract-execution-report'])" >
        <UpperNavLink :href="$page.props.base_url +'/contract-execution-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['contract-execution-report'])">
            Contract Execution Report
        </UpperNavLink>
    </li>

    <li class="m-0" v-if="canAny($page.props.granted_permissions,['pending-contract-report'])" >
        <UpperNavLink :href="$page.props.base_url +'/pending-contract-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation" :active="isLinkActive(['pending-contract-report'])">
            Pending Contract Report
        </UpperNavLink>
    </li>
    <!-- <li class="m-0" v-if="canAny($page.props.granted_permissions,['sale-format-report'])" >
        <UpperNavLink :href="$page.props.base_url +'/sale-format-report'"  color-class="text-white-500" data-drawer-hide="drawer-navigation">
            Sale Format Report
        </UpperNavLink>
    </li> -->

</ul>
</template>
