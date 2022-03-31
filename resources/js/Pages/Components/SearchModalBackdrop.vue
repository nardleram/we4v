<template>
    <div id="backdrop" v-if="showSearchBackdrop" class="absolute bg-we4vModalBg top-16 right-1/4 w-1/2 h-screen z-40"></div>
</template>

<script>

export default {
    name: 'ModalBackdrop',

    props: [ 
        'showSearchBackdrop'
    ],
}
</script>