<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-we4vGreen border rounded-md font-semibold text-xs text-we4vGrey-100 uppercase tracking-widest focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-150">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    }
</script>
