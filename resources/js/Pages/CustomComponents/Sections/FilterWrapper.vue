<script setup>
   const props = defineProps({
        title:{
            default:''
        },
        openClose:{
            default:'close'
        }
   });
</script>
<template>
    <div class="w-full px-6 pb-4 mx-auto">
        <div class="flex flex-wrap -mx-3">

            <div class="w-full max-w-full px-3 shrink-0 md:w-full md:flex-0">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl rounded-2xl bg-clip-border">
                    <div class="border-black/10 rounded-t-2xl border-b border-solid px-5 py-3">
                        <div class="flex items-center">
                            <p class="leading-normal uppercase text-sm font-semibold cursor-pointer flex items-center select-none" data-collapse-toggle="collapseFilter" aria-controls="collapseFilter" aria-expanded="false">
                                <span v-text="title" class="mr-2"></span>
                                <svg sidebar-toggle-item="" class="ml-auto w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </p>
                        </div>
                    </div>
                    <div id="collapseFilter" :class="openClose =='open'? ' flex-auto p-5':'hidden flex-auto p-5'">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
