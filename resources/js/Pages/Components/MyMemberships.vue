<template>
    <table class="text-xs font-light tracking-tighter rounded-bl-xl">
        <thead class="text-we4vBlue table-fixed border border-we4vGrey-900 text-left">
            <tr>
                <th class="w-3/12 border border-we4vGrey-800 py-2 px-1">Name</th>
                <th class="w-2/12 border border-we4vGrey-800 py-2 px-1">Type</th>
                <th class="w-4/12 border border-we4vGrey-800 py-2 px-1">Role (admin<span class="font-bold text-sm text-we4vOrange">*</span>)</th>
                <th class="w-3/12 border border-we4vGrey-800 py-2 px-1">Owner</th>
            </tr>
        </thead>
        <tbody class="border border-we4vGrey-800">
            <tr v-for="(memb, membKey) in $page.props.myMemberships" :key="membKey">
                <td @click="$emit('activateMembModal', memb)" class="text-xs leading-tight p-1 border border-we4vGrey-800 hover:bg-we4vBlue cursor-pointer">
                    {{ memb.name }}
                </td>
                <td class="text-xs leading-tight p-1 border border-we4vGrey-800">
                    {{ memb.type }}
                </td>
                <td class="text-xs leading-tight p-1 border border-we4vGrey-800">
                    {{ memb.role }}<span v-if="memb.admin" class="font-bold text-sm text-we4vOrange">*</span>
                </td>
                <td class="text-left leading-tight p-1 border border-we4vGrey-800">
                    <inertia-link :href="route('user-show', memb.slug)" as="button">
                        <p class="text-xs text-we4vGrey-200 hover:text-we4vBlue">{{ memb.owner }}</p>
                    </inertia-link>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    name: 'MyAssociates',

    emits: [
        'activateMembModal',
    ],
}
</script>