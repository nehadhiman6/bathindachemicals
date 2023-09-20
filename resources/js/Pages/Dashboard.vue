<script setup>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import {  onMounted} from 'vue';

  //TO SOLVE CSRF ISSUE ON PAGE AFTER FIRST LOGIN
  const props = defineProps(['csrf']);

    onMounted(() => {
        $('meta[name="csrf-token"]').attr('content',props.csrf);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN':props.csrf
            }
        });
    });
</script>

<template>
<AppLayout title="Dashboard">
    <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </template>
    <div class="w-full px-6 pb-0 mx-auto">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Users</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white">530</h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-blue-500 to-violet-500 flex items-center justify-center ml-auto">
                                    <i class="fa fa-users text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Accounts</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white">230</h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-red-600 to-orange-600 flex items-center justify-center ml-auto">
                                    <i class="fa fa-user text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Items</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white">346</h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-emerald-500 to-teal-400 flex items-center justify-center ml-auto">
                                    <i class="fa fa-list text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full max-w-full px-3 sm:w-1/2 sm:flex-none xl:w-1/4">
                <div class="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="flex-auto p-4">
                        <div class="flex flex-row -mx-3">
                            <div class="flex-none w-2/3 max-w-full px-3">
                                <div>
                                    <p class="mb-0 font-sans font-semibold leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Others</p>
                                    <h5 class="2xl:text-2xl xl:text-xl lg:text-xl text-xl mb-2 font-bold dark:text-white">103,430</h5>
                                </div>
                            </div>
                            <div class="px-3 text-right basis-1/3">
                                <div class="w-12 h-12 text-center rounded-full bg-gradient-to-tl from-orange-500 to-yellow-500 flex items-center justify-center ml-auto">
                                    <i class="fa fa-network-wired text-lg text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AppLayout>
</template>
