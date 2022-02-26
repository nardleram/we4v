<template>
    <div v-if="groupData.length > 0">
        <div v-if="groupData[0].groupMembers" class="w-full m-0 tracking-tight">
            <h4 class="text-xs font-medium text-we4vBlue mt-1">Group members (of {{ groupData[0].group_name }})</h4>
            <div v-for="(groupMember, groupMemberKey) in groupData[0].groupMembers" :key="groupMemberKey">
                <div v-if="groupMember.confirmed" class="text-xs mt-1">
                    <label class="text-we4vGrey-500 text-xs mr-2 w-full text-center" for="groupMember">{{ groupMember.username }}</label>
                    <input @click="emitTaskData(groupData[0].group_id, 'App\\Models\\Group')" type="checkbox" name="task" class="selectedMembers" :checked="assignees.includes(groupMember.username) && !groupMember.declined">
                </div>
                
                <div v-if="!groupMember.confirmed && groupData[0].groupMembers.length === 1" class="text-xs mt-1">
                    <p class="text-we4vGrey-500 text-xs mt-1">There are no (confirmed) group members in {{ groupData[0].group_name }}. <span v-if="!groupData[0].teams">Nor are there any teams. Please first add group members or teams with members to this group to be able to assign tasks.</span></p>
                </div>
            </div>
        </div>

        <div v-if="groupData[0].teams" class="w-full m-0 tracking-tight">
            <div v-for="(team, teamKey) in groupData[0].teams" :key="teamKey" class="grid gap-1 grid-cols-12 mt-2">
                <div class="row-span-1 row-end-1 col-start-1 col-end-3">
                    <h4 class="text-xs font-medium text-we4vBlue">Teams (from my groups)</h4>
                </div>
                <div class="row-span-1 row-end-1 col-start-4 col-end-12">
                    <h4 class="text-xs font-medium text-we4vBlue m-0 p-0">Team members</h4>
                </div>
                <div class="row-start-2 -row-end-1 col-start-1 col-end-3 items-center">
                    <label class="text-we4vGrey-600 text-xs mr-2 w-full p-0 text-center" for="team">{{ team.team_name }}</label>
                    <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team')" type="radio" :value="team.team_id">
                </div>
                <div v-if="team.teamMembers" class="row-span-1 col-start-4 col-span-3 m-0 p-0">
                    <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey" class="p-0 m-0">
                        <div v-if="teamMember.confirmed" class="p-0 m-0">
                            <label class="text-we4vGrey-600 text-xs mr-2 p-0 w-full text-center" for="teamMember">{{ teamMember.username }}</label>
                            <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team')" type="checkbox" :value="teamMember.user_id" class="selectedMembers">
                        </div>
                        <div v-if="!teamMember.confirmed && team.teamMembers.length === 1" class="text-xs mt-1">
                            <p class="text-we4vGrey-600 text-xs mt-1">{{ team.team_name }} contains no confirmed members.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="teamData[0]" class="w-full m-0 tracking-tight">
        <div v-for="(team, teamKey) in teamData" :key="teamKey" class="grid gap-1 grid-cols-12 mt-2 max-h-32 overflow-y-scroll">
            <div class="row-span-1 row-end-1 col-start-1 col-end-3">
                <h4 class="text-xs font-medium text-we4vBlue">Teams<span class="text-we4vOrange font-bold">*</span></h4>
            </div>
            <div class="row-span-1 row-end-1 col-start-4 col-end-12">
                <h4 class="text-xs font-medium text-we4vBlue m-0 p-0">Team members</h4>
            </div>
            <div class="row-start-2 -row-end-1 col-start-1 col-end-3 items-center overflow-y-scroll">
                <label class="text-we4vGrey-600 text-xs mr-2 w-full p-0 text-center" for="team">{{ team.team_name }}</label>
                <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team')" type="radio" :value="team.team_id">
            </div>
            <div v-if="team.teamMembers" class="row-span-1 col-start-4 col-span-9 m-0 p-0 flex flex-row flex-wrap">
                <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey" class="p-0 m-0">
                    <div v-if="teamMember.confirmed" class="p-0 m-0 mb-2 mr-2">
                        <label class="text-we4vGrey-600 text-xs mr-2 p-0 w-full text-center" for="teamMember">{{ teamMember.username }}</label>
                        <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team')" type="checkbox" :value="teamMember.user_id" class="selectedMembers" :checked="assignees.some(assignee => assignee.username === teamMember.username)">
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
        'teamData',
        'taskableId',
        'taskableType',
        'assignees',
        'userId',
    ],

    emits: [
        'sendTaskData'
    ],

    setup(props, { emit }) {
        const emitTaskData = (taskableId, taskableType) => {
            let taskMemberships = []

            let myVals = document.querySelectorAll('input.selectedMembers')

            // Users may send a new selection of associates as task assignees.
            // This new selection may reflect deselected (unassigned-from-task) associates.
            // In action UpdateTaskMemberships.php, I've used a delete-every-membership-
            // with-relevant-membershipable_id line of code, followed by a create section that
            // 'simulates' an update by using the relevant membership's original created_at value
            // with a now() value for updated_at. Cosmetic, but cute methinks.

            myVals.forEach(myVal => {
                if (myVal.checked) {
                    let user = (props.assignees.find(assignee => assignee.user_id === myVal.value))
                    user
                    ? taskMemberships.push({ 
                        'user_id': myVal.value, 
                        'role': null, 
                        'admin': false, 
                        'created_at': user.created_at 
                    })
                    : taskMemberships.push({ 
                        'user_id': myVal.value, 
                        'role': null, 
                        'admin': false, 
                        'created_at': null 
                    })
                }
            })

            let taskData = {
                'taskableId': taskableId,
                'taskableType': taskableType,
                'taskMemberships': taskMemberships
            }

            emit('sendTaskData', taskData)
        }

        return { emitTaskData }
    },
}
</script>