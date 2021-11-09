<template>
    <input class="w-full pl-4 text-we4vGrey-600 border border-we4vGrey-300 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:border-we4vBlue focus:shadow-outline text-sm tracking-tight my-2" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
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

