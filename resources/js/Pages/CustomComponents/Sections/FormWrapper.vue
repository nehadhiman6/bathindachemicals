<script setup>
   const props = defineProps(['title']);
</script>
<template>
    <div class="w-full px-6 pb-4 mx-auto form">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 shrink-0 md:w-full md:flex-0">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl rounded-2xl bg-clip-border">
                    <div class="border-black/10 rounded-t-2xl border-b border-solid px-5 py-3">
                        <div class="flex items-center">
                            <p class="leading-normal uppercase text-sm font-semibold" v-text="title"> </p>
                        </div>
                    </div>
                    <div class="flex-auto px-5 py-3">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
