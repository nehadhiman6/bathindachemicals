<template>
    <button v-on:click="updateSlides">Add new image</button>
    <lightgallery
        :settings="{ speed: 500, plugins: plugins }"
        :onInit="onInit"
        :onBeforeSlide="onBeforeSlide"
    >
        <a
            v-for="item in items"
            :key="item.id"
            :data-lg-size="item.size"
            className="gallery-item"
            :data-src="item.src"
        >
            <img className="img-responsive" :src="item.thumb" />
        </a>
    </lightgallery>
</template>

<script>
import Lightgallery from 'lightgallery/vue';
import lgZoom from 'lightgallery/plugins/zoom';
let lightGallery = null;
export default {
    name: 'App',
    components: {
        Lightgallery,
    },
    watch: {
        items(newVal, oldVal) {
            this.$nextTick(() => {
                lightGallery.refresh();
            });
        },
    },
    data: () => ({
        plugins: [lgZoom],
         licenseKey: 'A06A88F4-6D99-4522-BE6A-653CD9D44CF2',
        items: [
            {
                id: '1',
                size: '1400-933',
                src:
                    'https://images.unsplash.com/photo-1542103749-8ef59b94f47e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1400&q=80',
                thumb:
                    'https://images.unsplash.com/photo-1542103749-8ef59b94f47e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=240&q=80',
                subHtml: `<div class="lightGallery-captions">
                <h4>Photo by <a href="https://unsplash.com/@dann">Dan</a></h4>
                <p>Published on November 13, 2018</p>
            </div>`,
            },
            {
                id: '2',
                size: '1400-933',
                src:
                    'https://images.unsplash.com/photo-1473876988266-ca0860a443b8?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1400&q=80',
                thumb:
                    'https://images.unsplash.com/photo-1473876988266-ca0860a443b8?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=240&q=80',
                subHtml: `<div class="lightGallery-captions">
                <h4>Photo by <a href="https://unsplash.com/@kylepyt">Kyle Peyton</a></h4>
                <p>Published on September 14, 2016</p>
            </div>`,
            },
            {
                id: '3',
                size: '1400-932',
                src:
                    'https://images.unsplash.com/photo-1588953936179-d2a4734c5490?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=1400&q=80',
                thumb:
                    'https://images.unsplash.com/photo-1588953936179-d2a4734c5490?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=240&q=80',
                subHtml: `<div class="lightGallery-captions">
                <h4>Photo by <a href="https://unsplash.com/@jxnsartstudio">Garrett Jackson</a></h4>
                <p>Published on May 8, 2020</p>
            </div>`,
            },
        ],
    }),
    methods: {
        onInit: (detail) => {
            lightGallery = detail.instance;
        },
        updateSlides: function () {
            this.items = [
                ...this.items,
                {
                    id: '4',
                    size: '1400-933',
                    src:
                        'https://images.unsplash.com/photo-1609902726285-00668009f004?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=1400&q=80',
                    thumb:
                        'https://images.unsplash.com/photo-1609902726285-00668009f004?ixlib=rb-1.2.1&ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&auto=format&fit=crop&w=240&q=80',
                    subHtml: `<div class="lightGallery-captions">
                <h4>Photo by <a href="https://unsplash.com/@bruno_adam">Bruno Adam</a></h4>
                <p>Published on January 6, 2021</p>
            </div>`,
                },
            ];
            lightGallery.refresh();
        },
        onBeforeSlide: () => {
            console.log('calling before slide');
        },
    },
};
</script>
<style lang="css">
@import url('https://cdn.jsdelivr.net/npm/lightgallery@2.1.0-beta.1/css/lightgallery.css');
@import url('https://cdn.jsdelivr.net/npm/lightgallery@2.1.0-beta.1/css/lg-zoom.css');
body {
    margin: 0;
}
.gallery-item {
  margin: 5px
}
</style>
