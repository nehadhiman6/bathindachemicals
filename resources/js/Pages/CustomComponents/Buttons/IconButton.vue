<script setup>
    import {    ref,  computed,  onMounted,    reactive} from 'vue';

    const props = defineProps({
        icon: {
            type: String,
            default: 'fa fa-edit',
        },

    });

    const classes = computed(() => props.icon);
</script>

<template>

    <i  :class="classes" @click.prevent="$emit('buttonClicked')">
    </i>
        <slot/>
</template>
