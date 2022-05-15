<template>
    <tr v-if="(req.type === 'group')" @click="$emit('activateInviteModal', req)" class="text-xs cursor-pointer hover:bg-we4vBlue border-b border-we4vGrey-600">
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.g_updated_by }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.groupName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.groupDesc }} <span v-if="req.geogArea">({{ req.geogArea }})</span></td>
    </tr>
    <tr v-if="(req.type === 'team')" @click="$emit('activateInviteModal', req)" class="text-xs cursor-pointer hover:bg-we4vBlue border-b border-we4vGrey-600">
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.t_updated_by }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.teamName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.teamFunc }}</td>
    </tr>
</template>

<script>
export default {
    name: 'PendingMembReqs',

    props: [
        'req'
    ],

    emits: [
        'activateInviteModal'
    ],
}
</script>