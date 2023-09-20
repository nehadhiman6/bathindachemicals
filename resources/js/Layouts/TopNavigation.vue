<script setup>
    import { ref } from 'vue';
    import Search from '@/Pages/CustomComponents/Others/Search.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';

    import { Inertia } from '@inertiajs/inertia';


    const props = defineProps(['title']);
    const showingNavigationDropdown = ref(false);

    const switchToTeam = (team) => {
        Inertia.put(route('current-team.update'), {
            team_id: team.id,
        }, {
            preserveState: false,
        });
    };

    const logout = () => {
        Inertia.post(route('logout'));
    };

    const collapseSidebar = () =>{
        var element = document.getElementsByTagName("body")[0];
        element.classList.toggle("sidebar-mini");
        var sidebar = document.getElementsByTagName("aside")[0];
        sidebar.classList.toggle("expand");
    }
</script>
<template>
    <div class="iw-topbar relative transition-all duration-200 ease-in-out xl:ml-[244px]">
        <nav class="relative flex flex-wrap items-center justify-between px-0   mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap lg:justify-start" navbar-main="" navbar-scroll="false">
            <div class="flex flex-wrap items-center justify-between w-full px-4 py-1 mx-auto md:flex-nowrap">
                <nav class="w-full flex items-center">
                    <div class="flex items-center pr-4 hidden xl:flex">
                        <a href="javascript:void(0)" @click="collapseSidebar()" class="block p-0 text-white transition-all ease-nav-brand text-sm">
                            <div class="w-4 overflow-hidden">
                                <i class="ease mb-1 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                                <i class="ease mb-1 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                                <i class="ease relative block h-0.5 rounded-sm bg-white transition-all"></i>
                            </div>
                        </a>
                    </div>

                     <div class="w-full max-w-full px-3 md:w-2/12">
                        <h6 class="mb-0 font-bold text-white capitalize" v-text="props.title+' '">
                    </h6>
                     </div>

                     <div class="w-full max-w-full px-3 md:w-3/12">
                      <span class="mb-0 ml-2 font-bold text-white "  v-text="' (Session : ' + $page.props.session +') '" ></span>
                     </div>
                    <span class="mb-0 ml-2 font-bold text-white ">BRANCH:</span>
                    <div class="w-full max-w-full px-3 md:w-6/12 font-bold text-white">

                        <span v-text="$page.props.session_branch ? $page.props.session_branch.name:'NO BRANCH SELECTED'"></span>

                    </div>
                </nav>
                <div class="flex items-center justify-between mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
                    <!-- <Search/> -->

                    <!-- Primary Navigation Menu -->
                    <div class="flex justify-between h-16">
                        <div class="hidden sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="w-10 h-10 flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="w-full rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>
                                        <DropdownLink :href="route('current-branch')">
                                            Set Branch
                                        </DropdownLink>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>


                                         <!-- <DropdownLink :href="route('profile.show')">
                                            Set Branch
                                        </DropdownLink> -->

                                        <!-- <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink> -->

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                        <div class="flex items-center pl-4 xl:hidden">
                        <a href="#" class="block p-0 text-white transition-all ease-nav-brand text-sm" data-drawer-target="drawer-navigation" data-drawer-show="drawer-navigation" aria-controls="drawer-navigation">
                            <div class="w-4 overflow-hidden">
                                <i class="ease mb-1 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                                <i class="ease mb-1 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                                <i class="ease relative block h-0.5 rounded-sm bg-white transition-all"></i>
                            </div>
                        </a>
                    </div>
                    </div>

                </div>
            </div>
        </nav>
    </div>
</template>


<style>

</style>
