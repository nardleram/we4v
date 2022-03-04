<template>
    <flash-message></flash-message>
    <error-message></error-message>

    <app-layout>
        <template #centre>
            <div class="w-1/2 p-2 tracking-tight">
                <Title>
                    <template #title>
                        My articles
                    </template>
                </Title>

                <Subtitle>
                    <template #title>
                        Write an article
                    </template>
                </Subtitle>

                <Form>
                    <template #form>
                        <div class="mb-5">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="title">title<span class="text-red-600">*</span></label>
                            <input v-model="title" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" placeholder="Article title">
                        </div>

                        <div class="mb-5">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">synopsis<span class="text-red-600">*</span></label>
                            <input v-model="synopsis" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" placeholder="Article synopsis">
                        </div>

                        <div id="tags" class="mb-3">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="projectName">article tags (hit enter to add to your tag list)</label>
                            <input @keydown.enter.prevent="addArticleTag" v-model="articleTag" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text">
                        </div>

                        <h5 v-if="articleTags.length > 0" class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight ml-4">List of tags (click tag to remove any unwanted tag)</h5>
                        <div id="articleTags" class="flex flex-row w-full mb-2 ml-4">
                            <div v-for="tag in articleTags" :key="tag" class="cursor-pointer mr-1">
                                <p @click="deleteTag(tag)" class="text-we4vBlue text-xs font-semibold py-1 px-2 bg-we4vGrey-100 border border-we4vGrey-200 rounded-md">{{ tag }}</p>
                            </div>
                        </div>

                        <Menubar v-if="editor" :editor="editor"/>

                        <editor-content id="editorDiv" :editor="editor" class="max-h-80 overflow-y-scroll mb-2 border border-we4vGrey-200 p-2 focus: "/>

                        <button-blue @click="submitArticle">Save article</button-blue>
                    </template>
                </Form>

                <Subtitle>
                    <template #title>
                        My published articles
                    </template>
                </Subtitle> 

                <div v-if="myArticles.length > 0" class="w-full m-0 m-auto">
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
import ButtonBlue from '../Jetstream/ButtonBlue'
import Form from '@/Jetstream/FormSection'
import Menubar from '../Pages/Components/MenuBar'
import { watch, ref, onMounted, onBeforeUnmount } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'
import Article from '../Pages/Components/Article'
import { Editor, EditorContent } from '@tiptap/vue-3'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import Blockquote from '@tiptap/extension-blockquote'
import Heading from '@tiptap/extension-heading'
import Strike from '@tiptap/extension-strike'
import BulletList from '@tiptap/extension-bullet-list'
import OrderedList from '@tiptap/extension-ordered-list'
import ListItem from '@tiptap/extension-list-item'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'

export default {
    name: 'MyArticles',

    components: {
        AppLayout,
        EditorContent,
        Title,
        Subtitle,
        ButtonBlue,
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
        const error = ref(false)
        const flashMessage = ref(false)
        const articleTag = ref(null)
        const articleTags = ref([])
        const editor = ref(null)
        const title = ref('')
        const synopsis = ref('')

        onMounted(() => {
            editor.value = new Editor({
                content: '<h2>Article subhead could go here (if needed)</h2><blockquote>Perhaps a quote to lend your work an air of gravitas?</blockquote><p>The body of your article might continue here and be far more engaging than this dummy text...</p><p>(Go right ahead! Delete all this dummy text and compose your own!)</p>',
                extensions: [
                    Document,
                    Paragraph,
                    Text,
                    Heading,
                    Strike,
                    ListItem,
                    OrderedList,
                    BulletList,
                    Blockquote,
                    Bold,
                    Italic
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
                editor.value = null
                editor.value = new Editor({
                    content: '<h2>Article subhead could go here (if needed)</h2><blockquote>Perhaps a quote to lend your work an air of gravitas?</blockquote><p>The body of your article might continue here and be far more engaging than this dummy text...</p><p>(Go right ahead! Delete all this dummy text and compose your own!)</p>',
                    extensions: [
                        Document,
                        Paragraph,
                        Text,
                        Heading,
                        Strike,
                        ListItem,
                        OrderedList,
                        BulletList,
                        Blockquote,
                        Bold,
                        Italic
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
            }, 2500)
        })

        watch(flashMessage, () => {
            setTimeout(() => {
                usePage().props.value.jetstream.flash.message = ''
            }, 2500 )
        })

        return {
            articleTags,
            articleTag,
            addArticleTag,
            synopsis,
            deleteTag,
            title,
            editor,
            submitArticle,
        }
    }
    
}
</script>

<style>
li > p {
    display: inline;
}
.ProseMirror:focus,textarea:focus { 
    outline: none;
}
</style>