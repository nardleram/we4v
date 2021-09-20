<template>
    <div class="w-11/12 mb-2 m-auto">
        <div class="md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')" enctype="multipart/form-data">
                <div class="px-3 py-4 bg-white sm:p-3 shadow rounded">
                    <div>
                        <slot name="form"></slot>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        emits: ['submitted'],
    }
</script>
