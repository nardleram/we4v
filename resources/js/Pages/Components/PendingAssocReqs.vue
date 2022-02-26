<template>
    <div class="p-1 text-right flex flex-1 items-center justify-between mb-2">
        <div class="flex items-center">
            <div>
                <inertia-link :href="route('user-show', req.slug_requester)" as="button">
                    <img :src="'/'+req.requester_profile_photo" :alt="req.requester" 
                    class="rounded-full object-cover w-10 h-10">
                </inertia-link>
            </div>
            <div class="z-30 -ml-2 mb-1">
                <connection-dots class="h-10 w-10"/>
            </div>
            <div class="z-0 -ml-2">
                <inertia-link :href="route('user-show', req.slug_requestee)" as="button">
                    <img :src="'/'+req.requestee_profile_photo" :alt="req.requestee" 
                    class="rounded-full object-cover w-10 h-10">
                </inertia-link>
            </div>
        </div>
        <div v-if="req.myResponseNeeded" class="flex">
            <button @click="acceptAssocRequest(req)" class="rounded-md font-semibold bg-we4vGreen-600 text-we4vBg py-2 text-xs w-14 mr-1">
                Accept
            </button>
            <button @click="rejectAssocRequest(req)" class="rounded-md font-semibold bg-red-700 text-we4vBg py-2 text-xs w-14">
                Reject
            </button>
        </div>
        <div v-else>
            <button 
            class="font-semibold text-xs tracking-tight flex justify-center rounded-lg bg-we4vGrey-900 text-we4vBlue w-full p-2 border border-we4vBlue cursor-default">
                Pending...
            </button>
        </div>
    </div>
</template>

<script>
import ConnectionDots from '../../Jetstream/ConnectionDots'

export default {
    name: 'PendingAssocs',

    components: {
        ConnectionDots,
    },

    props: [
        'req',
    ],

    methods: {
        acceptAssocRequest(req) {
            let payload = {
                'id': req.id,
                'requested_of': req.requested_of,
                'requested_by': req.requested_by,
                'status': 1
            }
            this.$inertia.post('/associate-request-response', payload)
        },
        rejectAssocRequest(req) {
            let payload = {
                'id': req.id,
                'requested_of': req.requested_of,
                'requested_by': req.requested_by,
                'status': 0
            }
            this.$inertia.post('/associate-request-response', payload)
        }
    }

}
</script>

<style scoped>

</style>