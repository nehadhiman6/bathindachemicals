
<script setup>
    import {    ref,  computed,  onMounted,    reactive, onUnmounted} from 'vue';
    import SelectInput from '@/Pages/CustomComponents/Forms/SelectInput.vue';
    import InputLabel from '@/Pages/CustomComponents/Forms/InputLabel.vue';
    import TextInput from '@/Pages/CustomComponents/Forms/TextInput.vue';
    import TextAreaInput from '@/Pages/CustomComponents/Forms/TextAreaInput.vue';
    import Pond from '@/Pages/AttachmentComponents/Pond.vue';
    import Modal from '@/Pages/CustomComponents/Sections/Modal.vue';
    import ButtonComp from '@/Pages/CustomComponents/Buttons/ButtonComp.vue';
    import IconButton from '@/Pages/CustomComponents/Buttons/IconButton.vue';
    import ImagePreview from '@/Pages/AttachmentComponents/ImagePreview.vue';
    import globalMixin from '../../globalMixin';
    const { base_url} = globalMixin();

    const emit = defineEmits(['updateResources','cancelPage','updateStatus']);

    const getAttachment = ()=>{
        return {
            id:0,
            attachment_id:0,
            doc_type:'0',
            doc_description:'',
            random:Utilities.getRandomNumber()
        }
    }
    const props = defineProps(
    {
        instantUpload:{                 // if you want to upload  to server/ attachment table immediately after upload
            type:Boolean,
            default:true
        },
        stylePanelLayout:{
            type:[String,Object],
            default:null           // can be: compact, compact integrated, circle, compact circle
        },
        maxFileSize:{
            default:'50MB'         // Maximum file size
        },
        labelMaxFileSize:{              // label for maximum filke size
            type:String,
            default:'maximum 50MB'
        },
        labelMaxFileSizeExceeded:{       // error to show when max file exceed
            type:String,
            default:'Exceed Size'
        },
        docTypes:{
            type:Array,                  // array of document Types with id and value object
            default:function(){
                return [];
            }
        },
        initialResources:{             // for edit case array of inital images just pass resources  array in prop
            type:Array,
            default:function(){
                return [];
            }
        },
        showDescription:{              // remarks textarea field visibility
            type:Boolean,
            default:true
        },
        showClose:{              // remarks textarea field visibility
            type:Boolean,
            default:true
        },
        numberAttachments:{        //Number of maximum attachments
            type:[Number,String],
            default:0
        },
        oneByOneServerUpload:{     // if you want to preview file thumbnail and only want to upload on server by upside aerrow click on file
            type:[Boolean],
            default:true
        },
        loopPreview:{            // if ypu want loop preview of images/ pdfs in all modal
            type:Boolean,       //loop means after last file first will be shown if false then it will stop to last image/pdf
            default:false
        },
        strictFixedNumbers:{      // strict number of attachments needed to upload
            type:Boolean,       // number of upload images would be shown initally without add button click
            default:false
        },
        saveFormOnSaveClicked:{      // this would not close modal on save clicked
            type:Boolean,       // modal will will be closed from parent after saving files so that we can run our logic to save attachments
            default:false       // immediately after save clicked in parent // can show validation errors on modal
        },
        editable:{      // this would not allow user  to delete or add new attachments
            type:Boolean,       // for view purpose only if set to false
            default:true       // false if you don't want the  user to add new or update existing attachments
        },
        errors:{      // error object
            type:Object,
            default:function(){
                return { 'errors':{}};
            }
        },
        cancelClicked:{      // this would not close modal on save clicked
            type:Boolean,       // modal will will be closed from parent after saving files so that we can run our logic to save attachments
            default:false       // immediately after save clicked in parent // can show validation errors on modal
        },
        path:{        //Number of maximum attachments
            type:[Number,String],
            default:'shared-attachments'
        },
        pathThumbnail:{        //Number of maximum attachments
            type:[Number,String],
            default:'shared-attachments-thumbnail'
        },
        showAddBtn:{              // remarks textarea field visibility
            type:Boolean,
            default:true
        },
        title:{
            type:String,
            default:' Attachment Upload Modal'
        },
        readOnly:{
            type:Boolean,
            default:false
        }

    });

    const data = reactive({
        token:$('meta[name="csrf-token"]').attr('content'),
        resources:[],
        previewImageId:0
    });

    onUnmounted(()=>{
        data.show= false;
    });

    onMounted(() => {
        data.show= true;
        data.resources =  JSON.parse(JSON.stringify(props.initialResources));
        var i = data.resources.length;
        if(data.resources.length == 0){
            do{
                data.resources.push(getAttachment());
                i++;
                if(props.strictFixedNumbers == false){
                    break;
                }
            }while(i < props.numberAttachments)
        }
    });

    const showAddButton = computed(() => {
         if(props.numberAttachments == 0)
                return true;
            if(props.strictFixedNumbers == true ||  data.resources.length >= props.numberAttachments)
                return false;
            else
                return true;
    });

    const modalSize = computed(() =>{
        if (columnSize.value > 10) {
            return '2xl';
        } else if (columnSize.value <= 10 && columnSize.value > 5) {
            return '3xl';
        } else if (columnSize.value <= 5) {
            return '4xl';
        }
    });

    const columnSize = computed(() =>{
        let cols = 12;
        if (props.numberAttachments == 0 || props.numberAttachments > 1) {
            cols = cols - 1;
        }
        if (props.docTypes.length > 0) {
            cols = cols - 3;
        }
        if (props.showDescription) {
            cols = cols - 6;
        }
        return cols;
    });

    const saveImages = () => {
        emit('updateResources',true,data.resources);
        if (!props.saveFormOnSaveClicked) {
            data.show = false;
        }
    }

    const backPage = () => {
        emit('cancelPage', true);
        if (!props.cancelClicked) {
            data.show = false;
        }
    };


    const addNewFile = () => {
        data.resources.push(getAttachment());
    };

    const removeResource = (index) => {
        data.resources.splice(index, 1);
    };

    const setSelectedFile = (id = 0) => {
        data.previewImageId = id;
    };

    const getRandomNumber = ()=>{
        return Utilities.getRandomNumber();
    }

</script>
<template>
<!-- For click on button type -->
    <Modal :show="data.show" :max-width="modalSize" :closeable="closeable" @close="close" >
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900" v-text="props.title">
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <div class="flex -mx-3">
                    <div class="w-full max-w-full px-3 md:w-1/12" v-if="props.numberAttachments != 1">
                        <div class="mb-2 text-center uppercase">
                            <InputLabel for="name" value="Sr. no." />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-3/12"  v-if="props.docTypes.length > 0">
                        <div class="mb-2 text-center uppercase">
                            <InputLabel for="name" value="Type" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-6/12" v-if="showDescription == true">
                        <div class="mb-2 text-center uppercase">
                            <InputLabel for="name" value="Description" />
                        </div>
                    </div>
                    <div :class="'w-full max-w-full px-3 md:w-'+columnSize+'/12'">
                        <div class="mb-2 text-center uppercase">
                            <InputLabel for="name" value="Attachment" />
                        </div>
                    </div>
                </div>
                <div class="flex -mx-3" v-for="(resource,index) in data.resources" :key="index">
                    <div class="w-full max-w-full px-3 md:w-1/12" v-if="props.numberAttachments != 1">
                        <div class="mb-4">
                            <InputLabel for="name" :value="index+1" />
                            <br>
                            <IconButton  v-if="!readOnly" @buttonClicked="removeResource(index)" icon="fa fa-trash ml-2 text-red-400 "> </IconButton>
                              <!-- <i class="mr-1 fas fa-trash text-red-400 edit-item ml-1" aria-hidden="true" @click="emit('changeInDetails','remove',props.index)"></i> -->
                        </div>
                    </div>
                     <div class="w-full max-w-full px-3 md:w-3/12 md:flex-0" v-if="props.docTypes.length > 0">
                        <div class="mb-4">
                            <SelectInput v-model="resource.doc_type" :options ="props.docTypes" :disabled="readOnly"
                             ></SelectInput>
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 md:w-6/12"  v-if="showDescription == true">
                        <div class="mb-4">
                            <TextAreaInput v-model="resource.doc_description" :disabled="readOnly"/>
                        </div>
                    </div>
                    <div :class="'w-full max-w-full px-3 md:w-'+columnSize+'/12'" v-if="readOnly == false">
                        <div class="">
                             <pond
                                :key="resource.random"
                                :index="index"
                                :resource="resource"
                                :editable="props.editable"
                                :path="props.path"
                                :path-thumbnail="props.pathThumbnail"
                                :instant-upload="props.instantUpload"
                                :style-panel-layout="props.stylePanelLayout"
                                :max-file-size="props.maxFileSize"
                                :label-max-file-size="props.labelMaxFileSize"
                                :label-max-file-size-exceeded="props.labelMaxFileSizeExceeded"
                                :initial-files="props.initialResources"
                                :one-by-one-server-upload ="oneByOneServerUpload"
                                :strict-fixed-numbers ="props.strictFixedNumbers"
                                @setSelectedFile="setSelectedFile"
                                >
                            </pond>
                        </div>
                    </div>
                    <div :class="'bg-[#f1f0ef] border-neutral-200 w-full max-w-full p-4 mb-4 md:w-'+columnSize+'/12'" v-if="readOnly == true">
                        <div class="rounded-lg bg-[#111] mx-4 cursor-pointer">
                            <img class="max-w-[162px] max-h-[100px] object-cover mx-auto" :src="base_url+'/'+props.pathThumbnail +'/'+resource.attachment.id"  @click="setSelectedFile(resource.attachment.id)"/>
                        </div>
                    </div>

                </div>
                <div class="flex -mx-3 " v-if="showAddButton && readOnly ==false">
                      <IconButton @buttonClicked="addNewFile" icon="fa fa-plus-square mr-2"> ADD </IconButton>
                </div>
            </div>
        </div>
        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <div class="">
                <ButtonComp @buttonClicked="saveImages" v-if="readOnly ==false" type="save">Save</ButtonComp>
                <ButtonComp @buttonClicked="emit('updateStatus')" type="cancel">
                    <span v-text="readOnly ? 'Close':'Cancel'"></span>
                </ButtonComp>
            </div>
        </div>
            <image-preview v-if="data.resources.length > 0 && data.previewImageId > 0"
                :imagesArr= "data.resources"
                :initial-id ="data.previewImageId"
                :path-thumbnail ="props.pathThumbnail"
                :path = "props.path"
                :loop = "props.loopPreview"
                idLabel = 'attachment_id'
                @resetImageArr="setSelectedFile"
                :key="getRandomNumber()">
            </image-preview>
    </Modal>
</template>
