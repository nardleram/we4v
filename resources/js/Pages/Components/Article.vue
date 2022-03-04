<template>
    <div class="mb-2">
        <h4 class="text-sm font-semibold text-we4vGrey-600">{{ article.title }}</h4>
        <p>{{ article.synopsis }}</p>
        <jet-nav-link :href="route('article-show', article.slug)"><h3>Read article</h3></jet-nav-link>
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