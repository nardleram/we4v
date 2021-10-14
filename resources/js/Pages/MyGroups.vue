<template>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">
                <!-- Modal forms -->
                <teleport to="#groupModals">
                    <Modal :show="showGroupModal">
                        <div v-click-outside="onClickOutside" v-if="showGroupModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="closeModal('group')" class="rounded-full hover:shadow-md">
                                                <circle-x class="z-50 w-8 h-8 animate-pulse text-we4vOrange"/>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Add a group</h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">new group name</label>
                                        <input v-model="groupName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="groupName" placeholder="Enter group name">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">describe group</label>
                                        <input v-model="groupDescription" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="groupDescription" placeholder="Enter group description">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">geographic area</label>
                                        <input v-model="geogArea" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="geogArea" placeholder="Enter group’s geagraphic area">
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Invite associates to join your group (optional)</h4>
                                    <div class="w-full m-0 flex flex-col items-center">
                                        <div id="groupAssociates" v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="w-full flex flex-row justify-between items-center">
                                            <div class="w-1/4">
                                                <input @click="addRemoveAssociate('group')" :value="associate.user_id" class="invitedAssocs rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                                <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                            </div>
                                            <div class="w-1/2">
                                                <input @blur="collectGroupMemberRoles" :id="associate.user_id" class="w-full mt-4 pl-4 py-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-xs tracking-tight font-medium" type="text" placeholder="define role">
                                            </div>
                                            <div class="w-1/4 pl-8">
                                                <input @click="makeAdmin('group')" :value="associate.user_id" class="admins rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Admin</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                    @click="submitGroupData()">
                                        Save group, send invites
                                    </button>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#groupModals">
                    <Modal :show="showTeamModal" :name="groupName" :id="groupId" :description="groupDescription">
                        <div v-click-outside="onClickOutside" v-if="showTeamModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="closeModal('team')" class="rounded-full hover:shadow-md">
                                                <circle-x class="z-50 w-8 h-8 animate-pulse text-we4vOrange"/>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Add team to <span class="italic text-we4vGrey-600">{{ groupName }}</span></h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">add a team</label>
                                        <input v-model="teamName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="teamName" placeholder="Enter team name">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">define team function</label>
                                        <input v-model="teamFunction" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="teamFunction" placeholder="Define team’s function">
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Invite associates to join your team</h4>
                                    <div class="w-full m-0 flex flex-row flex-wrap justify-between">
                                        <div id="teamAssociates" v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="w-full flex flex-row justify-between items-center">
                                            <div class="w-1/4">
                                                <input @click="addRemoveAssociate('team')" :value="associate.user_id" class="invitedAssocs rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                                <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                            </div>
                                            <div class="w-1/2">
                                                <input @blur="collectTeamMemberRoles" :id="associate.user_id" class="w-full mt-4 pl-4 py-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-xs tracking-tight font-medium" type="text" placeholder="define role">
                                            </div>
                                            <div class="w-1/4 pl-8">
                                                <input @click="makeAdmin('team')" :value="associate.user_id" class="admins rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Is admin?</label>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                    @click="submitTeamData()">
                                        Save team, send invites
                                    </button>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <!-- Main -->
                <Title>
                    <template #title>
                        Group and team management
                    </template>
                    <template #description>
                        Groups house the teams or members you add to them, and can also house groups if needed. Teams house only members, they cannot house groups. Invite associates to become members of your groups or teams as required by the complexity of the project you are managing. After your groups and teams are set, you can then create projects for and assign tasks to them and their members.
                    </template>
                </Title>

                <button class="text-we4vGrey-100 bg-we4vBlue hover:bg-we4vDarkBlue hover:shadow-sm font-bold text-sm tracking-tight flex justify-center rounded-lg w-full focus:outline-none py-2 mr-1 my-4"
                @click="showGroupModal = true">
                    Create a new group
                </button>

                <Subtitle>
                    <template #title>
                        My groups
                    </template>
                    <template #description>
                        Click a group name to add/manage teams, groups or group members. Click a team name to add/manage team members.
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
import vClickOutside from 'click-outside-vue3'

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

    directives: {
      clickOutside: vClickOutside.directive
    },

    data: () => {
        return {
            showGroupModal: false,
            showTeamModal: false,
            groupId: '',
            groupDescription: '',
            groupName: '',
            groupDescription: '',
            geogArea: '',
            teamName: '',
            teamFunction: '',
            selectedGroupAssociates: [],
            groupMemberRoles: [],
            groupAdmins: [],
            selectedTeamAssociates: [],
            teamMemberRoles: [],
            teamAdmins: [],
        }
    },

    methods: {
        closeModal: function (mode) {
            mode === 'group'? this.showGroupModal = false : this.showTeamModal = false
            this.groupName = ''
            this.groupId = ''
            this.groupDescription = ''
            this.teamName = ''
            this.teamId = ''
            this.teamFunction = ''
        },

        submitGroupData: async function () {
            let members = []
            let role
            let admin
            this.selectedGroupAssociates.forEach(assoc => {
                role = this.groupMemberRoles.filter(groupMember => groupMember.id === assoc)
                admin = this.groupAdmins.filter(groupAdmin => groupAdmin === assoc)
                admin[0] ? admin = true : admin = false
                members.push({
                    user_id: assoc,
                    role: role[0].role,
                    admin: admin
                })
            })
            let payload = {
                'owner': this.$page.props.authUser.id,
                'name': this.groupName,
                'description': this.groupDescription,
                'geog_area': this.geogArea,
                'membershipable_type': 'App\\Models\\Group',
                'members': members
            }
            await this.$inertia.post('/mygroups/store', payload)
            this.groupName = ''
            this.groupDescription = ''
            this.selectedGroupAssociates = []
            this.groupMemberRoles = []
            this.groupAdmins = []
            this.showGroupModal = false
        },

        submitTeamData: async function () {
            let members = []
            let role
            let admin
            this.selectedTeamAssociates.forEach(assoc => {
                role = this.teamMemberRoles.filter(teamMember => teamMember.id === assoc)
                admin = this.teamAdmins.filter(teamAdmin => teamAdmin === assoc)
                admin[0] ? admin = true : admin = false
                members.push({
                    user_id: assoc,
                    role: role[0].role,
                    admin: admin
                })
            })
            let payload = {
                'owner': this.$page.props.authUser.id,
                'group_id': this.groupId,
                'name': this.teamName,
                'function': this.teamFunction,
                'membershipable_type': 'App\\Models\\Team',
                'members': members
            }
            await this.$inertia.post('/myteams/store', payload)
            this.teamName = ''
            this.teamFunction = ''
            this.selectedTeamAssociates = []
            this.teamMemberRoles = []
            this.teamAdmins = []
            this.showTeamModal = false
        },

        onActivateTeamModal(group) {
            this.groupId = group.group_id
            this.groupName = group.group_name
            this.groupDescription = group.group_description
            this.showTeamModal = true
        },

        onClickOutside() {
            this.showTeamModal = false
            this.showGroupModal = false
        },

        addRemoveAssociate(mode) {
            this.selectedGroupAssociates = []
            this.selectedTeamAssociates = []
            let myVals = document.querySelectorAll('input.invitedAssocs')
            myVals.forEach(myVal => {
                if (mode === 'group') {
                    myVal.checked ? this.selectedGroupAssociates.push(myVal.value) : null
                }
                if (mode === 'team') {
                    myVal.checked ? this.selectedTeamAssociates.push(myVal.value) : null
                }
            });
        },

        makeAdmin(mode) {
            this.groupAdmins = []
            this.teamAdmins = []
            let myVals = document.querySelectorAll('input.admins')
            myVals.forEach(myVal => {
                if (mode === 'group') {
                    myVal.checked ? this.groupAdmins.push(myVal.value) : null
                }
                if (mode === 'team') {
                    myVal.checked ? this.teamAdmins.push(myVal.value) : null
                }
            });
        },

        collectGroupMemberRoles(e) {
            this.groupMemberRoles.push({id: e.target.id, role: e.target.value})
        },

        collectTeamMemberRoles(e) {
            this.teamMemberRoles.push({id: e.target.id, role: e.target.value})
        }
    }
}
</script>