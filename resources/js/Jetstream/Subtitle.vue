<template>
    <div class="md:col-span-1 mb-4 mt-3">
        <div class="px-2 sm:px-0">
            <h3>
                <slot name="title"></slot>
            </h3>
            
            <p class="mt-1 text-sm text-we4vGrey-500 mb-4 tracking-tight">
                <slot name="description"></slot>
            </p>
        </div>
    </div>
</template>
