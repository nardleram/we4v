<template>
    <app-layout>
        <template #centre>

            <div class="w-1/2 p-3 flex flex-col items-center max-h-screen overflow-x-hidden">
                <h1 v-html="article[0].title"></h1>
                <div v-html="article[0].body"></div>
            </div>

        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Title from '@/Jetstream/SectionTitle'

export default {
    name: 'ArticleShow',

    components: {
        AppLayout,
        Title,
    },

    props: [
        'article'
    ],
}
</script>