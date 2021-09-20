<template>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">

                <!-- Modal -->
                <teleport to="#groupModals">
                    <Modal :show="showModal" :name="groupName" :id="groupId" :description="groupDescription">
                        <div v-if="showModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                            <div class="flex justify-end">
                                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                    <div @click="closeModal" class="rounded-full hover:shadow-md">
                                        <circle-x class="z-50 w-8 h-8 animate-pulse text-we4vOrange"/>
                                    </div>   
                                </div>
                            </div>
                            <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Add team to <span class="italic text-we4vGrey-600">{{ groupName }}</span></h4>

                            <div>
                                <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">add a team</label>
                                <input v-model="teamName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="groupName" placeholder="Enter team name">
                            </div>

                            <div>
                                <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">define team function</label>
                                <input v-model="teamFunction" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="teamFunction" placeholder="Define team’s function">
                            </div>

                            <h4 class="text-we4vBlue font-semibold text-sm mt-4">Invite associates to join your team</h4>
                            <div class="w-full m-0 flex flex-row flex-wrap justify-between">
                                <div id="teamAssociates" v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="max-w-1/4 min-w-1/4">
                                    <input @click="addRemoveAssociate('team')" :value="associate.user_id" class="rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox">
                                    <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                    <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                </div>
                            </div>
                            <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                            @click="submitTeamData()">
                                Save team, send invites
                            </button>
                        </div>
                    </Modal>
                </teleport>

                <!-- Main -->
                <Title>
                    <template #title>
                        Group and team management
                    </template>
                    <template #description>
                        Groups house the teams or members you add to them, as well as housing other groups if needed. Teams house the people you assign whatever roles are needed to complete projects and project-tasks relevant for the group. Teams cannot house groups. Invite associates to become members of your groups or teams as required by the complexity of the project you are managing.
                    </template>
                </Title>

                <Form>
                    <template #form>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">Create new group</label>
                            <input v-model="groupName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="groupName" placeholder="enter group name">
                        </div>
                        <div @click="errors.name = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.name">{{ errors.name }}</div>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupDescription">purpose</label>
                            <input v-model="groupDescription" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="groupDescription" placeholder="enter group description">
                        </div>
                        <div @click="errors.description = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.description">{{ errors.description }}</div>
                        <h4 class="text-we4vBlue font-semibold text-sm">Invite associates to join your group (<span class="font-bold text-we4vGrey-700">not</span> team)</h4>
                        <div class="w-full m-0 flex flex-row flex-wrap justify-between">
                            <div id="groupAssociates" v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="max-w-1/4 min-w-1/4">
                                <input @click="addRemoveAssociate('group')" class="rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox" :value="associate.user_id">
                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                            </div>
                            <div @click="errors.assocs = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.assocs">{{ errors.assocs }}</div>
                            <div @click="errors.group_id = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.group_id">{{ errors.group_id }}</div>
                            <div @click="errors.type = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.type">{{ errors.type }}</div>
                            <div @click="errors.role = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.role">{{ errors.role }}</div>
                        </div>
                        <div @click="errors.description = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.description">{{ errors.description }}</div>
                        <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                        @click="submitGroupData()">
                            Save new group
                        </button>
                    </template>
                </Form>

                <Subtitle>
                    <template #title>
                        My groups
                    </template>
                    <template #description>
                        Click a team name to add/manage teams, groups or members. Click a group to add/manage group members.
                    </template>
                </Subtitle>

                <!-- Groups and teams -->
                <div v-if="mygroups.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Group v-for="(group, groupKey) in mygroups" :key="groupKey" :group="group" :teams="group.teams" @activate-team-modal="onActivateTeamModal" />
                    </div>
                </div>

            </div>
        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Form from '@/Jetstream/FormSection'
import Group from './Components/Group'
import Modal from './Components/Modal'
import Title from '@/Jetstream/SectionTitle'
import Subtitle from '@/Jetstream/Subtitle'
import CircleX from '../Jetstream/CircleX'
export default {
    name: 'MyGroups',

    components: {
        AppLayout,
        Form,
        Modal,
        Group,
        Title,
        Subtitle,
        CircleX
    },

    props: [
        'mygroups',
        'errors',
        'user'
    ],

    data: () => {
        return {
            showModal: false,
            groupId: '',
            groupDescription: '',
            groupName: '',
            teamName: '',
            teamFunction: '',
            selectedGroupAssociates: [],
            selectedTeamAssociates: [],
        }
    },

    methods: {
        closeModal: function () {
            this.showModal = false
            this.groupName = ''
            this.groupId = ''
            this.groupDescription = ''
        },

        submitGroupData: async function () {
            let payload = {
                'owner': this.$page.props.authUser.id,
                'name': this.groupName,
                'description': this.groupDescription,
                'membership_type': 'App\\Models\\Group',
                'assocs': this.selectedGroupAssociates,
                'role': 'test role'
            }
            await this.$inertia.post('/mygroups/store', payload)
            this.groupName = ''
            this.groupDescription = ''
            this.selectedGroupAssociates = []
        },

        submitTeamData: async function () {
            let payload = {
                'owner': this.$page.props.authUser.id,
                'group_id': this.groupId,
                'name': this.teamName,
                'function': this.teamFunction,
                'membership_type': 'App\\Models\\Team',
                'assocs': this.selectedTeamAssociates,
                'role': 'team role'
            }
            await this.$inertia.post('/myteams/store', payload)
            this.selectedTeamAssociates = []
            this.closeModal()
        },

        onActivateTeamModal(group) {
            this.groupId = group.group_id
            this.groupName = group.group_name
            this.groupDescription = group.group_description
            this.showModal = true
        },

        addRemoveAssociate(mode) {
            this.selectedTeamAssociates = []
            this.selectedGroupAssociates = []
            let myVals = document.querySelectorAll('input[type=checkbox]')
            myVals.forEach(myVal => {
                if (mode === 'group') {
                    myVal.checked ? this.selectedGroupAssociates.push(myVal.value) : null
                }
                if (mode === 'team') {
                    myVal.checked ? this.selectedTeamAssociates.push(myVal.value) : null
                }
            });
        }
    }
}
</script>