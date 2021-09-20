<template>
    <input class="w-full pl-4 text-we4vGrey-700 font-medium bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:border-we4vBlue focus:shadow-outline text-sm tracking-tighter my-6" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
</template>

<script>
    export default {
        props: ['modelValue'],

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    }
</script>

