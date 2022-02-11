<template>
    <button :type="type" class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4">
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

        }
    }
</script>
