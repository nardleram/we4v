<template>
    <div class="text-we4vBg bg-we4vGrey-800 p-3 max-w-5/12 min-w-5/12 mr-1 rounded shadow-md mb-1 tracking-tight">
        <div class="text-sm font-semibold text-we4vBlue flex flex-row justify-between items-center mb-2">
            <p @click="$emit('activateTeamModal', group)" class="cursor-pointer w-3/5">{{ group.group_name }}</p>
            <div class="flex flex-row flex-nowrap w-1/5 m-0">
                <img @click="updateGroup(group.group_id)" src="/images/pencil.svg" alt="" class="h-5 object-cover cursor-pointer mr-3">
                <img @click="deleteGroup(group.group_id)" src="/images/bin.svg" alt="" class="h-5 object-cover cursor-pointer">
            </div>
        </div>
        <div class="text-xs font-light text-we4vGrey-200 italic">
            {{ group.group_description }}
        </div>
        <div v-if="group.groupMembers" class="mt-3">
            <h4 class="text-sm font-regular text-we4vGrey-200">Members</h4>
            <div class="bg-we4vBg text-sm font-normal rounded my-2 p-2 flex flex-col flex-wrap items-start justify-between">
                <div v-for="(member, memberKey) in group.groupMembers" :key="memberKey">
                    <div class="flex flex-row mb-2 items-center">
                        <div class="font-bold text-xs text-we4vBlue mr-2">
                            {{ member.username }}
                        </div>
                        <div v-if="!member.confirmed" class="font-regular text-red-600 text-xs mr-2">
                            <p>TBC</p>
                        </div>
                        <div class="font-regular text-we4vGrey-600 text-xs">
                            <p>Role: {{ member.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="group.teams">
            <h4 class="text-sm font-regular text-we4vGrey-200 mt-2">Teams</h4>
            <div v-for="(team, teamKey) in group.teams" :key="teamKey" class="bg-we4vBg text-sm font-normal text-we4vGrey-800 rounded my-2 p-2">
                <div class="font-semibold">
                    {{ team.team_name }}
                </div>
                <div class="font-light text-xs italic">
                    {{ team.team_function }}
                </div>
                <div class="bg-we4vBg text-sm font-normal rounded my-2 p-2 flex flex-col flex-wrap items-start justify-between">
                    <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey">
                        <div class="flex flex-row mb-2 items-center">
                            <div class="font-bold text-xs text-we4vBlue mr-2">
                                {{ teamMember.username }}
                            </div>
                            <div v-if="!teamMember.confirmed" class="font-regular text-red-600 text-xs mr-2">
                                <p>TBC</p>
                            </div>
                            <div class="font-regular text-we4vGrey-600 text-xs">
                                <p>Role: {{ teamMember.role }}</p>
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

export default {
    name: 'Group',

    props: [
        'team',
        'group',
        'users',
    ],

    emits: [
        'activateTeamModal'
    ],

    methods: {
        async deleteGroup(id) {
            await Inertia.delete(`/mygroups/${id}/destroy`, { method: 'delete' })
        },

        async updateGroup(id) {
            await Inertia.update(`/mygroups/${id}/update`)
        }
    }
}
</script>