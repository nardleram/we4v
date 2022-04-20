<template>
    <!-- @blur="$emit('checkIfUserMaySubmit')" -->
    <div class="w-full relative pb-4">
        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" :for="name">{{label}}</label>
        <input :id="id" class="w-full pl-4 pt-9 pb-4 shadow-sm bg-white h-8 rounded-full border-none focus:border-we4vBlue focus:ring-1 focus:ring-we4vBlue focus:shadow-md text-sm tracking-tight font-medium" :type="type" :placeholder="placeholder" v-model="modelValue" @input="$emit('updateModelValue', $event.target.value)" @blur="$emit('inputBlurFired')" >
    </div>
</template>

<script>
export default {
    name: 'Input',

    props: [
        'name', 
        'label', 
        'placeholder',
        'modelValue',
        'type',
        'id'
    ],

    emits: [
        'updateModelValue',
        'inputBlurFired'
    ],
    
}
</script>

