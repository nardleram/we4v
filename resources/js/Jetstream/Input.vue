<template>
    <input class="w-full pl-4 pt-9 pb-4 shadow-sm bg-white h-8 rounded-full border-none focus:border-we4vBlue focus:ring-1 focus:ring-we4vBlue focus:shadow-md text-sm tracking-tight font-medium" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input">
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

