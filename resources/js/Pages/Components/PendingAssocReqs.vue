<template>
    <div class="p-1 flex items-center justify-between">
        <div class="flex items-center pt-1">
            <div>
                <inertia-link :href="route('user-show', req.slug_requester)" as="button">
                    <img :src="'/storage/'+req.requester_profile_photo" :alt="req.requester" 
                    class="rounded-full object-cover w-9 h-9">
                </inertia-link>
            </div>
            <div class="z-30 -ml-2 mr-1">
                <connection-dots class="h-10 w-10"/>
            </div>
            <div class="z-0 -ml-2">
                <inertia-link :href="route('user-show', req.slug_requestee)" as="button">
                    <img :src="'/storage/'+req.requestee_profile_photo" :alt="req.requestee" 
                    class="rounded-full object-cover w-9 h-9">
                </inertia-link>
            </div>
        </div>
        <div v-if="req.myResponseNeeded" class="flex pr-1">
            <button @click="acceptAssocRequest(req)" class="rounded-md font-semibold bg-we4vGreen-600 text-we4vBg py-2 text-xs w-14 mr-1">
                Accept
            </button>
            <button @click="rejectAssocRequest(req)" class="rounded-md font-semibold bg-red-700 text-we4vBg py-2 text-xs w-14">
                Reject
            </button>
        </div>
        <div v-else class="pr-1">
            <button 
            class="font-semibold text-xs tracking-tight rounded-lg bg-we4vGrey-900 text-we4vBlue w-full p-2 border border-we4vBlue cursor-default">
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
            flashMessage.value = true
        },

        rejectAssocRequest(req) {
            let payload = {
                'id': req.id,
                'requested_of': req.requested_of,
                'requested_by': req.requested_by,
                'status': 0
            }
            this.$inertia.post('/associate-request-response', payload)
            flashMessage.value = true
        }
    }

}
</script>

<style scoped>

</style>