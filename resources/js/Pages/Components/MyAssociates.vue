<template>
    <div>
        <div v-for="(assoc, assocKey) in $page.props.myAssociates" :key="assocKey" class="w-full p-1 max-h-96 overflow-y-hidden">
            <div class="flex flex-row flex-nowrap items-center justify-between w-full rounded-md p-1 border-b border-we4vGrey-700">
                <div>
                    <inertia-link :href="route('user-posts', assoc.username)" as="button">
                        <img :src="'/'+assoc.path" alt="{{ assoc.username }}" class="rounded-full cursor-pointer w-8 h-8 object-cover">
                    </inertia-link>
                </div>
                <div class="text-xs text-we4vGrey-100 font-light">
                    {{ assoc.username }} ({{ assoc.name }} {{ assoc.surname }})
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MyAssociates',
}
</script>