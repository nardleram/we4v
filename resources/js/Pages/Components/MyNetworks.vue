<template>
    <tr @click="$emit('activateNetworkInviteModal', req)" class="cursor-pointer hover:bg-we4vBlue">
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.networkOwner }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.networkName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-800">{{ req.groupName }}</td>
    </tr>
</template>

<script>

export default {
    name: 'MyNetworks',

    props: [
        'req',
    ],

    emits: [
        'activateNetworkInviteModal'
    ],
}
</script>