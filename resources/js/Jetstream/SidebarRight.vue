<template>
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
            <pending-assoc-reqs v-for="(req, reqKey) in $page.props.myPendingAssocReqs" :key="reqKey" :req="req"/>
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
                <pending-memb-reqs v-for="(req, reqKey) in $page.props.myPendingMembReqs" :key="reqKey" :req="req"/>
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

export default {
    name: 'SidebarRight',

    components: {
        PendingAssocReqs,
        PendingMembReqs,
        Associates
    },

    data: () => {
        return {
            showAssocReqs: false,
            showMembReqs: false,
            showAssocs: false,
            showMembs: false
        }
    },

}
</script>