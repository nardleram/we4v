<template>
    <div class="w-2/3 p-1 bg-white rounded shadow">
        <div class="flex justify-between items-center">

            <div class="w-10">
                <img :src="authUser.data.attributes.profile_image.data.attributes.path" class="rounded-full object-cover h-10 w-10" />
            </div>

            <div class="flex-1 flex mx-2">
                <input v-model="postMessage"
                type="text" name="body" class="w-full pl-4 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tighter" placeholder="Add to your Talkboard">
                <transition name="fade">
                    <button v-if="postMessage"
                        @click="postHandler" 
                        class="bg-we4vGrey-100 rounded-full h-8 w-8 focus:outline-none focus:shadow-outline border border-we4vGrey-200 font-bold text-2xl text-we4vBlue ml-2 leading-2">
                        +
                    </button>
            
                </transition>
            </div>

            <div class="w-10">
                <button ref="postImage" class="dz-clickable text-xm tracking-tighter focus:outline-none focus:shadow-outline bg-we4vGrey-100 rounded-full h-10 w-10 border border-we4vGrey-200">
                    <small class="dz-clickable">IMG</small>
                </button>
            </div>

        </div>

    <div class="dropzone-previews">
        <div id="dz-template" class="hidden">
            <div class="dz-preview dz-file-preview mt-4">
                <div class="dz-details">
                    <img data-dz-thumbnail class="w-32 h-32"/>
                    <button data-dz-remove class="text-xs rounded-lg text-we4vBlue bg-gray-200 py-1 px-2 tracking-tighter font-medium mt-2 border border-we4vBlue shadow-xs cursor-pointer">REMOVE</button>
                </div>
                <div class="dz-progress">
                    <span class="dz-upload" data-dz-upload></span>
                </div>
            </div>
        </div>
    </div>

    </div>
</template>

<script>
import _ from 'lodash';
import { mapGetters } from 'vuex';
import Dropzone from 'dropzone';

export default {
    name: 'Newpost',

    data: () => {
        return {
            dropzone: null,
        }
    },

    mounted() {
        this.dropzone = new Dropzone(this.$refs.postImage, this.settings);
    },

    computed: {
        ...mapGetters({
            authUser: 'authUser',
        }),

        postMessage: {
            get() {
                return this.$store.getters.postMessage;
            },
            set: _.debounce(function (postMessage) {
                this.$store.commit('updateMessage', postMessage);
            }, 300),
        },

        settings() {
            return {
                paramName: 'image',
                url: '/api/posts',
                acceptedFiles: 'image/*',
                clickable: '.dz-clickable',
                autoProcessQueue: false,
                maxFiles: 1,
                previewsContainer: '.dropzone-previews',
                previewTemplate: document.querySelector('#dz-template').innerHTML,
                params: {
                    'width': 1000,
                    'height': 1000,
                },
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
                },
                sending: (file, xhr, formData) => {
                    formData.append('body', this.$store.getters.postMessage);
                },
                success: (event, res) => {
                    this.dropzone.removeAllFiles();
                    this.$store.commit('pushPost', res);
                },
                error: (error) => {
                    console.log('Something went wrong: ' + error);
                },
                maxfilesexceeded: file => {
                    this.dropzone.removeAllFiles();
                    this.dropzone.addFile(file);
                }
            };
        },
    },

    methods: {
        postHandler() {
            if (this.dropzone.getAcceptedFiles().length) {
                this.dropzone.processQueue();
            } else {
                this.$store.dispatch('postMessage');
            }
            this.$store.commit('updateMessage', '');
        }
    }
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