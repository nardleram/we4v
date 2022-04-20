<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-groupBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight">
        <div @click="$emit('activateTeamModal', group)" class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center cursor-pointer pt-1 max-h-10">
            <p class="text-we4vBlue mt-0">{{ group.group_name }} <span v-if="group.geog_area" class="text-we4vGrey-200 font-light text-xs italic">({{ group.geog_area }})</span> <span v-if="group.group_owner_username" class="text-we4vGrey-200 font-light text-xs italic"> – Owner: {{ group.group_owner_username }}</span></p>
            <p class="text-xs font-light text-we4vGrey-200 italic mt-1">{{ group.group_description }}</p>
        </div>
        <div class="col-start-11 col-end-13 flex flex-row flex-nowrap justify-around p-0 content-center items-center max-h-10">
            <div @click="$emit('activateEditGroupModal', group)">
                <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
            </div>
            <div v-if="group.group_owner === $page.props.authUser.id" @click="$emit('activateTransferGroupOwnership', group)">
                <i class="fas fa-arrow-alt-circle-right h-5 cursor-pointer text-lg"></i>
            </div>
            <div class="h-5 rounded-full bg-we4vGrey-200">
                <img v-if="!displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/openGlyph.svg" alt="">
                <img v-if="displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/closeGlyph.svg" alt="">
            </div>
        </div>

        <div v-if="displayDetails" class="col-start-1 col-end-13 mt-3">
            <div v-if="group.groupMembers">
                <h4 class="text-sm font-semibold text-we4vGrey-200">Group members</h4>
                <div class="bg-we4vBg rounded-md px-2 pt-1 flex flex-col flex-wrap items-start justify-between">
                    <div v-for="(member, memberKey) in group.groupMembers" :key="memberKey">
                        <div class="flex flex-row items-center">
                            <div class="font-bold text-xs text-we4vBlue mr-2">
                                {{ member.username }}<span v-if="member.admin" class="text-we4vOrange font-bold">*</span>
                            </div>
                            <div class="font-regular mr-3">
                                <p class="text-we4vGrey-600 text-xs">Role: {{ member.role }}</p>
                            </div>
                            <div v-if="!member.confirmed && !member.declined" class="text-red-600 text-xs">
                                <p class="text-we4vGrey-600 text-xs font-bold">(TBC)</p>
                            </div>
                            <div v-if="member.declined" class="text-red-600 text-xs">
                                <p class="text-red-600 text-xs font-bold">Invitation declined <span @click="deleteMembership(member.membership_id)" class="ml-2 cursor-pointer font-bold text-we4vBlue text-xs hover:text-we4vDarkBlue">Delete membership</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="group.teams">
                <h4 class="text-sm font-semibold text-we4vGrey-200 mt-4">Teams</h4>
                <div v-for="(team, teamKey) in group.teams" :key="teamKey" class="w-full flex bg-we4vBg text-sm font-normal text-we4vGrey-800 rounded my-2 p-1 max-h-48 overflow-y-scroll">

                    <div id="teamDeetz" class="w-5/6">
                        <div class="font-semibold">
                            {{ team.team_name }}
                        </div>
                        <div class="font-light text-xs italic">
                            {{ team.team_function }}
                        </div>
                        <div class="bg-we4vBg text-sm font-normal rounded flex flex-col flex-wrap items-start justify-between">
                            <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey">
                                <div class="flex flex-row items-center">
                                    <div class="font-bold text-xs text-we4vBlue mr-2">
                                        {{ teamMember.username }}<span v-if="teamMember.admin" class="text-we4vOrange font-bold">*</span>
                                    </div>
                                    <div class="font-regular text-we4vGrey-600 text-xs mr-3">
                                        <p class="text-we4vGrey-600 text-xs">Role: {{ teamMember.role }}</p>
                                    </div>
                                    <div v-if="!teamMember.confirmed && !teamMember.declined">
                                        <p class="text-we4vGrey-600 text-xs font-bold">(TBC)</p>
                                    </div>
                                    <div v-if="teamMember.declined">
                                        <p class="text-red-600 text-xs font-bold">Invitation declined <span @click="deleteMembership(teamMember.membership_id)" class="ml-2 cursor-pointer font-bold text-we4vBlue text-xs hover:text-we4vDarkBlue">Delete membership</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="teamActions" class="flex w-1/6 items-start justify-end mt-1">
                        <div @click="$emit('activateEditTeamModal', team)">
                            <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
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
    name: 'Group',

    props: [
        'team',
        'group',
    ],

    emits: [
        'activateTeamModal',
        'activateEditGroupModal',
        'activateEditTeamModal',
        'activateTransferGroupOwnership'
    ],

    setup (props) {
        const displayDetails = ref(false)
        const owner = ref(null)

        const setOwner = function () {
            let hit
            usePage().props.value.myAssociates.forEach(assoc => {
                assoc.user_id === props.group.group_owner
                ? hit = assoc.username
                : null
            })
            
            owner.value = hit
        }

        const deleteMembership = async function (id) {
            try {
                await Inertia.delete('/memberships/'+id+'/destroy')
                flashMessage.value = true
                props.errors = null
            } catch (err) {
                error.value = true
                props.errors  = err
            }
        }

        setOwner()

        return {
            displayDetails,
            owner,
            deleteMembership
        }
    }
}
</script>