
<script setup>
defineProps({
    user: {
        type: Object,
    },
});
</script>
<template>
  <div id="tooltip-jese" role="tooltip" class="inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 transition-opacity duration-300 tooltip dark:bg-gray-700 border">
      Jese Leos
      <div class="tooltip-arrow" data-popper-arrow></div>
  </div>
    <img class="h-8 w-8 rounded-full object-cover mr-2" :src="user.profile_photo_url" :alt="user.name" data-tooltip-target="tooltip-jese"
    >

</template>

<style>

</style>
