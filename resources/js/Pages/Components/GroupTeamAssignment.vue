<template>
    <div v-if="groupData[0].groupMembers" class="w-full m-0 tracking-tight">
        <h4 class="text-xs font-medium text-we4vBlue mt-1">Group members (of {{ groupData[0].group_name }})</h4>
        <div v-for="(groupMember, groupMemberKey) in groupData[0].groupMembers" :key="groupMemberKey">
            <div v-if="groupMember.confirmed" class="text-xs mt-1">
                <label class="text-we4vGrey-500 text-xs mr-2 w-full text-center" for="groupMember">{{ groupMember.username }}</label>
                <input @click="sendTaskData(groupData[0].group_id, 'App\\Models\\Group', groupMember.user_id)" type="radio" name="task" :checked="assignee === groupMember.username && !groupMember.declined">
            </div>
            <div v-if="!groupMember.confirmed && groupData[0].groupMembers.length === 1" class="text-xs mt-1">
                <p class="text-we4vGrey-500 text-xs mt-1">There are no (confirmed) group members in {{ groupData[0].group_name }}. <span v-if="!groupData[0].teams">Nor are there any teams. Please first add group members or teams with members to this group to be able to assign tasks.</span></p>
            </div>
        </div>
    </div>

    <div v-if="groupData[0].teams" class="w-full m-0 tracking-tight">
        <div v-for="(team, teamKey) in groupData[0].teams" :key="teamKey" class="grid gap-1 grid-cols-12 mt-2">
            <div class="row-span-1 row-end-1 col-start-1 col-end-3">
                <h4 class="text-xs font-medium text-we4vBlue">Teams</h4>
            </div>
            <div class="row-span-1 row-end-1 col-start-4 col-end-12">
                <h4 class="text-xs font-medium text-we4vBlue m-0 p-0">Team members</h4>
            </div>
            <div class="row-start-2 -row-end-1 col-start-1 col-end-3 items-center">
                <label class="text-we4vGrey-600 text-xs mr-2 w-full p-0 text-center" for="team">{{ team.team_name }}</label>
                <input @click="taskableId = team.team_id; taskableType = 'App\\Models\\Team'" type="radio" name="task" :value="team.team_id" :checked="assignee === team.team_name">
            </div>
            <div v-if="team.teamMembers" class="row-span-1 col-start-4 col-span-3 m-0 p-0">
                <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey" class="p-0 m-0">
                    <div v-if="teamMember.confirmed" class="p-0 m-0">
                        <label class="text-we4vGrey-600 text-xs mr-2 p-0 w-full text-center" for="teamMember">{{ teamMember.username }}</label>
                        <input @click="taskableId = team.team_id; taskableType = 'App\\Models\\Team'; userId = teamMember.user_id" type="radio" name="task" :value="teamMember.user_id" :checked="assignee === teamMember.username && !teamMember.declined">
                    </div>
                    <div v-if="!teamMember.confirmed && team.teamMembers.length === 1" class="text-xs mt-1">
                        <p class="text-we4vGrey-600 text-xs mt-1">{{ team.team_name }} contains no confirmed members.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'GroupTeamAssignment',

    props: [ 
        'groupData',
        'taskableId',
        'taskableType',
        'assignee',
        'userId'
    ],

    emits: [
        'sendTaskData'
    ],

    methods: {
        sendTaskData (taskableId, taskableType, userId) {
            let taskData = {
                'taskableId': taskableId,
                'taskableType': taskableType,
                'userId': userId
            }
            this.$emit('sendTaskData', taskData)
        }
    }
}
</script>