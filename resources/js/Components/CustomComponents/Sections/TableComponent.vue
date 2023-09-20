<script setup>
import { computed } from 'vue';
const props = defineProps({
    id:{
        default:'custom_table'
    },
});

</script>
<template>
    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
        <div class="relative flex flex-col min-w-0 bg-white bg-clip-border border border-solid border-gray-300 rounded break-words">
            <div class="p-5 flex-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" :id="props.id">
                    <slot name="thead">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        </thead>
                    </slot>
                    <slot/>
                    <slot name ="tfoot">
                    </slot>
                </table>
            </div>
        </div>

    </div>
</template>
