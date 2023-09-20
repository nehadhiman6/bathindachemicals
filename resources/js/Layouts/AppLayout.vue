<script setup>
import { ref, onMounted, reactive} from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Sidebar from '../Layouts/Sidebar.vue';
import TopNavigation from '../Layouts/TopNavigation.vue';
import { initFlowbite} from 'flowbite';

defineProps({
    title: String,
});


onMounted(() => {
    initFlowbite();

});
const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};



const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <div class="fixed w-full bg-primary dark:hidden h-80"></div>
        <Head :title="title" />
        <Banner/>
        <!-- <div class="min-h-screen bg-gray-100"> -->
        <Sidebar />
        <TopNavigation :title="title"/>

        <!-- Page Content -->
        <main  class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-[244px] rounded-xl">
            <slot />

        </main>
    <!-- </div> -->
    </div>
</template>
