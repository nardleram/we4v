<template>
    <div class="w-full bg-we4vGrey-900 max-h-48 overflow-y-scroll rounded-bl-xl mt-2">
        <div v-for="(assoc, assocKey) in $page.props.myAssociates" :key="assocKey">
            <div class="flex flex-row flex-nowrap items-center justify-between w-full -mr-2 pl-2 p-1 m-0 border-b border-we4vGrey-700 rounded-b-lg">
                <div class="p-0 -mb-1">
                    <inertia-link :href="route('user-show', assoc.slug)" as="button">
                        <img :src="'/'+assoc.path" alt="{{ assoc.username }}" class="rounded-full cursor-pointer w-7 h-7 object-cover">
                    </inertia-link>
                </div>
                <div class="text-xs text-we4vGrey-200 font-normal">
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