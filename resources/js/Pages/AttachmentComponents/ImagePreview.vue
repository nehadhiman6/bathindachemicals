
<script setup>
import { ref, onMounted, reactive, watchEffect } from 'vue';

import lightGallery from 'lightgallery';
import lgRotate from 'lightgallery/plugins/rotate';
import lgZoom from 'lightgallery/plugins/zoom';
import lgThumbnail from 'lightgallery/plugins/thumbnail';

const emit = defineEmits(['resetImageArr']);

// import 'lg-thumbnail';
const props = defineProps({
    imagesArr: { type: Array, default: [] },
    idLabel: { type: String, default: 'id' },
    path: String,
    pathThumbnail: String,
    type: { type: String, default: 'single' },
    initialId: { type: [String, Number], default: 0 },
    loop: { type: Boolean, default: true }
});

const state = reactive({
  data: [],
  startIndex: 0
});

onMounted(() => {
  watchEffect(() => {
    state.data = [];
    props.imagesArr.forEach((element, index) => {
        if (props.initialId > 0 && props.initialId == element[props.idLabel]) {
            state.startIndex = index;
        }
        if (element.attachment && element.attachment.mime_type == 'application/pdf') {
            state.data.push({

                src:"https://www.africau.edu/images/default/sample.pdf",
                // src: window[appname].base_url + '/' + props.path + '/' + element[props.idLabel],
                iframe: true,
                thumb: window[appname].base_url + '/' + props.pathThumbnail + '/' + element[props.idLabel],
                subHtml: `<a href="${window[appname].base_url}/${props.path}/${element[props.idLabel]}" target="_blank">${element.attachment.file_name}</a>`,
                downloadUrl: window[appname].base_url + '/' + props.path + '/' + element[props.idLabel],
            });
        } else {
            state.data.push({
                src: window[appname].base_url + '/' + props.pathThumbnail + '/' + element[props.idLabel],
                thumb: window[appname].base_url + '/' + props.pathThumbnail + '/' + element[props.idLabel],
                subHtml: `<a href="${window[appname].base_url}/${props.path}/${element[props.idLabel]}" target="_blank">${element.attachment.file_name}</a><br>`,
                downloadUrl: window[appname].base_url + '/' + props.path + '/' + element[props.idLabel]
            });
        }
    });

    launchGallery();
  });
});
const galleryContainer = ref(null);

const launchGallery = () => {
    // const lgContainer = document.getElementById('inline-gallery-container');
    const dynamicGallery = lightGallery($(this), {
         plugins:  [lgZoom, lgThumbnail, lgRotate],
        // container: lgContainer,
        thumbnail:true,
        licenseKey: 'A06A88F4-6D99-4522-BE6A-653CD9D44CF2',
        // videojs: true,
        dynamic: true,
        dynamicEl:state.data,
        galleryId:Utilities.getRandomNumber(),
        toggleThumb: true,
        addClass: 'iw-lightbox',
        escKey: true,
        share: false,
        loop: props.loop,
        index: state.startIndex,

    })

    dynamicGallery.openGallery(0);

};
</script>

<template>

  <div>
      <!-- Your gallery content goes here -->
    <!-- FOR image preview Only -->
      <!-- Your gallery content goes here -->
  </div>
</template>

<style type="module">
/* Your styles here */
/*
    @import 'lightgallery/css/lightgallery.css';
    @import 'lightgallery/css/lg-thumbnail.css';
    @import 'lightgallery/css/lg-zoom.css'; */

    @import 'lightgallery/css/lightgallery.css';
    @import 'lightgallery/css/lg-thumbnail.css';
    @import 'lightgallery/css/lg-zoom.css';
    @import 'lightgallery/css/lg-video.css';

   .lg-backdrop.in{
    opacity: 1;
   }

   .gallery-item {
  margin: 5px;
}
</style>
