<template>
    <div class="absolute w-96 top-20 right-24 text-sm rounded-lg shadow-md bg-we4vModalBg text-we4vOrange font-medium p-4" v-if="$page.props.jetstream.flash.message">
        <div @click="$page.props.jetstream.flash.message = ''">
            <i class="fas fa-skull-crossbones animate-pulse cursor-pointer text-sm text-we4vBlue absolute top-2 right-2"></i>
        </div> 
        <p class="text-center">{{ $page.props.jetstream.flash.message }}</p>
    </div>
</template>

<script>
export default {
    name: 'FlashMessage'
}
</script>