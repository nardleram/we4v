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

                <h4 v-if="!updated" class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ groupRequester ? groupRequester : teamRequester }} cordially invites you to join the {{ type }} </h4>
                <h4 v-else class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ groupRequester ? groupRequester : teamRequester }} has updated the details of your membership of the {{ type }} </h4>
                <h3 class="font-serif font-semibold text-we4vBlue text-center text-2xl">{{ groupName ? groupName : teamName }}</h3>

                <h5 class="text-we4vGrey-700 text-sm mt-4">Description: {{ groupDescription ? groupDescription : teamFunction  }}</h5>
                <h5 v-if="geogArea" class="text-we4vGrey-700 text-sm mt-1">Geographical area: {{ geogArea }}</h5>
                <h5 v-if="groupRole || teamRole" class="text-we4vGrey-700 text-sm mt-1">Proposed role: {{ groupRole ? groupRole : teamRole }}</h5>
                <h5 v-if="gAdmin || tAdmin" class="text-we4vOrange text-sm font-medium mt-1">{{ groupRequester ? groupRequester : teamRequester }} invites you to be the {{ type }}’s administrator.</h5>
                
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
                    <p class="text-we4vGrey-500 text-sm">{{ voteOwner }} asks you to cast your vote on: <span class="italic font-medium">{{ voteTitle }}</span></p>
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

    <div class="w-1/4 h-screen p-2 pt-4 bg-we4vGrey-800 text-we4vBg font-light">
        <div class="text-l tracking-tight mb-1 text-right text-sm">Association requests: 
            <span 
            v-if="$page.props.myPendingAssocReqs.length > 0" class="text-xl font-extrabold text-we4vOrange cursor-pointer"
            @click="showReqs = !showReqs">
                +
            </span>
            <span v-else class="font-extrabold text-we4vGreen-500">
                0
            </span>
        </div>
        <div v-if="showAssocReqs">
            <pending-assoc-reqs v-for="(req, reqKey) in $page.props.myPendingAssocReqs" :key="reqKey" :req="req" />
        </div>

        <div class="text-l tracking-tight mb-1 text-right text-sm">Group & team invitations: 
            <span 
            v-if="$page.props.myPendingMembReqs.length > 0" class="text-xl font-extrabold text-we4vOrange cursor-pointer"
            @click="showMembReqs = !showMembReqs">
                +
            </span>
            <span v-else class="font-extrabold text-we4vGreen-500">
                0
            </span>
        </div>
        <table v-if="showMembReqs" class="text-xs font-light tracking-tighter text-we4vGrey-700 w-full mb-2">
            <thead class="text-we4vBlue table-fixed border border-we4vGrey-700 text-left">
                <tr class="py-2 px1">
                    <th class="w-2/12 border border-we4vGrey-700">From</th>
                    <th class="w-4/12 border border-we4vGrey-700">Name</th>
                    <th class="w-1/2 border border-we4vGrey-700">Description</th>
                </tr>
            </thead>
            <tbody class="border border-we4vGrey-700 text-we4vGrey-100">
                <pending-memb-reqs @activate-invite-modal="hydrateInviteModal" v-for="(req, reqKey) in $page.props.myPendingMembReqs" :key="reqKey" :req="req" />
            </tbody>
        </table>

        <div class="text-l tracking-tight mb-1 text-right text-sm">Votes:  
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

        <h4 @click="showAssocs = !showAssocs" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">Associates</h4>
        <div v-if="showAssocs" class="mb-2">
            <Associates />
        </div>

        <h4 @click="showMembs = !showMembs" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">Memberships</h4>
        <div v-if="showMembs">
            <small>To be completed</small>
        </div>

        <h4 @click="showUnansweredInvites = !showUnansweredInvites" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">Unanswered invitations</h4>
        <div v-if="showUnansweredInvites">
            <small>To be completed</small>
        </div>

        <h4 @click="showTasks = !showTasks" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">Tasks</h4>
        <div v-if="showTasks">
            <small>My tasks</small>
        </div>
    </div>
</template>

<script>
import PendingAssocReqs from '../Pages/Components/PendingAssocReqs'
import PendingMembReqs from '../Pages/Components/PendingMembReqs'
import PendingVotes from '../Pages/Components/PendingVotes'
import Associates from '../Pages/Components/MyAssociates'
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
        Modal,
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
        'success'
    ],

    setup() {
        const {
            amOutside, 
            amInside,
            clearModal,
            gAdmin,
            geogArea,
            groupDescription,
            groupId,
            groupName,
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
            tAdmin,
            teamFunction,
            teamName,
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
        const error = ref(null)

        const storeInviteResponse = async (inviteData, res) => {
            if (inviteData[0].type === 'group') {
                var payload = {
                    'membershipable_id': inviteData[0].groupId,
                    'requester': inviteData[0].groupRequesterId,
                    'membershipable_type': 'App\\Models\\Group',
                    'confirmed': res
                }
            }
            if (inviteData[0].type === 'team') {
                var payload = {
                    'membershipable_id': inviteData[0].teamId,
                    'requester': inviteData[0].teamRequesterId,
                    'membershipable_type': 'App\\Models\\Team',
                    'confirmed': res
                }
            }

            try {
                await Inertia.post('/memberships/accept-reject', payload)
                error.value = null
            } catch(err) {
                error.value = err
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
                error.value = null
            } catch(err) {
                error.value = err
            }

            showPendingVoteModal.value = false
            clearModal()
        }

        watch(amOutside, () => {
            if (amOutside.value && !amInside.value) {
                document.body.addEventListener('click', onClickOutside, true)
            }
        })

        return {
            amOutside, 
            amInside,
            clearModal,
            error,
            gAdmin,
            geogArea,
            groupId,
            groupName,
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
            showPendingVoteModal,
            showMembReqs,
            showUnansweredInvites,
            storeInviteResponse,
            storeVoteResponse,
            tAdmin,
            teamFunction,
            teamId,
            teamName,
            teamRequester,
            teamRole,
            type,
            updated,
            voteClosingDate,
            voteElements,
            voteId,
            voteOwner,
            voteTitle,
        }
    },

}
</script>