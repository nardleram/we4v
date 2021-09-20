<template>
    <teleport to="#membRequestModals">
        <Modal :show="showModal" :name="name" :id="id" :description="description">
            <div v-if="showModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                <div class="flex justify-end">
                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                        <div @click="showModal = false" class="rounded-full hover:shadow-md">
                            <circle-x class="z-50 w-8 h-8 animate-pulse"/>
                        </div>   
                    </div>
                </div>
                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Respond to {{ requester }}’s invitation to join the {{ type }}, <span class="italic font-light text-we4vGrey-600">{{ name }}</span></h4>

                <h4 class="text-we4vGrey-700 text-sm mt-4">Description: {{ description }}</h4>
                
                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
                @click="acceptInvite(req)">
                    Accept
                </button>
                <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-red-600 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none my-4 py-2"
                @click="declineInvite(req)">
                    Reject
                </button>
            </div>
        </Modal>
    </teleport>

    <tr @click="populateModal(req)" v-if="(req.type === 'group')" class="cursor-pointer hover:bg-we4vBlue">
        <td class="px-1 py-2 border border-we4vGrey-600">{{ req.groupRequester }}</td>
        <td class="px-1 py-2 border border-we4vGrey-600">{{ req.groupName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-600">{{ req.groupDesc }} (group)</td>
    </tr>
    <tr @click="populateModal(req)" v-if="(req.type === 'team')" class="cursor-pointer bg-we4vGrey-700 hover:bg-we4vBlue">
        <td class="px-1 py-2 border border-we4vGrey-600">{{ req.teamRequester }}</td>
        <td class="px-1 py-2 border border-we4vGrey-600">{{ req.teamName }}</td>
        <td class="px-1 py-2 border border-we4vGrey-600">{{ req.teamFunc }} (team)</td>
    </tr>
</template>

<script>
import ConnectionDots from '../../Jetstream/ConnectionDots'
import Modal from '../Components/Modal'
import CircleX from '../../Jetstream/CircleX'

export default {
    name: 'PendingMembReqs',

    components: {
        ConnectionDots,
        Modal,
        CircleX,
    },

    props: [
        'req',
        'user'
    ],

    data: () => {
        return {
            showModal: false,
            name: '',
            id: '',
            description: '',
            type: '',
            requester: ''
        }
    },

    methods: {
        populateModal: function (req) {
            this.showModal = true
            if (req.type === 'group') {
                this.name = req.groupName
                this.id = req.groupId
                this.description = req.groupDesc
                this.type = req.type
                this.requester = req.groupRequester
            }
            if (req.type === 'team') {
                this.name = req.teamName
                this.id = req.teamId
                this.description = req.teamFunc
                this.type = req.type
                this.requester = req.teamRequester
            }
        },

        acceptInvite: async function (req) {
            if (req.type === 'group') {
                var payload = {
                    'membershipable_id': req.groupId,
                    'requester': req.groupRequesterId,
                    'membershipable_type': 'App\\Models\\Group',
                    'confirmed': true
                }
            }
            if (req.type === 'team') {
                var payload = {
                    'membershipable_id': req.teamId,
                    'requester': req.teamRequesterId,
                    'membershipable_type': 'App\\Models\\Team',
                    'confirmed': true
                }
            }
            await this.$inertia.post('/memberships/confirm-reject', payload)
            .catch(e => console.log(e))
            this.showModal = false
        }
    },

}
</script>

<style scoped>

</style>