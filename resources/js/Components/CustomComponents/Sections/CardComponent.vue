<script setup>
import { computed } from 'vue';
const props = defineProps({
    id:{
        default:'custom_cards'
    },
    cardClass:{
        default:'w-full sm:w-1/2 md:w-1/2 lg:w-1/4 px-4 mb-6'
    }
});

</script>
<template>
    <!-- project cards start -->
    <div :class="props.cardClass">
        <div class="relative rounded border bg-white">
            <div class="px-6 py-4">
                <slot name="card-data">
                </slot>
            </div>
        </div>
    </div>
    <!-- project cards end -->
</template>

<style>

</style>
