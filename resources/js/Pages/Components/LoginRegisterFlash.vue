<template>
    <div v-if="$page.props.jetstream.flash.message" class="w-96 mt-10 -mb-10 mx-auto rounded-lg shadow-md border-2 border-red-700 bg-red-50 font-medium p-1 z-50" >
        <div class="flex items-center h-16 m-0 p-0">
            <div class="w-11/12 h-12">
                <p class="text-center text-red-700 pt-4">{{ $page.props.jetstream.flash.message }}</p>
            </div>
            <div @click="$page.props.jetstream.flash.message = ''" class="w-1/12 h-12 pl-3 -pt-8">
                <i class="fas fa-skull-crossbones animate-pulse cursor-pointer text-sm text-red-700"></i>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'LoginRegisterFlash'
}
</script>