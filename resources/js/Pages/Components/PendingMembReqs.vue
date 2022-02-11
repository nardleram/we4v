<template>
    <tr v-if="(req.type === 'group')" @click="$emit('activateInviteModal', req)" class="cursor-pointer hover:bg-we4vBlue">
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.groupRequester ? req.groupRequester : req.groupOwner }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.groupName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.groupDesc }} <span v-if="req.geogArea">({{ req.geogArea }})</span></td>
    </tr>
    <tr v-if="(req.type === 'team')" @click="$emit('activateInviteModal', req)" class="cursor-pointer hover:bg-we4vBlue">
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.teamRequester ? req.teamRequester : req.teamOwner }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.teamName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.teamFunc }}</td>
    </tr>
</template>

<script>
import ConnectionDots from '../../Jetstream/ConnectionDots'

export default {
    name: 'PendingMembReqs',

    components: {
        ConnectionDots,
    },

    props: [
        'req',
        'user'
    ],

    emits: [
        'activateInviteModal'
    ],
}
</script>