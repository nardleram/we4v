<template>
    <div class="mb-1 w-full flex items-center justify-start">
        <div class="w-2/3">
            <jet-nav-link :href="route('article-show', article.slug)" class="m-0">
                <h5 class="m-0 text-sm font-semibold text-we4vDarkBlue hover:underline">{{ article.title }}</h5>
            </jet-nav-link>
            <p class="text-we4vGrey-500 text-xs tracking-tight italic">{{ article.synopsis }}. Published on {{ article.created_at }}</p>
        </div>
        <div class="w-1/3 flex flex-wrap justify-center">
            <div v-for="(tag, tagKey) in article.tags" :key="tagKey">
                <div class="mr-1 mb-1 px-1 text-xs cursor-pointer text-we4vGrey-300 rounded-md border border-we4vGrey-200 bg-we4vBg hover:text-we4vGrey-500">
                    {{ tag.tag }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetNavLink from '@/Jetstream/NavLink'

export default {
    name: 'Article',

    components: {
        JetNavLink,
    },

    props: [
        'article',
    ],
}
</script>