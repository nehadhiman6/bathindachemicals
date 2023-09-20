<script setup>
    import {    ref,  computed,  onMounted,    reactive} from 'vue';

    const props = defineProps({
        type: {
            type: String,
            default: 'button',
        },
        size: {
            type: String,
            default: 'md',
        },
    });
    const size_class = computed(() =>
         props.size == 'md' ? 'px-8 py-2'
        :props.size == 'sm' ? 'px-6 py-1'
        :props.size == 'lg'?'px-10 py-2':''
   );

    const classes = computed(() => props.type == 'new' ? 'inline-block '+size_class.value+' ml-auto font-bold leading-normal text-center text-white align-middle transition-all ease-in bg-secondary border-0 rounded-lg shadow-md cursor-pointer text-xs tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85'
        :props.type == 'save' ? 'inline-block '+size_class.value+' ml-auto font-bold leading-normal text-center text-white align-middle transition-all ease-in bg-primary border border-indigo-500 rounded-lg shadow-md cursor-pointer text-xs tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85'
        :props.type == 'cancel'?'inline-block '+size_class.value+' ml-2 font-bold leading-normal text-center text-gray-400 align-middle transition-all ease-in border border-gray-400 rounded-lg shadow-md cursor-pointer text-xs tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85'
        :''
    );
</script>

<template>
    <button  :class="classes" @click.prevent="$emit('buttonClicked')">
        <slot/>
    </button>
</template>
