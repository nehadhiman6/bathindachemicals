
<template>
     <div :class="'border-b '+paddingClass "></div>
</template>

<script>
export default {
    props:{
        paddingClass:{
            default:'my-8'
        }
    }
}
</script>

<style>

</style>
