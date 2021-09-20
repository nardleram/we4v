<template>
    <div class="w-full relative pb-4">
        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" :for="name">{{label}}</label>
        <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-500 bg-we4vBg h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text"
        :id="name" :placeholder="placeholder" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)">
    </div>
</template>

<script>
export default {
    name: 'Input',

    props: [
        'name', 
        'label', 
        'placeholder',
        'modelValue'
    ],

    emits: ['update:modelValue'],
    
}
</script>

