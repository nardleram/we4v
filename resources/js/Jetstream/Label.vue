<template>
    <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot></slot></span>
    </label>
</template>

<script>
    export default {
        props: ['value']
    }
</script>
