<template>
    <error-message></error-message>

    <div class="p-1 bg-white rounded shadow mb-2">
        <div class="flex justify-between items-center">

            <div class="w-10">
                <img :src="'/storage/'+$page.props.userProfileImages.profile" class="rounded-full object-cover h-10 w-10" />
            </div>

            <div class="flex-1 flex mx-2">
                <textarea v-model="postMessage" name="body" class="w-full border border-we4vGrey-200 outline-none resize-none overflow-auto text-sm rounded-xl max-h-24 text-we4vGrey-600 tracking-tighter" rows="2" placeholder="Add a post to your Talkboard"></textarea>
                <!-- <input v-model="postMessage"
                type="text" name="body" class="w-full pl-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:border-we4vBlue focus:shadow-outline text-sm tracking-tighter" placeholder="Add a post to your Talkboard"> -->
                <transition name="fade">
                    <button v-if="postMessage"
                        @click="postHandler" 
                        class="text-we4vBlue ml-3">
                        <small class="dz-clickable"><i class="fas fa-save cursor-pointer text-xl text-we4vBlue"></i></small>
                    </button>
                </transition>
            </div>

            <div class="w-10">
                <button ref="postImage" class="ml-2" :class="{ 'ml-4' : !postMessage  }">
                    <small class="dz-clickable"><i class="far fa-image cursor-pointer text-xl text-we4vBlue"></i></small>
                </button>
            </div>

            <div class="dropzone-previews">
                <div id="dz-template" class="hidden">
                    <div class="dz-preview dz-file-preview mt-4">
                        <div class="dz-details">
                            <img data-dz-thumbnail class="w-32 h-32"/>
                            <button data-dz-remove class="text-xs rounded-lg text-we4vBlue bg-gray-200 py-1 px-2 tracking-tighter font-medium mt-2 border border-we4vBlue shadow-xs cursor-pointer m-auto">REMOVE</button>
                        </div>
                        <div class="dz-progress">
                            <span class="dz-upload" data-dz-upload></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>

<script>
import _ from 'lodash'
import Dropzone from 'dropzone'
import ErrorMessage from './ErrorMessage'

export default {
    name: 'NewPost',

    components: {
        ErrorMessage
    },

    data: () => {
        return {
            postMessage: null,
            dropzone: null
        }
    },

    mounted() {
        this.dropzone = new Dropzone(this.$refs.postImage, this.settings)
    },

    computed: {
        settings() {
            return {
                paramName: 'image',
                url: '/posts/store',
                acceptedFiles: 'image/*',
                clickable: '.dz-clickable',
                autoProcessQueue: false,
                maxFiles: 1,
                previewsContainer: '.dropzone-previews',
                previewTemplate: document.querySelector('#dz-template').innerHTML,
                // params: {
                //     'width': 1000,
                //     'height': 1000,
                // },
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
                },
                sending: (file, xhr, formData) => {
                    formData.append('body', this.postMessage)
                    formData.append('user_id', this.$page.props.authUser.id)
                },
                success: (event, res) => {
                    this.dropzone.removeAllFiles()
                    this.updatePosts(res)
                },
                error: (error) => {
                    this.$page.props.errors = { message: 'Images may be no larger than 2MB and must be one of jpg, jpeg, png, bmp, gif, svg, or webp'}
                },
                maxfilesexceeded: file => {
                    this.dropzone.removeAllFiles()
                    this.dropzone.addFile(file)
                }
            };
        },
    },

    methods: {
        postHandler() {
            let payload = {
                'body': this.postMessage,
                'user_id': this.$page.props.authUser.id,
            }
            if (this.dropzone.getAcceptedFiles().length) {
                this.dropzone.processQueue()
            } else {
                this.$inertia.post('/posts/store', payload, { preserveScroll: true })
            }
            this.postMessage = null
        },

        updatePosts(post) {
            this.$emit('updatePosts', post)
        },
    },
}
</script>

<style scoped>
    .fade-enter-active, .face-leave-active {
        transition: opacity .7s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0; 
    }
</style>