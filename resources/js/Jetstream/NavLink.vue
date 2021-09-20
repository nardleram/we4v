<template>
    <inertia-link :href="href" :class="classes">
        <slot></slot>
    </inertia-link>
</template>

<script>
    export default {
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                            ? 'inline-flex items-center text-xs font-medium leading-5 text-we4vOrange transition duration-150 ease-in-out'
                            : 'inline-flex items-center text-xs leading-5 text-white hover:text-we4vOrange transition duration-150 ease-in-out'
            }
        }
    }
</script>
