<template>
<div class="flex flex-row items-start mb-1">
    <div class="w-4 h-9 rounded-l-full bg-we4vGrey-900 text-l tracking-tight text-right text-sm border-b border-we4vDarkBlue">
    </div>
    <div class="h-9 w-full flex flex-row flex-wrap items-center justify-end bg-we4vGrey-900 -mr-2 pr-2 text-l tracking-tight text-right text-sm border-b border-we4vDarkBlue">
        <div class="mr-2">
            <slot name="title"></slot>
        </div>

        <div class="p-0 mt-tiny">
            <slot name="bell"></slot>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'PanelElement',

    props: [ 
        'active'
    ],
}
</script>