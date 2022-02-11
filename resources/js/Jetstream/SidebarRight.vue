<template>
    <teleport to="#membRequestModals">
        <Modal :show="showInviteModal" >
            <div v-if="showInviteModal" @mouseleave="nowOutside(); mode = type" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                <div class="flex justify-end">
                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                        <div @click="showInviteModal = false; clearModal()">
                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                        </div>   
                    </div>
                </div>

                <h4 v-if="!updated" class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ groupOwner ? groupOwner : teamOwner }} cordially invites you to join the {{ type }} </h4>
                <h4 v-else class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ groupRequester ? groupRequester : teamRequester }} cordially invites you to join the {{ type }} </h4>
                <h3 class="font-serif font-semibold text-we4vBlue text-center text-2xl">{{ groupName ? groupName : teamName }}</h3>

                <h5 class="text-we4vGrey-700 text-sm mt-4">Description: {{ groupDescription ? groupDescription : teamFunction  }}</h5>
                <h5 v-if="geogArea" class="text-we4vGrey-700 text-sm mt-1">Geographical area: {{ geogArea }}</h5>
                <h5 v-if="groupRole || teamRole" class="text-we4vGrey-700 text-sm mt-1">Proposed role: {{ groupRole ? groupRole : teamRole }}</h5>
                <h5 v-if="gAdmin || tAdmin" class="text-we4vOrange text-sm font-medium mt-1">{{ groupOwner ? groupOwner : teamOwner }} also asks that you become the {{ type }}’s administrator.</h5>
                
                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
                @click="storeInviteResponse(inviteData, true)">
                    Accept
                </button>
                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-red-600 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none my-4 py-2"
                @click="storeInviteResponse(inviteData, false)">
                    Reject
                </button>
            </div>
        </Modal>
    </teleport>

    <teleport to="#pendingVoteModals">
        <Modal :show="showPendingVoteModal" >
            <div v-if="showPendingVoteModal" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                <div class="flex justify-end">
                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                        <div @click="showPendingVoteModal = false; clearModal()">
                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                        </div>   
                    </div>
                </div>

                <div>
                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8 w-10/12">{{ voteOwner }} requests your vote to help decide on <span class="italic text-we4vGrey-600">{{ voteTitle }}</span></h4>
                    <p class="text-we4vGrey-500 text-sm">Closing date: <span class="italic font-medium">{{ voteClosingDate }}</span></p>
                    <h5 class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight mt-4">Vote options</h5>
                    <div v-for="(element, elementKey) in voteElements" :key="elementKey">
                        <input :value="element.element_id" name="element" class="group rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                        <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ element.element_id }}">{{ element.element_title }}</label>
                    </div>
                </div>

                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
                @click="storeVoteResponse()">
                    Submit vote
                </button>
                
            </div>
        </Modal>
    </teleport>

    <teleport to="#projectModals">
        <Modal :show="showUserTaskModal" >
            <div v-if="showUserTaskModal" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                <div class="flex justify-end">
                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                        <div @click="showUserTaskModal = false; clearModal()">
                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                        </div>   
                    </div>
                </div>

                <div>
                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8 w-10/12">{{ taskOwner }} assigned task <span class="italic text-we4vGrey-600">{{ taskName }}</span> to 
                        <span v-if="taskUserId === $page.props.authUser.id" class="italic text-we4vGrey-600">you</span>
                        <span v-if="!taskUserId && taskTeamName" class="italic text-we4vGrey-600">{{ taskTeamName }}</span>
                        <span v-if="!taskUserId && taskTeamName" class="uppercase text-we4vBlue font-semibold mb-4 -mt-8 w-10/12">, one of the teams you are in</span>.</h4>
                    <p class="text-we4vGrey-500 text-sm">Deadline: <span class="italic font-medium">{{ taskEndDate }}</span></p>
                    <p class="text-we4vGrey-500 text-sm">Description: <span class="italic font-medium">{{ taskDescription }}</span></p>
                </div>


                <div v-if="taskNotes">
                    <Notes :notes="taskNotes" />
                </div>

                <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a note</h5>
                <textarea v-model="taskNoteBody" name="taskNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none"></textarea>

                <input v-model="taskCompleted" class="rounded-sm border-indigo-100 shadow-sm focus:outline-none" type="checkbox" >
                <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ taskCompleted }}">Task completed</label>

                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
                @click="updateTask()">
                    Update task
                </button>
                
            </div>
        </Modal>
    </teleport>

    <div class="w-1/4 h-screen p-2 pt-4 bg-we4vGrey-800 text-we4vBg font-light">
        <div class="text-l tracking-tight mb-1 text-right text-sm">Association requests: 
            <span 
            v-if="$page.props.myPendingAssocReqs.length > 0" class="text-xl font-extrabold text-we4vOrange cursor-pointer"
            @click="showAssocReqs = !showAssocReqs">
                +
            </span>
            <span v-else class="font-extrabold text-we4vGreen-500">
                0
            </span>
        </div>
        
        <div v-if="showAssocReqs">
            <pending-assoc-reqs v-for="(req, reqKey) in $page.props.myPendingAssocReqs" :key="reqKey" :req="req" />
        </div>

        <div class="tracking-tight mb-1 text-right text-sm">Group & team invitations: 
            <span 
            v-if="$page.props.myPendingMembReqs.length > 0" class="text-xl font-extrabold text-we4vOrange cursor-pointer"
            @click="showMembReqs = !showMembReqs">
                +
            </span>
            <span v-else class="font-extrabold text-we4vGreen-500">
                0
            </span>
        </div>

        <table v-if="showMembReqs" class="text-xs font-light tracking-tighter bg-we4vGrey-700 w-full mb-2 rounded-md">
            <thead class="text-we4vBlue table-fixed border border-we4vGrey-800 text-left">
                <tr class="py-2 px1">
                    <th class="w-2/12 border border-we4vGrey-800 py-2 px-1">From</th>
                    <th class="w-4/12 border border-we4vGrey-800 py-2 px-1">Name</th>
                    <th class="w-1/2 border border-we4vGrey-800 py-2 px-1">Description</th>
                </tr>
            </thead>
            <tbody class="border border-we4vGrey-800 text-we4vGrey-100">
                <pending-memb-reqs @activate-invite-modal="hydrateInviteModal" v-for="(req, reqKey) in $page.props.myPendingMembReqs" :key="reqKey" :req="req" />
            </tbody>
        </table>

        <div class="text-l tracking-tight mb-1 text-right text-sm">Pending votes:  
            <span
            v-if="Object.keys($page.props.myPendingVotes).length > 0" class="text-xl font-extrabold text-we4vOrange cursor-pointer"
            @click="showPendingVotes = !showPendingVotes">
                +
            </span>
            <span v-else class="font-extrabold text-we4vGreen-500">
                0
            </span>
        </div>
        <div v-if="showPendingVotes" class="bg-we4vGrey-700 p-2 rounded-md mb-2 max-h-48 overflow-y-scroll">
            <pending-votes @activate-pending-vote-modal="onActivatePendingVoteModal" v-for="(vote, voteKey) in $page.props.myPendingVotes" :key="voteKey" :vote="vote" />
        </div>

        <h4 @click="showAssocs = !showAssocs" class="tracking-tight mb-1 text-right text-sm cursor-pointer">Associates</h4>
        <div v-if="showAssocs" class="mb-2">
            <Associates />
        </div>

        <h4 @click="showMembs = !showMembs" class="tracking-tight mb-1 text-right text-sm cursor-pointer">Memberships</h4>
        <div v-if="showMembs && $page.props.myMemberships.length" class="mb-2 max-h-48 overflow-y-scroll">
            <Memberships />
        </div>
        <div v-if="showMembs && !$page.props.myMemberships.length" class="tracking-tight text-right -mt-2 mb-1">
            <small class="text-we4vGrey-200 text-xs">You are not yet a member of any group or team</small>
        </div>

        <h4 @click="showUnansweredInvites = !showUnansweredInvites" class="tracking-tight mb-1 text-right text-sm cursor-pointer">Open invitations</h4>
        <div v-if="showUnansweredInvites" class="text-right -mt-2 mb-1">
            <small class="text-we4vGrey-200 text-xs">To be completed</small>
        </div>

        <h4 @click="showTasks = !showTasks" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">Assigned tasks</h4>
        <div v-if="showTasks && Object.keys($page.props.myOpenTasks).length">
            <table v-if="showTasks" class="text-xs font-light tracking-tighter bg-we4vGrey-700 w-full mb-2 rounded-md">
                <thead class="text-we4vBlue table-fixed border border-we4vGrey-800 text-left">
                    <tr>
                        <th class="w-3/12 border border-we4vGrey-800 py-2 px-1">From</th>
                        <th class="w-6/12 border border-we4vGrey-800 py-2 px-1">Name</th>
                        <th class="w-3/2 border border-we4vGrey-800 py-2 px-1">Deadline</th>
                    </tr>
                </thead>
                <tbody class="border border-we4vGrey-800 text-we4vGrey-100">
                    <tr v-for="(task, taskKey) in $page.props.myOpenTasks" :key="taskKey" @click="activateUserTaskModal(task)" class="cursor-pointer hover:bg-we4vBlue">
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ task.owner}}
                        </td>
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ task.name}}
                        </td>
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ task.end_date}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showTasks && !Object.keys($page.props.myOpenTasks).length" class="text-right -mt-2 mb-1">
            <small class="text-we4vGrey-200 text-xs">There are no tasks currently assigned to you</small>
        </div>
    </div>
</template>

<script>
import PendingAssocReqs from '../Pages/Components/PendingAssocReqs'
import PendingMembReqs from '../Pages/Components/PendingMembReqs'
import PendingVotes from '../Pages/Components/PendingVotes'
import Associates from '../Pages/Components/MyAssociates'
import Memberships from '../Pages/Components/MyMemberships'
import Notes from '../Pages/Components/Notes'
import Modal from '../Pages/Components/Modal'
import manageModals from '../Pages/Composables/manageModals'
import { watch, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'SidebarRight',

    components: {
        PendingAssocReqs,
        PendingMembReqs,
        PendingVotes,
        Associates,
        Memberships,
        Modal,
        Notes,
    },

    data: () => {
        return {
            showAssocReqs: false,
            showAssocs: false,
            showMembs: false,
            showTasks: false,
            showPendingVotes: false,
        }
    },

    props: [
        'success',
        'errors',
    ],

    emits: [
        'activateUserTaskModal'
    ],

    setup(props) {
        const {
            activateUserTaskModal,
            amOutside, 
            amInside,
            clearModal,
            gAdmin,
            geogArea,
            groupDescription,
            groupId,
            groupName,
            groupOwner,
            groupRequester,
            groupRole,
            hydrateInviteModal,
            inviteData,
            mode,
            nowInside, 
            nowOutside, 
            onActivatePendingVoteModal,
            onClickOutside,
            showInviteModal,
            showPendingVoteModal,
            showUserTaskModal,
            tAdmin,
            taskableId,
            taskableType,
            taskCompleted,
            taskDescription,
            taskEndDate,
            taskGroupName,
            taskId,
            taskInputEndDate,
            taskName,
            taskNotes,
            taskOwner,
            taskTeamName,
            taskUserId,
            teamFunction,
            teamName,
            teamOwner,
            teamId,
            teamRequester,
            teamRole,
            type,
            updated,
            voteClosingDate,
            voteElements,
            voteId,
            voteOwner,
            voteTitle,
        } = manageModals()

        const showUnansweredInvites = ref(false)
        const showMembReqs = ref(false)
        const taskNoteBody = ref(null)

        const storeInviteResponse = async (inviteData, res) => {
            if (inviteData[0].type === 'group') {
                var payload = {
                    'membershipable_id': inviteData[0].groupId,
                    'confirmed': res
                }
            }
            if (inviteData[0].type === 'team') {
                var payload = {
                    'membershipable_id': inviteData[0].teamId,
                    'confirmed': res
                }
            }

            try {
                await Inertia.post('/memberships/accept-reject', payload)
                props.errors = null
            } catch(err) {
                props.errors = err
            }

            showMembReqs.value = false
            clearModal()
        }

        const storeVoteResponse = async () => {
            let selectedElement = document.querySelector('input[name="element"]:checked').value

            var payload = {
                'vote_id': voteId.value,
                'element_id': selectedElement,
                'user_id': usePage().props.value.authUser.id
            }

            try {
                await Inertia.post('/cast-vote/store', payload)
                props.errors = null
            } catch(err) {
                props.errors = err
            }
            
            usePage().props.value.myPendingVotes.length === 0
            ? showPendingVotes.value = false
            : null

            clearModal()
        }

        const updateTask = async function () {
            let note = {
                'body': taskNoteBody.value,
                'noteable_id': taskId.value,
                'noteable_type': 'App\\Models\\Task'
            }

            let payload = {
                'id': taskId.value,
                'completed': taskCompleted.value,
                'taskable_id': taskableId.value,
                'taskable_type': taskableType.value,
                'end_date': taskInputEndDate.value,
                'user_id': usePage().props.value.authUser.id,
                'note': note
            }
            
            try {
                await Inertia.patch('/mytasks/update', payload)
                props.errors = null
            } catch (err) {
                props.errors = err
            }
            
            clearModal()
            taskNoteBody.value = null
        }

        watch(amOutside, () => {
            if (amOutside.value && !amInside.value) {
                document.body.addEventListener('click', onClickOutside, true)
            }
        })

        return {
            activateUserTaskModal,
            amOutside, 
            amInside,
            clearModal,
            gAdmin,
            geogArea,
            groupId,
            groupName,
            groupOwner,
            groupDescription,
            groupRequester,
            groupRole,
            hydrateInviteModal,
            inviteData,
            nowInside, 
            nowOutside,
            onActivatePendingVoteModal,
            onClickOutside,
            mode,
            showInviteModal,
            showMembReqs,
            showPendingVoteModal,
            showUnansweredInvites,
            showUserTaskModal,
            storeInviteResponse,
            storeVoteResponse,
            taskableId,
            taskableType,
            taskCompleted,
            taskDescription,
            taskEndDate,
            taskGroupName,
            taskId,
            taskInputEndDate,
            taskName,
            taskNoteBody,
            taskNotes,
            taskOwner,
            taskTeamName,
            taskUserId,
            tAdmin,
            teamFunction,
            teamId,
            teamName,
            teamOwner,
            teamRequester,
            teamRole,
            type,
            updated,
            updateTask,
            voteClosingDate,
            voteElements,
            voteId,
            voteOwner,
            voteTitle,
        }
    },

}
</script>