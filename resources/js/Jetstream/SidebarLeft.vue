<template>
    <div class="w-1/4 h-screen p-4 bg-we4vGrey-800 text-we4vBg font-light text-sm">
        <Input :name="name" :label="label" :placeholder="placeholder" />
    </div>
</template>

<script>
import Input from '../Pages/Components/Input'

export default {
    name: 'SidebarLeft',

    components: {
        Input,
    },

    data: () => {
        return {
            name: 'search',
            label: 'Search site',
            placeholder: 'enter search string',
        }
    }
}
</script>