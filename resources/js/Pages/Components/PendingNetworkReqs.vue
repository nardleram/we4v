<template>
    <tr @click="$emit('activateNetworkInviteModal', req)" class="cursor-pointer hover:bg-we4vDarkBlue">
        <td class="text-xs px-1 py-2 border border-we4vGrey-800 rounded-bl-xl">{{ req.username }}</td>
        <td class="text-xs px-1 py-2 border border-we4vGrey-800">{{ req.networkName }}</td>
        <td class="text-xs px-1 py-2 border border-we4vGrey-800">{{ req.groupName }}</td>
    </tr>
</template>

<script>

export default {
    name: 'PendingNetworkReqs',

    components: {
    },

    props: [
        'req',
    ],

    emits: [
        'activateNetworkInviteModal'
    ],

}
</script>

<style scoped>

</style>