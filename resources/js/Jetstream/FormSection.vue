<template>
    <div class="w-full m-auto">
        <form @submit.prevent="$emit('submitted')" enctype="multipart/form-data">
            <slot name="form"></slot>
        </form>
    </div>
</template>

<script>
    export default {
        emits: ['submitted'],
    }
</script>
