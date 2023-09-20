
<script setup>
    import {    ref,  computed,  onMounted,    reactive} from 'vue';
    import globalMixin from '../../globalMixin';
    const { base_url} = globalMixin();
    //FILE POND

    // for File uploads
    import vueFilePond from 'vue-filepond';
    import 'filepond/dist/filepond.min.css';

    // image preview in file pond
    import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
    import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';


    import FilePondPluginFilePoster from 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.esm.js';
    import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.min.css';

    // file size validations
    import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.esm.js';
    import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
     const FilePond = vueFilePond(
        FilePondPluginImagePreview,
        FilePondPluginFilePoster,
        FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType
    );
    const props = defineProps(
    {
        instantUpload:{
            type:Boolean,
            default:true  //how upload should save  on server intsantly true for immediately on  upload false for user click on upload on file
        },
        stylePanelLayout:{
            type:[String,Object],
            default:null // can be: compact, compact integrated, circle, compact circle
        },
        maxFileSize:{
            default:'50MB'  //size of the file
        },
        labelMaxFileSize:{
            type:String,
            default:'maximum 50MB'  // label for file is size
        },
        labelMaxFileSizeExceeded:{
            type:String,
            default:'Exceed Size'  // label for file is exceedeing
        },
        resource:{              // one file in array
            type:[String,Object],
            default:null
        },
        index:{             // index of our attachments array
            type:[String,Number],
            default:-1
        },
        oneByOneServerUpload:{
            type:[Boolean],
            default:true
        },
        editable:{
            type:[Boolean],
            default:true
        },
        path:{
            type:[String],
            default:'attachments'
        },
        pathThumbnail:{        //Number of maximum attachments
            type:[String],
            default:'attachments-thumbnail'
        },
    });

    //DATA
    const data = reactive({
        token:$('meta[name="csrf-token"]').attr('content'),
        size:100,
        maxheight:20,
        showBlockUI:false
    });
    const emit = defineEmits(['setSelectedFile']);
    //COMPUTED
    const initialFile = computed(() => {
        const arr = [];
        if (props.resource.id > 0) {
            arr.push({
            source: `${base_url.value}/${props.pathThumbnail}/${props.resource.attachment_id}`,
            options: {
                type: 'local',
                file: {
                    name: props.resource.attachment ? props.resource.attachment.file_name : '',
                    type: props.resource.attachment ? props.resource.attachment.mime_type : '',
                    serverId: props.resource.attachment_id,
                },
                metadata: {
                    poster: `${base_url.value}/${props.pathThumbnail}/${props.resource.attachment_id}`,
                },
            },
            });
        }
        return arr;
    });


    //METHODs
    const getFiles = () => {
        return  $refs['pond'].getFiles();
    };

    const onResponse = (response) => {
        if (response) {
            const parsedResponse = JSON.parse(response);
            props.resource.attachment_id = parsedResponse.attachment.id;
            props.resource.attachment = parsedResponse.attachment;
            return parsedResponse.attachment.id;
        }
    };

    const removeFile = (file) => {
        console.log(file);
    };

    const fileUploadStarted = ()=>{
        //BLOCK UI
    }

    const saveAttachments = () => {
    return;
    const self = this;
    const files = getFiles();
    const fileArr = [];

    files.forEach((element) => {
        if (isNaN(element.serverId)) {
        if (element.file.serverId) {
            fileArr.push(element.file.serverId);
        }
        } else {
        fileArr.push(element.serverId);
        }
    });

    self.$emit('updateFiles', fileArr);
    };

    const onActivateFile = (file) => {
    if (isNaN(file.serverId)) {
        if (file.file.serverId) {
            emit('setSelectedFile', file.file.serverId);
        }
        } else {
            emit('setSelectedFile', file.serverId);
        }
    };
</script>
<template>
    <FilePond :key="resource.random"
        ref="pond"
        class="resource-modal-filepond "
        v-bind:server="{
            url: base_url+'/',
            timeout: 7000,
            process: {
                url: props.path,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':data.token
                },
                withCredentials: false,
                onload: onResponse
            },
            revert: {
                url: props.path+'/'+props.resource.attachment_id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN':data.token
                },
                withCredentials: false,
                onload: onResponse,
            },
        //   revert: '/attachments/'+form.attachment_id // or null
            //revert: null
        }"

        :allow-replace="props.editable"
        :files="initialFile"
        :image-preview-max-height="data.maxheight"
        :image-preview-height='data.size'
        :file-poster-height ='data.size'
        :max-file-size ="maxFileSize"
        :instant-upload ="props.instantUpload"
        :style-panel-layout="props.stylePanelLayout"
        :label-max-file-size="props.labelMaxFileSize"
        :label-max-file-size-exceeded="props.labelMaxFileSizeExceeded"
        @addfile ="saveAttachments"
        @removefile ="removeFile"
        @activatefile="onActivateFile"
        @addfilestart="fileUploadStarted"
        >
    </FilePond>
</template>

<style>
.resource-modal-filepond .filepond--root {
    width: auto;
}

.filepond--root .filepond--credits[style]{
    display: none;
}

</style>
