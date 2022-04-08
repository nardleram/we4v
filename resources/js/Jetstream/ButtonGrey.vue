<template>
    <button :type="type" class="border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4" :class="[ enabled ? 'cursor-pointer text-we4vGrey-600 hover:bg-we4vGrey-100' : 'text-we4vGrey-200 cursor-not-allowed']">
        <slot class="text-center"></slot>
    </button>
</template>

<script>
    export default {
        name: 'ButtonGrey',

        props: {
            type: {
                type: String,
                default: 'submit',
            },
            enabled: {
                type: Boolean,
                default: false
            }
        },
    }
</script>
