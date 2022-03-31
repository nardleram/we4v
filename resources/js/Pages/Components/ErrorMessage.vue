<template>
    <div class="absolute w-96 top-20 right-24 text-sm rounded-lg border border-red-600 shadow-md z-50 bg-we4vModalBg p-4" v-if="Object.entries($page.props.errors).length > 0">
        <div @click="$page.props.errors = {}">
            <i class="fas fa-skull-crossbones animate-pulse cursor-pointer text-sm text-we4vBlue absolute top-2 right-2"></i>
        </div> 
        <div v-for="(message, messageKey) in $page.props.errors" :key="messageKey">
            <p class="text-center text-red-500 font-medium">{{ message }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ErrorMessage'
}
</script>