<script setup>

import { computed, useSlots } from 'vue';
import TitleComponent from '@/Components/CustomComponents/Sections/TitleComponent.vue';

const props = defineProps({
    showFormHeader: {
        type: Boolean,
        default:false
    }
});
defineEmits(['submitted']);

const hasActions = computed(() => !! useSlots().actions);
</script>

<template>
    <TitleComponent v-if="props.showFormHeader == false">
        <template #title>
            <slot name="title" />
        </template>
        <template #right-content>
            <slot name="right-content" />
        </template>
    </TitleComponent>
    <div class="relative flex flex-col min-w-0 bg-white bg-clip-border border border-solid border-gray-300 rounded break-words">
        <div class="py-3 px-7 mb-0 bg-white border-b border-solid border-gray-300" v-if="props.showFormHeader">
            <strong> <slot name="title" /></strong>
        </div>
        <div class="p-5 flex-auto">
            <form class="w-full" @submit.prevent="$emit('submitted')">
                <slot name="form" />
            </form>
        </div>
        <div  v-if="hasActions" class="py-3 px-7 mb-0 bg-white border-t border-solid border-gray-300">
            <slot name="actions" />
        </div>
    </div>
</template>
