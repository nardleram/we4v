<template>
    <app-layout>
        <template #centre>

            <div class="w-1/2 p-3 flex flex-col max-h-screen overflow-x-hidden">
                <h1 class="text-3xl">{{ article[0].title }}</h1>
                <small class="text-xs text-we4vGrey-500 italic mb-2">By <span class="font-semibold">{{ article[0].name }} {{ article[0].surname }}</span>, published {{ article[0].created_at }}</small>
                
                <div class="flex flex-row items-start justify-between">
                    <div class="w-16 mr-5">
                        <inertia-link :href="route('user-show', article[0].userslug)" as="button">
                            <img class="h-12 w-12 rounded-full object-cover mb-3 cursor-pointer hover:shadow-md" :src="'/'+article[0].path" :alt="article[0].name + ' ' + article[0].surname" />
                        </inertia-link>
                    </div>

                    <div v-if="article[0]['tags'].length > 0" class="mr-5 w-6/12">
                        <h5 class="text-we4vDarkBlue font-semibold text-sm">Article tags</h5>
                        <div class="0 flex flex-row flex-wrap">
                            <div v-for="(tag, tagKey) in article[0].tags" :key="tagKey">
                                <small class="text-xs py-0 m-0 text-we4vGrey-500 mr-2 cursor-pointer hover:text-we4vGrey-600">#{{ tag.tag }}</small>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5 class="text-we4vDarkBlue font-semibold text-xs">{{ article[0].num_approvals }} approvals, {{ article[0]['comments'].length }} comments</h5>
                    </div>
                </div>

                <div v-html="article[0].body" class="mb-2"></div>
                <hr />

                <button :class="[article[0].user_approves ? 'text-we4vBlue bg-we4vGrey-900 border-we4vBlue' : 'text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300']" class="font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 mt-4" @click="storeApproval(article[0].id)">
                    <span v-if="article[0].user_approves">Approved</span>
                    <span v-else>Approve</span>
                </button>

                <Subtitle>
                    <template #title>
                        Respond to <span class="italic text-we4vGrey-700">{{ article[0].title }}</span>
                    </template>
                </Subtitle>

                <div v-if="!reply" class="w-full">
                    <Menubar v-if="editor" :editor="editor"/>
                    <editor-content :editor="editor" class="max-h-80 overflow-y-scroll mb-2 border border-we4vGrey-200 p-2"/>
                    <button-grey @click="submitComment">Save comment</button-grey>
                </div>

                <div v-if="article[0]['comments'].length > 0">

                    <h5 class="font-semibold mb-2 text-we4vGrey-700 tracking-tight">Comments</h5>
                    
                    <div class="w-full m-0 flex flex-row flex-wrap">
                        <div v-for="(comment, commentKey) in article[0].comments" :key="commentKey" class="w-full">
                            <div class="flex w-full flex-row flex-wrap" :class="'p-indent-'+comment.indent_level">
                                <div class="w-1/12">
                                    <inertia-link :href="route('user-show', comment.author_slug)" as="button">
                                        <img :src="'/'+comment.comment_path" :alt="comment.comment_author" class="rounded-full w-8 h-8 object-cover">
                                    </inertia-link>
                                </div>
                                <div class="mb-3 w-11/12 tracking-tight -mt-2">
                                    <div v-html="comment.body" class="mb-1 text-we4vComment text-we4vGrey-700"></div>
                                    <div class="flex flex-1 justify-between w-full">
                                        <div class="text-gray-400 tracking-tight text-xs w-10/12">{{ comment.comment_author }}, {{ comment.commented_at }} <span v-if="comment.indent_level > 0">, reply to {{ comment.reply_to }}, {{ comment.parent_created_at }}</span></div>
                                        <div 
                                            @click="reply = true;
                                                commentId = comment.id; 
                                                indentLevel = comment.indent_level; 
                                                newEditor('<p>Reply to '+comment.comment_author+'</p>')" 
                                            class="text-xs text-we4vGrey-500 cursor-pointer w-2/12 text-right pr-2">
                                            <span class="text-we4vBlue hover:text-we4vDarkBlue">Reply</span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="reply && commentId === comment.id" class="w-full">
                                    <Menubar v-if="editor" :editor="editor"/>
                                    <editor-content :editor="editor" class="max-h-80 overflow-y-scroll mb-2 border border-we4vGrey-200 p-2"/>
                                    <button-grey @click="submitComment">Save comment</button-grey>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Subtitle from '@/Jetstream/Subtitle'
import Menubar from '../Pages/Components/MenuBar'
import ButtonGrey from '../Jetstream/ButtonGrey'
import { watch, ref, onMounted, onBeforeUnmount } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
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
    name: 'ArticleShow',

    components: {
        AppLayout,
        ButtonGrey,
        Subtitle,
        EditorContent,
        Menubar,
    },

    props: [
        'article',
        'errors'
    ],

    setup(props) {
        const error = ref(false)
        const flashMessage = ref(false)
        const editor = ref(null)
        const reply = ref(false)
        const commentId = ref(null)
        const indentLevel = ref(null)

        onMounted(() => {
            newEditor('<p>Enter comment here</p>')
        })

        onBeforeUnmount(() => {
            editor.value.destroy()
        })
        
        const newEditor = function (content) {
            editor.value ? editor.value.destroy() : null

            editor.value = new Editor({
                content: content,
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
        }

        const submitComment = async function () {
            let htmlBody = editor.value.getHTML()

            let payload = {
                'body': htmlBody,
                'commentable_id': props.article[0].id,
                'commentable_type': 'App\\Models\\Article',
                'parent_id': commentId.value ? commentId.value : props.article[0].id,
                'parent_type': reply.value ? 'App\\Models\\Comment': 'App\\Models\\Article',
                'indent_level': reply.value ? indentLevel.value + 1 : 0,
                '_token': usePage().props.value.csrf_token
            }

            try {
                await Inertia.post('/comments/store', payload)
                props.errors = null
                reply.value = false
                commentId.value = null
                newEditor('<p>Enter comment here</p>')
            } catch (err) {
                error.value = true
                props.errors = err
            }
        }

        const storeApproval = async function (id) {
            await Inertia.post('/approvals/store', {
                'id': id,
                'model': 'App\\Models\\Article'
            })
            if (props.article.user_approves) { // User is therefore disapproving
                props.article.user_approves = false;
                --props.article.num_approvals;
            } else {
                props.article.user_approves = true;
                ++props.article.num_approvals;
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
            editor,
            submitComment,
            storeApproval,
            reply,
            commentId,
            newEditor,
            indentLevel,
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