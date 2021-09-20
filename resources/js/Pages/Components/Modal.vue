<template>
    <div id="backdrop" v-if="show" class="absolute bg-we4vModalBg top-16 right-1/4 w-1/2 h-screen z-40"></div>
    <slot></slot>
</template>

<script>

export default {
    name: 'Modal',

    props: [ 
        'show',
        'name',
        'id',
        'description'
    ],
}
</script>