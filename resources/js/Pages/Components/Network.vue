<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-groupBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight">
        <div class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center pt-1 max-h-10">
            <p class="text-we4vBlue mt-0">{{ network.network_name }}</p>
            <p class="text-xs font-light text-we4vGrey-200 italic mt-1">{{ network.network_description }}</p>
        </div>
        <div class="col-start-11 col-end-13 flex flex-row flex-nowrap justify-around p-0 content-center items-center max-h-10">
            <div @click="$emit('activateEditNetworkModal', network)">
                <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
            </div>
            <div @click="$emit('activateTransferNetworkOwnership', network)">
                <i class="fas fa-arrow-alt-circle-right h-5 cursor-pointer text-lg"></i>
            </div>
            <div class="h-5 rounded-full bg-we4vGrey-200">
                <img v-if="!displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/openGlyph.svg" alt="">
                <img v-if="displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/closeGlyph.svg" alt="">
            </div>
        </div>

        <div v-if="displayDetails" class="col-start-1 col-end-13 mt-3">
            <div v-if="network.groups">
                <h4 class="text-sm font-semibold text-we4vGrey-200">Group members</h4>
                <div class="bg-we4vBg text-sm font-normal rounded my-2 px-2 pt-2 flex flex-col flex-wrap items-start justify-between">
                    <div v-for="(member, memberKey) in network.groups" :key="memberKey">
                        <div class="flex flex-row items-center">
                            <div class="font-bold text-xs text-we4vBlue mr-2">
                                <span @click="$emit('activateShowGroupModal', member.group_id)" class="hover:text-we4vDarkBlue cursor-pointer">
                                    {{ member.group_name }} 
                                </span>
                                <span class="font-normal text-we4vGrey-500 hover:text-we4vBlue">
                                    <inertia-link :href="route('user-show', member.user_slug)">
                                        ({{ member.group_owner }})
                                    </inertia-link>
                                </span>
                            </div>
                            <div class="font-regular mr-3">
                                <p class="text-xs text-we4vGrey-500 italic">"{{ member.group_description }}"</p>
                            </div>
                            <div class="font-regular mr-3">
                                <p class="text-xs text-we4vGrey-600 font-medium">{{ member.group_geog_area }}</p>
                            </div>
                            <div v-if="!member.membership_confirmed && !member.declined">
                                <p class="font-bold text-red-600 text-xs">(TBC)</p>
                            </div>
                            <div v-if="member.declined" class="text-red-600 text-xs">
                                <p class="font-bold">Invitation declined</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'Network',

    props: [
        'network',
    ],

    emits: [
        'activateEditNetworkModal',
        'activateShowGroupModal',
        'activateTransferNetworkOwnership'
    ],

    setup (props) {
        const displayDetails = ref(false)
        const owner = ref(null)

        const deleteNetwork = async function (id) {
            let deleteOk = true
            if (props.group.members || props.group.teams) {
                deleteOk = false
            }
            deleteOk
            ? await Inertia.delete(`/mygroups/${id}/destroy`, { method: 'delete' })
            : alert('force election')
        }

        const setOwner = function () {
            let hit
            usePage().props.value.myAssociates.forEach(assoc => {
                assoc.user_id === props.network.network_owner
                ? hit = assoc.username
                : null
            })
            
            owner.value = hit
        }

        setOwner()

        return {
            displayDetails,
            deleteNetwork,
            owner
        }
    }
}
</script>