<template>
    <input class="w-full pl-4 py-4 shadow-sm bg-white h-8 rounded-full border-none focus:border-we4vBlue focus:ring-1 focus:ring-we4vBlue focus:shadow-md text-xs tracking-tight font-medium" :type="type" :id="id" :placeholder="placeholder" :value="modelValue" @input="$emit('updateModelValue', $event.target.value)" @blur="$emit('collectMemberRoles', $event.target)" />
</template>

<script>
export default {
    name: 'InputNoLabel',

    props: [
        'id', 
        'placeholder',
        'modelValue',
        'type'
    ],

    emits: [
        'updateModelValue',
        'collectMemberRoles',
    ],
    
}
</script>

