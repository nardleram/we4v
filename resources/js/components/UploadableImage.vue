<template>
    <div>
        <img
        :alt="alt"
        :class="classes"
        ref="userImage"
        :src="userImage.data.attributes.path">
    </div>
</template>

<script>
import Dropzone from 'dropzone';
import { mapGetters } from 'vuex';

export default {
    name: "UploadableImage",

    props: [
        'userImage',
        'imageWidth',
        'imageHeight',
        'pagePosition',
        'classes',
        'alt',
    ],

    data: () => {
        return {
            dropzone: null,
        }
    },

    mounted() {
        if (this.authUser.data.user_id.toString() === this.$route.params.userId) {
            this.dropzone = new Dropzone(this.$refs.userImage, this.settings);
        }
    },

    computed: {
        settings() {
            return {
                paramName: 'image',
                url: '/api/user-images',
                acceptedFiles: 'image/*',
                params: {
                    'width': this.imageWidth,
                    'height': this.imageHeight,
                    'pagePosition': this.pagePosition,
                },
                headers: {
                    'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
                },
                success: (e, res) => {
                    this.uploadedImage = res;
                    this.$store.dispatch('fetchAuthUser');
                    this.$store.dispatch('fetchUser', this.$route.params.userId);
                    this.$store.dispatch('fetchUserPosts', this.$route.params.userId);
                }
            };
        },

        ...mapGetters({
            authUser: 'authUser'
        })
    },
}
</script>