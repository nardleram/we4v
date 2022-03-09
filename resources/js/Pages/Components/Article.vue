<template>
    <div class="mb-1 flex flex-nowrap flex-row w-full">
        <jet-nav-link :href="route('article-show', article.slug)" class="w-3/12"><h4 class="text-sm font-semibold text-we4vDarkBlue hover:underline">{{ article.title }}</h4></jet-nav-link>
        <p class="w-9/12 text-we4vGrey-500">{{ article.synopsis }}</p>
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