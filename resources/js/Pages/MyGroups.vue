<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">
                <!-- Modal forms -->
                <teleport to="#groupModals">
                    <Modal :show="showGroupModal">
                        <div @mouseleave="nowOutside(); mode = 'group'" @mouseenter="nowInside(); mode = 'group'" v-if="showGroupModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="clearModal()" >
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 v-if="!edit" class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Add a group</h4>
                                    <h4 v-if="edit" class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Edit group <span class="italic text-we4vGrey-600">{{ groupName }}</span></h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">group name</label>
                                        <input v-model="groupName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" placeholder="Enter group name">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">describe group</label>
                                        <input v-model="groupDescription" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="groupDescription" placeholder="Enter group description">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="geogArea">geographic area</label>
                                        <input v-model="geogArea" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="geogArea" placeholder="Enter group’s geagraphic area">
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Invite associates to join your group (optional)</h4>
                                    <div v-if="!edit" class="w-full m-0 flex flex-col items-center">
                                        <div v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="w-full flex flex-row justify-between items-center">
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
                                    <div v-if="edit" class="w-full m-0 flex flex-col items-center">
                                        <div v-for="(groupMember, groupMembersEditKey) in groupMembersEdit" :key="groupMembersEditKey" class="w-full flex flex-row justify-between items-center">

                                            <div class="w-1/4">
                                                <input @click="addRemoveAssociate('group')" :value="groupMember.user_id" class="invitedAssocsEdit rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" :checked="groupMember.invited" type="checkbox">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ groupMember.user_id }}">{{ groupMember.user_name }}</label>
                                                <img :src="'/'+groupMember.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                            </div>

                                            <div class="w-1/2">
                                                <input @blur="collectGroupMemberRoles" :id="groupMember.user_id" v-model="groupMember.role" class="w-full mt-4 pl-4 py-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-xs tracking-tight font-medium" type="text" placeholder="define role">
                                            </div>

                                            <div class="w-1/4 pl-8">
                                                <input @click="makeAdmin('group')" :value="groupMember.user_id" class="admins rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" :checked="groupMember.admin" type="checkbox">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Admin</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                    @click="submitGroupData()">
                                        <p v-if="!edit">Save group, send invites</p>
                                        <p v-else>Update group (send invites)</p>
                                    </button>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#groupModals">
                    <Modal :show="showTeamModal" :name="groupName" :id="groupId" :description="groupDescription">
                        <div v-if="showTeamModal" @mouseleave="nowOutside(); mode = 'team'" @mouseenter="nowInside(); mode = 'team'" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                        <div class="w-9/12 text-sm font-bold mb-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.members">{{ errors.members }}</div>
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="clearModal()">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
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
                                        <div v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="w-full flex flex-row justify-between items-center">
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
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Admin</label>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                    @click="submitTeamData()">
                                        <p v-if="!edit">Save team, send invites</p>
                                        <p v-else>Update team (send invites)</p>
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
                @click="showGroupModal = true; showBackdrop = true">
                    Create a new group
                </button>

                <Subtitle>
                    <template #title>
                        My groups
                    </template>
                    <template #description>
                        Click a group name to add a team. Click the edit icon to the right of a group’s name to make changes to that group.
                    </template>
                </Subtitle>

                <!-- Groups and teams -->
                <div v-if="mygroups.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Group v-for="(group, groupKey) in mygroups" :key="groupKey" :group="group" :teams="group.teams" @activate-team-modal="onActivateTeamModal" @activate-edit-group-modal="onActivateEditGroupModal"/>
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
import ModalBackdrop from './Components/ModalBackdrop'
import Title from '@/Jetstream/SectionTitle'
import Subtitle from '@/Jetstream/Subtitle'
import manageModals from '../Pages/Composables/manageModals'
import { watch, ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'

export default {
    name: 'MyGroups',

    components: {
        AppLayout,
        Form,
        Modal,
        ModalBackdrop,
        Group,
        Title,
        Subtitle,
        FlashMessage,
        ErrorMessage,
    },

    props: [
        'mygroups',
        'errors',
        'user'
    ],

    setup(props) {
        const {
            amOutside, 
            amInside,
            clearInviteModals,
            clearModal,
            edit,
            groupAdmins,
            groupId,
            groupMembers,
            groupMembersEdit,
            groupName,
            groupDescription,
            geogArea,
            mode,
            nowInside, 
            nowOutside,
            onActivateEditGroupModal,
            onActivateTeamModal,
            onClickOutside,
            showBackdrop,
            showGroupModal,
            showTeamModal,
            teamAdmins,
            teamMembers,
            teamMembersEdit,
            teamFunction,
            teamName,
        } = manageModals()
        
        let groupMemberRoles = ref([])
        let selectedGroupAssociates = ref([])
        let selectedTeamAssociates = ref([])
        let teamMemberRoles = ref([])

        const submitGroupData = async function () {
            let members = []
            let role
            let admin

            if (edit.value) {
                groupMembersEdit.value.forEach(groupMember => {
                    if (groupMember.invited === 'clicked' || groupMember.invited) {
                        groupMember.invited === 'clicked'
                        ? selectedGroupAssociates.value.push({id: groupMember.user_id, invited: false, confirmed: groupMember.confirmed})
                        : selectedGroupAssociates.value.push({id: groupMember.user_id, invited: groupMember.invited, confirmed: groupMember.confirmed})
                    }
                    if (!groupMemberRoles.value.find(role => role.id === groupMember.user_id)) {
                        groupMemberRoles.value.push({id: groupMember.user_id, role: groupMember.role})
                    }
                    if (groupMember.admin) {
                        groupAdmins.value.push(groupMember.user_id)
                    }
                })
            }
            
            selectedGroupAssociates.value.forEach(assoc => {
                role = groupMemberRoles.value.filter(groupMember => groupMember.id === assoc.id)
                admin = groupAdmins.value.filter(groupAdmin => groupAdmin === assoc.id)
                admin[0] ? admin = true : admin = false
                members.push({
                    user_id: assoc.id,
                    role: role[0].role,
                    admin: admin,
                    invited: assoc.invited,
                    confirmed: assoc.confirmed
                })
            })
            
            let payload = {
                'owner': usePage().props.value.authUser.id,
                'name': groupName.value,
                'description': groupDescription.value,
                'geog_area': geogArea.value,
                'membershipable_type': 'App\\Models\\Group',
                'members': members,
            }
            edit.value ? payload.membershipable_id = groupId.value : null
            try {
                edit.value
                ? await Inertia.patch('/mygroups/update', payload)
                : await Inertia.post('/mygroups/store', payload)
                props.errors = null
            } catch (err) {
                props.errors = err
            }

            clearModal()
        }

        const submitTeamData = async function () {
            let members = []
            let role
            let admin

            if (edit.value) {
                teamMembersEdit.value.forEach(teamMember => {
                    if (teamMember.invited === 'clicked' || teamMember.invited) {
                        teamMember.invited === 'clicked'
                        ? selectedTeamAssociates.value.push({id: teamMember.user_id, invited: false, confirmed: teamMember.confirmed})
                        : selectedTeamAssociates.value.push({id: teamMember.user_id, invited: teamMember.invited, confirmed: teamMember.confirmed})
                    }
                    if (!teamMemberRoles.value.find(role => role.id === teamMember.user_id)) {
                        teamMemberRoles.value.push({id: teamMember.user_id, role: teamMember.role})
                    }
                    if (teamMember.admin) {
                        teamAdmins.value.push(teamMember.user_id)
                    }
                })
            }

            selectedTeamAssociates.value.forEach(assoc => {
                role = teamMemberRoles.value.filter(teamMember => teamMember.id === assoc.id)
                admin = teamAdmins.value.filter(teamAdmin => teamAdmin === assoc.id)
                admin[0] ? admin = true : admin = false
                members.push({
                    user_id: assoc.id,
                    role: role[0].role,
                    admin: admin,
                    invited: assoc.invited,
                    confirmed: assoc.confirmed
                })
            })

            let payload = {
                'owner': usePage().props.value.authUser.id,
                'group_id': groupId.value,
                'name': teamName.value,
                'function': teamFunction.value,
                'membershipable_type': 'App\\Models\\Team',
                'members': members
            }

            try {
                edit.value
                ? await Inertia.patch('/myteams/update', payload)
                : await Inertia.post('/myteams/store', payload)
                props.errors = null
            } catch (err) {
                props.errors = err
            }

            clearModal()
        }

        const addRemoveAssociate = function (mode) {
            selectedGroupAssociates.value = []
            selectedTeamAssociates.value = []
            let myVals
            edit.value
            ? myVals = document.querySelectorAll('input.invitedAssocsEdit')
            : myVals = document.querySelectorAll('input.invitedAssocs')

            if (mode === 'group') {
                selectedGroupAssociates.value = []
                for (const myVal of myVals) {
                    if (myVal.checked) {
                        selectedGroupAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                    }
                }
            }
            if (mode === 'group' && edit.value) {
                for (const groupMember of groupMembersEdit.value) {
                    if (!groupMember.invited) {
                        for (const myVal of myVals) {
                            if (myVal.checked && myVal.value === groupMember.user_id) {
                                selectedGroupAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                            }
                        }
                    }
                }
            }

            if (mode === 'team') {
                for (const myVal of myVals) {
                    if (myVal.checked) {
                        selectedTeamAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                    }
                }
            }
            if (mode === 'team' && edit.value) {
                for (const teamMember of teamMembersEdit.value) {
                    if (!teamMember.invited) {
                        for (const myVal of myVals) {
                            if (myVal.checked && myVal.value === teamMember.user_id) {
                                selectedTeamAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                            }
                        }
                    }
                }
            }
        }

        const makeAdmin = function (mode) {
            groupAdmins.value = []
            teamAdmins.value = []
            let myVals = []

            edit.value
            ? myVals = document.querySelectorAll('input.adminsEdit')
            : myVals = document.querySelectorAll('input.admins')

            myVals.forEach(myVal => {
                if (mode === 'group') {
                    myVal.checked ? groupAdmins.value.push(myVal.value) : null
                }
                if (mode === 'team') {
                    myVal.checked ? teamAdmins.value.push(myVal.value) : null
                }
            })
        }

        const collectGroupMemberRoles = function (e) {
            groupMemberRoles.value.push({id: e.target.id, role: e.target.value})
        }

        const collectTeamMemberRoles = function (e) {
            if (teamMemberRoles.value.length > 0) {
                teamMemberRoles.value.forEach(member => {
                    e.target.id !== member.id
                    ? teamMemberRoles.value.push({id: e.target.id, role: e.target.value})
                    : null
                })
            } else {
                teamMemberRoles.value.push({id: e.target.id, role: e.target.value})
            }
        }

        watch(amOutside, () => {
            if (amOutside.value && !amInside.value) {
                document.body.addEventListener('click', onClickOutside, true)
            }
        })

        watch(edit, () => {
            if (groupMembers.value.length) {
                groupMembers.value.forEach(groupMember => {
                    groupMembersEdit.value.push(groupMember)
                })
                usePage().props.value.myAssociates.forEach(associate => {
                    if (!groupMembersEdit.value.some(groupMemberEdit => groupMemberEdit.user_id === associate.user_id)) {
                        associate.invited = false
                        associate.confirmed = false
                        groupMembersEdit.value.push(associate)
                    }
                })
            }

            if (teamMembers.value.length) {
                teamMembers.value.forEach(teamMember => {
                    teamMembersEdit.value.push(teamMember)
                })
                usePage().props.value.myAssociates.forEach(associate => {
                    if (!teamMembersEdit.value.some(teamMemberEdit => teamMemberEdit.user_id === associate.user_id)) {
                        associate.invited = false
                        associate.confirmed = false
                        teamMembersEdit.value.push(associate)
                    }
                })
            }
        })

        return {
            addRemoveAssociate,
            amOutside,
            amInside,
            clearInviteModals,
            clearModal,
            collectGroupMemberRoles,
            collectTeamMemberRoles,
            edit,
            groupAdmins,
            groupDescription,
            groupMembers,
            groupMembersEdit,
            groupId,
            groupName,
            groupMemberRoles,
            geogArea,
            makeAdmin,
            mode,
            nowInside, 
            nowOutside,
            onActivateEditGroupModal,
            onActivateTeamModal,
            onClickOutside,
            selectedGroupAssociates,
            selectedTeamAssociates,
            showBackdrop,
            showGroupModal,
            showTeamModal,
            submitGroupData,
            submitTeamData,
            teamAdmins,
            teamFunction,
            teamMembers,
            teamMembersEdit,
            teamMemberRoles,
            teamName
        }
    }
}
</script>