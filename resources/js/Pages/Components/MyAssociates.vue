<template>
    <div class="w-full bg-we4vGrey-700 max-h-48 overflow-y-scroll rounded-md">
        <div v-for="(assoc, assocKey) in $page.props.myAssociates" :key="assocKey">
            <div class="flex flex-row flex-nowrap items-center justify-between w-full rounded-md pl-2 p-1 m-0 border-b border-we4vGrey-600">
                <div class="-mb-1 p-0">
                    <inertia-link :href="route('user-show', assoc.username)" as="button">
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