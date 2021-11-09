<template>
    <teleport to="#membRequestModals">
        <Modal :show="showInviteModal" >
            <div @mouseleave="nowOutside(); mode = type" @mouseenter="nowInside(); mode = ''" v-if="showInviteModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                <div class="flex justify-end">
                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                        <div @click="showInviteModal = false; clearModal()">
                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                        </div>   
                    </div>
                </div>

                <h4 class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ groupRequester ? groupRequester : teamRequester }} cordially invites you to join the {{ type }} </h4> 
                <h3 class="font-serif font-semibold text-we4vBlue text-center text-2xl">{{ groupName ? groupName : teamName }}</h3>

                <h5 class="text-we4vGrey-700 text-sm mt-4">Description: {{ groupDescription ? groupDescription : teamFunction  }}</h5>
                <h5 v-if="geogArea" class="text-we4vGrey-700 text-sm mt-1">Geographical area: {{ geogArea }}</h5>
                <h5 v-if="groupRole || teamRole" class="text-we4vGrey-700 text-sm mt-1">Proposed role: {{ groupRole ? groupRole : teamRole }}</h5>
                
                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
                @click="storeInviteResponse(req, true)">
                    Accept
                </button>
                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-red-600 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none my-4 py-2"
                @click="storeInviteResponse(req, false)">
                    Reject
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

        <div class="text-l tracking-tight mb-1 text-right text-sm">Membership requests: 
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

        <h4 @click="showAssocs = !showAssocs" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">My associates</h4>
        <div v-if="showAssocs">
            <Associates/>
        </div>

        <h4 @click="showMembs = !showMembs" class="text-l tracking-tight mb-1 text-right text-sm cursor-pointer">My memberships</h4>
        <div v-if="showMembs">
            Stuff here
        </div>
    </div>
</template>

<script>
import PendingAssocReqs from '../Pages/Components/PendingAssocReqs'
import PendingMembReqs from '../Pages/Components/PendingMembReqs'
import Associates from '../Pages/Components/MyAssociates'
import Modal from '../Pages/Components/Modal'
import manageModals from '../Pages/Composables/manageModals'
import { watch, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default {
    name: 'SidebarRight',

    components: {
        PendingAssocReqs,
        PendingMembReqs,
        Associates,
        Modal,
    },

    data: () => {
        return {
            showAssocReqs: false,
            showMembReqs: false,
            showAssocs: false,
            showMembs: false
        }
    },

    setup() {
        const {
            amOutside, 
            amInside,
            clearModal,
            geogArea,
            groupDescription,
            groupId,
            groupName,
            groupRequester,
            groupRole,
            hydrateInviteModal,
            mode,
            nowInside, 
            nowOutside, 
            onClickOutside,
            showInviteModal,
            teamFunction,
            teamName,
            teamId,
            teamRequester,
            teamRole,
            type
        } = manageModals()

        const storeInviteResponse = async (req, res) => {
            if (req.type === 'group') {
                var payload = {
                    'membershipable_id': req.groupId,
                    'requester': req.groupRequesterId,
                    'membershipable_type': 'App\\Models\\Group',
                    'confirmed': res
                }
            }
            if (req.type === 'team') {
                var payload = {
                    'membershipable_id': req.teamId,
                    'requester': req.teamRequesterId,
                    'membershipable_type': 'App\\Models\\Team',
                    'confirmed': res
                }
            }
            await Inertia.post('/memberships/accept-reject', payload)
            .catch(e => console.log(e))
            showInviteModal.value = false
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
            geogArea,
            groupId,
            groupName,
            groupDescription,
            groupRequester,
            groupRole,
            hydrateInviteModal,
            nowInside, 
            nowOutside, 
            onClickOutside,
            storeInviteResponse,
            mode,
            showInviteModal,
            teamFunction,
            teamId,
            teamName,
            teamRequester,
            teamRole,
            type
        }
    },

}
</script>