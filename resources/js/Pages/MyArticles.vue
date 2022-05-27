<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>

    <app-layout>
        <template #centre>
            <teleport to="#articleModal">
                <div v-if="showUploadImageModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-40 left-1/4 w-1/2 max-h-600 overflow-y-scroll m-auto rounded-md p-6">
                    <Form>
                        <template #form>
                            <div class="flex justify-end">
                                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                    <div @click="clearModal(); showUploadImageModal = false">
                                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                    </div>   
                                </div>
                            </div>

                            <h4 class="text-we4vBlue font-semibold mb-4 -mt-8">Select and upload an image for this article</h4>

                            <div class="w-full">
                                <label class="pl-4 text-we4vBlue text-xs font-medium tracking-tight" for="articleImage">select image</label>
                                <input multiple class="w-full pl-4 py-2 text-we4vGrey-600 bg-we4vGrey-100 h-10 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="file" id="articleImage">
                            </div>

                            <button-grey v-if="!isLoading" @click="addImage()" :type="'submit'" id="submitForm" :enabled="true" :loading="isLoading">
                                Upload image
                            </button-grey>
                            <button-grey v-if="isLoading" :type="'submit'" id="submitForm" :enabled="false" :loading="isLoading">
                                <span>Processing request...</span>
                                <span>
                                    <i class="fas fa-hourglass-half text-we4vOrange animate-pulse ml-2"></i>
                                </span>
                            </button-grey>
                            
                        </template>
                    </Form>
                </div>
            </teleport>

            <div class="w-1/2 p-3 ml-1/4 tracking-tight">
                <Title>
                    <template #title>
                        Articles
                    </template>
                </Title>

                <Subtitle>
                    <template #title>
                        Compose a new article
                    </template>
                </Subtitle>

                <Form>
                    <template #form>
                        <div class="mb-3 -mt-3">
                            <label class="pl-4 text-we4vBlue text-xs font-medium tracking-tight" for="title">Title<span class="text-red-600">*</span></label>
                            <input v-model="title" class="w-full pl-4 py-5 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" placeholder="Article title">
                        </div>

                        <div class="mb-3">
                            <label class="pl-4 text-we4vBlue text-xs font-medium tracking-tight" for="groupName">Synopsis<span class="text-red-600">*</span></label>
                            <input v-model="synopsis" class="w-full pl-4 py-5 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" placeholder="Article synopsis">
                        </div>

                        <div id="tags" class="mb-5">
                            <label class="pl-4 text-we4vBlue text-xs font-medium tracking-tight" for="projectName">Article tags (hit enter to add to your tag list)</label>
                            <input @keydown.enter.prevent="addArticleTag" v-model="articleTag" class="w-full pl-4 py-5 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text">
                        </div>

                        <h5 v-if="articleTags.length > 0" class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight ml-4">List of tags (click tag to remove it)</h5>
                        <div id="articleTags" class="flex flex-row w-full mb-2 ml-4">
                            <div v-for="tag in articleTags" :key="tag" class="cursor-pointer mr-1">
                                <p @click="deleteTag(tag)" class="text-we4vBlue text-xs font-semibold py-1 px-2 bg-we4vGrey-100 border border-we4vGrey-200 rounded-md">{{ tag }}</p>
                            </div>
                        </div>

                        <Menubar @show-add-image-modal="onShowAddImageModal" v-if="editor" :editor="editor"/>

                        <editor-content id="editorDiv" :editor="editor" class="max-h-80 overflow-y-scroll mb-2 border border-we4vGrey-200 p-2"/>

                        <button-grey :enabled="true" @click="submitArticle">Save article</button-grey>
                    </template>
                </Form>

                <div v-if="myArticles.length > 0" class="w-full m-0">
                    <h4>My published articles</h4>

                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Article v-for="(article, articleKey) in myArticles" :key="articleKey" :article="article" />
                    </div>
                </div>
            </div>

        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Title from '@/Jetstream/SectionTitle'
import Subtitle from '@/Jetstream/Subtitle'
import ButtonGrey from '../Jetstream/ButtonGrey'
import ModalBackdrop from '../Pages/Components/ModalBackdrop'
import Form from '@/Jetstream/FormSection'
import Menubar from '../Pages/Components/MenuBar'
import manageModals from '../Pages/Composables/manageModals'
import { watch, ref, onMounted, onBeforeUnmount } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'
import Article from '../Pages/Components/Article'
import { Editor, EditorContent } from '@tiptap/vue-3'
import Document from '@tiptap/extension-document'
import Text from '@tiptap/extension-text'
import Blockquote from '@tiptap/extension-blockquote'
import Heading from '@tiptap/extension-heading'
import Strike from '@tiptap/extension-strike'
import BulletList from '@tiptap/extension-bullet-list'
import OrderedList from '@tiptap/extension-ordered-list'
import ListItem from '@tiptap/extension-list-item'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'
import CustomImage from './Composables/customImage'
import CustomParagraph from './Composables/customParagraph'

export default {
    name: 'MyArticles',

    components: {
        AppLayout,
        ModalBackdrop,
        EditorContent,
        Title,
        Subtitle,
        ButtonGrey,
        Form,
        Menubar,
        Article,
        FlashMessage,
        ErrorMessage,
    },

    props: [
        'myArticles',
        'errors'
    ],

    setup(props) {
        const {
            amOutside, 
            amInside,
            clearModal,
            edit,
            mode,
            nowInside, 
            nowOutside,
            showBackdrop,
        } = manageModals()

        const error = ref(false)
        const flashMessage = ref(false)
        const articleTag = ref(null)
        const articleTags = ref([])
        const editor = ref(null)
        const title = ref('')
        const synopsis = ref('')
        const showUploadImageModal = ref(false)
        const isLoading = ref(false)

        const onShowAddImageModal = () => {
            showBackdrop.value = true
            showUploadImageModal.value = true
        }

        const addImage = async () => {
            isLoading.value = true

            if (!(articleImage.files[0])) {
                isLoading.value = false
                showUploadImageModal.value = false
                error.value = true
                props.errors = { message: 'No image file selected' }
                clearModal()
                return
            }

            let payload = {
                'image': articleImage.files[0]
            }

            await Inertia.post('/images/article/store', payload)
            .then(() => {
                isLoading.value = false
                showUploadImageModal.value = false
                editor.value.chain().focus().setImage({ src: usePage().props.value.articleImageData.articleImagePath }).run()
                usePage().props.value.articleImageData.articleImagePath = ''
            })
            .catch((err) => {
                console.log(err)
                isLoading.value = false
                showUploadImageModal.value = false
            })

            clearModal()

        }

        onMounted(() => {
            editor.value = new Editor({
                content: '<h2>Article subhead could go here (if needed)</h2><blockquote>Perhaps a quote to lend your work an air of gravitas?</blockquote><p>The body of your article might continue here and be far more engaging than this dummy text...</p><p>(Go right ahead! Delete all this dummy text and compose your own!)</p>',
                extensions: [
                    Document,
                    CustomParagraph,
                    Text,
                    Heading,
                    Strike,
                    ListItem,
                    OrderedList,
                    BulletList,
                    Blockquote,
                    Bold,
                    Italic,
                    CustomImage,
                ],
            })
        })

        onBeforeUnmount(() => {
            editor.value.destroy()
        })

        const addArticleTag = () => {
            if (!articleTags.value.includes(articleTag.value)) {
                articleTags.value.push(articleTag.value)
            }
            articleTag.value = ''
        }

        const deleteTag = (el) => {
            let result = articleTags.value.filter(articleTag => articleTag !== el)

            articleTags.value = result
        }

        const submitArticle = async function () {
            let htmlBody = editor.value.getHTML()

            let payload = {
                'title': title.value,
                'synopsis': synopsis.value,
                'body': htmlBody,
                'tags': articleTags.value,
                'tagable_type': 'App\\Models\\Article',
                '_token': usePage().props.value.csrf_token
            }

            try {
                await Inertia.post('/articles/store', payload)
                flashMessage.value = true
                props.errors = null
                title.value = ''
                synopsis.value = ''
                articleTags.value = []
                editor.value.destroy()
                editor.value = new Editor({
                    content: '<h2>Article subhead could go here (if needed)</h2><blockquote>Perhaps a quote to lend your work an air of gravitas?</blockquote><p>The body of your article might continue here and be far more engaging than this dummy text...</p><p>(Go right ahead! Delete all this dummy text and compose your own!)</p>',
                    extensions: [
                        Document,
                        CustomParagraph,
                        Text,
                        Heading,
                        Strike,
                        ListItem,
                        OrderedList,
                        BulletList,
                        Blockquote,
                        Bold,
                        Italic,
                        CustomImage
                    ],
                })
            } catch (err) {
                error.value = true
                props.errors = err
            }
        }

        watch(error, () => {
            setTimeout(() => { 
                usePage().props.value.errors = {} 
                error.value = false 
            }, 7500)
        })

        watch(flashMessage, () => {
            setTimeout(() => {
                usePage().props.value.jetstream.flash.message = ''
            }, 2500 )
        })

        return {
            addImage,
            articleTags,
            articleTag,
            addArticleTag,
            synopsis,
            deleteTag,
            title,
            editor,
            isLoading,
            submitArticle,
            amOutside, 
            amInside,
            clearModal,
            edit,
            onShowAddImageModal,
            mode,
            nowInside, 
            nowOutside,
            showBackdrop,
            showUploadImageModal
        }
    }
    
}
</script>

<style scoped>
li > p {
    display: inline;
}
.ProseMirror:focus,textarea:focus { 
    outline: none;
}
.ProseMirror,img {
    max-width: 100%;
    height: auto;
    border: 1px silver solid;
    margin: 1em 0;
    padding: 0 6px;
}
.ProseMirror p {
  margin: 8px 0;
}
</style>