<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-groupBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight">
        <div @click="$emit('activateEditTeamModal', team)" class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center cursor-pointer pt-1 max-h-10">
            <p class="text-we4vBlue mt-0">{{ team.team_name }} <span v-if="team.team_owner" class="text-we4vGrey-200 font-light text-xs italic"> – Owner: {{ team.team_owner }}</span></p>
            <p class="text-xs font-light text-we4vGrey-200 italic mt-1">{{ team.team_function }}</p>
        </div>
        <div class="col-start-11 col-end-13 flex flex-row flex-nowrap justify-around p-0 content-center items-center max-h-10">
            <div @click="$emit('activateEditTeamModal', team)">
                <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
            </div>
            <div>
                <i v-if="!displayDetails" @click="displayDetails = !displayDetails" class="fas fa-lock h-5 cursor-pointer text-lg"></i>
                <i v-if="displayDetails" @click="displayDetails = !displayDetails" class="fas fa-unlock h-5 cursor-pointer text-lg"></i>
            </div>
        </div>

        <div v-if="displayDetails" class="col-start-1 col-end-13 mt-3">
            <div v-if="team.teamMembers">
                <h4 class="text-sm font-regular text-we4vGrey-200">Members</h4>
                <div class="bg-we4vBg text-sm font-normal rounded my-2 px-2 pt-2 flex flex-col flex-wrap items-start justify-between">
                    <div v-for="(member, memberKey) in team.teamMembers" :key="memberKey">
                        <div class="flex flex-row items-center">
                            <div class="font-bold text-xs text-we4vBlue mr-2">
                                {{ member.username }}<span v-if="member.admin" class="text-we4vOrange font-bold">*</span>
                            </div>
                            <div class="font-regular mr-3">
                                <p class="text-we4vGrey-600 text-xs">Role: {{ member.role }}</p>
                            </div>
                            <div v-if="!member.confirmed" >
                                <p class="font-bold text-we4vGrey-600 text-xs">(TBC)</p>
                            </div>
                            <div v-if="member.declined">
                                <p class="font-bold text-red-600 text-xs">Invitation declined <span class="ml-2 cursor-pointer font-bold text-we4vBlue text-xs hover:text-we4vDarkBlue">Delete membership</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'Team',

    props: [
        'team'
    ],

    emits: [
        'activateEditTeamModal',
    ],

    setup (props) {
        const displayDetails = ref(false)
        const owner = ref(null)

        const setOwner = function () {
            let hit
            usePage().props.value.myAssociates.forEach(assoc => {
                assoc.user_id === props.team.team_owner
                ? hit = assoc.username
                : null
            })
            
            owner.value = hit
        }

        setOwner()

        return {
            displayDetails,
            owner
        }
    }
}
</script>