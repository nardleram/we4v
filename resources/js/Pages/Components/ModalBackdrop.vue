<template>
    <div id="backdrop" v-if="show" class="absolute bg-we4vModalBg right-1/4 w-1/2 h-backdrop z-40"></div>
</template>

<script>

export default {
    name: 'ModalBackdrop',

    props: [ 
        'show'
    ],
}
</script>