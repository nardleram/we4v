<template>
    <button :type="type" class="text-we4vGrey-100 bg-we4vBlue hover:bg-we4vDarkBlue hover:shadow-md font-bold text-sm tracking-tight flex justify-center rounded-lg w-full focus:outline-none transition ease-in-out duration-150 py-2 mr-1 my-4">
        <slot class="text-center"></slot>
    </button>
</template>

<script>
    export default {
        name: 'ButtonBlue',

        props: {
            type: {
                type: String,
                default: 'submit',
            },
        }
    }
</script>
