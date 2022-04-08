<template>
    <div v-if="groupData.length > 0">
        <div v-if="groupData[0].groupMembers" class="w-full m-0 tracking-tight">
            <h4 class="text-sm font-medium text-we4vGrey-600 mt-1">Group members (<span class="italic">{{ groupData[0].group_name }}</span>)</h4>
            <div class="flex flex-row flex-wrap w-full justify-start">
                <div v-for="(groupMember, groupMemberKey) in groupData[0].groupMembers" :key="groupMemberKey" class="mr-1">
                    <div v-if="groupMember.confirmed" class="text-xs mr-3">
                        <input @click="emitTaskData(taskableId = groupData[0].group_id, taskableType = 'App\\Models\\Group', 'cbxGroup')" type="checkbox" class="selectedMembers" :class="'checkbox_'+groupData[0].group_id" :checked="taskMembers.some(taskMember => taskMember.username === groupMember.username) && !groupMember.declined" :value="groupMember.user_id">
                        <label class="text-we4vGrey-500 text-xs ml-1" for="groupMember">{{ groupMember.username }}</label>
                    </div>
                    
                    <div v-if="!groupMember.confirmed && groupData[0].groupMembers.length === 1" class="text-xs mt-1">
                        <p class="text-we4vGrey-500 text-xs mt-1">There are no confirmed group members in {{ groupData[0].group_name }}. <span v-if="!groupData[0].teams">There are no confirmed team members. </span>Please wait for associates to confirm their membership before assigning tasks.</p>
                    </div>
                </div>
            </div>
        </div>

        <h4 v-if="groupData[0].teams" class="text-sm font-medium text-we4vGrey-600 mt-3 -mb-1">Teams</h4>
        <div v-if="groupData[0].teams" class="w-full tracking-tight">
            <div v-for="(team, teamKey) in groupData[0].teams" :key="teamKey" class="grid grid-cols-12">
                <div class="row-span-1 row-end-1 col-start-1 col-end-4">
                    <h4 class="text-xs font-medium text-we4vBlue">{{team.team_name}}</h4>
                </div>
                <div class="row-span-1 row-end-1 col-start-4 col-end-12 pl-1">
                    <h4 class="text-xs font-medium text-we4vBlue">{{team.team_name}} members</h4>
                </div>
                <div class="row-start-2 -row-end-1 col-start-1 col-end-4 items-start -mt-2 pl-1 pb-1">
                    <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team', 'rdo')" type="radio" :checked="taskAssingee === team.team_name" :id="'radio_'+team.team_id" :value="team.team_id" name="wholeTeam" class="selectedMembers">
                    <label class="text-we4vGrey-600 text-xs ml-1" for="team">Whole team</label>
                </div>

                <div v-if="team.teamMembers" class="col-start-4 col-end-12 max-h-28 overflow-y-scroll flex flex-row flex-wrap justify-start -mt-2">
                    <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey" class="mr-3">
                        <div v-if="teamMember.confirmed" class="w-full pl-1 pb-1">
                            <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team', 'cbxTeam')" :class="'checkbox_'+team.team_id" class="selectedMembers" type="checkbox" :value="teamMember.user_id" :checked="taskMembers.some(taskMember => taskMember.username === teamMember.username && taskMember.team_name === team.team_name)" >
                            <label class="text-we4vGrey-600 text-xs ml-1" for="teamMember">{{ teamMember.username }}</label>
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
            <div class="row-span-1 row-end-1 col-start-1 col-end-4">
                <h4 class="text-xs font-medium text-we4vBlue m-0 p-0">Teams<span class="text-we4vOrange font-bold">*</span></h4>
            </div>
            <div class="row-span-1 row-end-1 col-start-4 col-end-12">
                <h4 class="text-xs font-medium text-we4vBlue m-0 p-0">Team members</h4>
            </div>
            <div class="row-start-2 -row-end-1 col-start-1 col-end-4 items-center overflow-y-scroll">
                <label class="text-we4vGrey-600 text-xs mr-2 w-full p-0 text-center" for="team">{{ team.team_name }}</label>
                <input @click="emitTaskData(team.team_id, taskableType = 'App\\Models\\Team', 'rdo')" type="radio" :id="'radio_'+team.team_id" :checked="taskAssingee === team.team_name" :value="team.team_id" class="selectedMembers">
            </div>
            <div v-if="team.teamMembers" class="row-span-1 col-start-4 col-span-9 m-0 p-0 flex flex-row flex-wrap">
                <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey" class="p-0 m-0">
                    <div v-if="teamMember.confirmed" class="p-0 m-0 mb-2 mr-2">
                        <label class="text-we4vGrey-600 text-xs mr-2 p-0 w-full text-center" for="teamMember">{{ teamMember.username }}</label>
                        <input @click="emitTaskData(taskableId = team.team_id, taskableType = 'App\\Models\\Team', 'cbxTeam')" type="checkbox" :class="'checkbox_'+team.team_id" :value="teamMember.user_id" class="selectedMembers" :checked="taskMembers.some(taskMember => taskMember.username === teamMember.username && taskMember.team_name === team.team_name)">
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
        'taskMembers',
        'taskAssingee',
    ],

    emits: [
        'sendTaskData'
    ],

    setup(props, { emit }) {
        const emitTaskData = (taskableId, taskableType, inputType) => {
            selectMembersCtrl(taskableId, inputType)

            // Users may send a new selection of associates as task assignees.
            // This new selection may reflect deselected (unassigned-from-task) associates.
            // In the UpdateTaskMemberships.php action, I've used a delete-every-membership-
            // with-relevant-membershipable_id codeblock, followed by a create section that
            // 'simulates' an update by using the relevant membership's original created_at value
            // with a now() value for updated_at. Cosmetic, but cute methinks.
            let taskMemberships = []

            let myVals = document.querySelectorAll('input.selectedMembers')

            myVals.forEach(myVal => {
                if (myVal.checked) {
                    let user = (props.taskMembers.some(member => member.user_id === myVal.value))
                    user
                    ? taskMemberships.push({ 
                        'user_id': myVal.value, 
                        'role': null, 
                        'is_admin': false, 
                        'created_at': user.created_at,
                        'invited': true
                    })
                    : taskMemberships.push({ 
                        'user_id': myVal.value === taskableId ? null : myVal.value, 
                        'role': null, 
                        'is_admin': false, 
                        'created_at': null,
                        'invited': false
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

        const selectMembersCtrl = (taskableId, inputType) => {
            // Control radio-button and checkbox blocks to prevent user from submitting
            // associates from multiple teams or from teams AND group members, etc.
            let cbxMembersToUncheck
            let ids = []

            if ( props.groupData[0].teams) {
                props.groupData[0].teams.forEach(team => {
                    ids.push(team.team_id)
                })
            }
            if ( props.teamData[0]) {
                props.teamData.forEach(team => {
                    ids.push(team.team_id)
                })
            }
            if (props.groupData[0]) {
                ids.push(props.groupData[0].group_id)
            }

            ids.forEach(id => {
                if (inputType === 'rdo') { 
                    cbxMembersToUncheck = document.querySelectorAll('input[type=checkbox]')
                    cbxMembersToUncheck.forEach(member => {
                        if (member.checked) {
                            member.checked = false
                        }
                    })
                }

                if (id !== taskableId && inputType !== 'rdo') { 
                    cbxMembersToUncheck = document.querySelectorAll('input.checkbox_'+id)
                    cbxMembersToUncheck.forEach(member => {
                        if (member.checked) {
                            member.checked = false
                        }
                    })

                    // Check if any radio button selected
                    let radioButtons = document.querySelectorAll('input[type=radio]')

                    radioButtons.forEach(radioButton => {
                        radioButton.checked
                        ? radioButton.checked = false
                        : null
                    })
                }

                if (inputType === 'cbxTeam') {
                    let radioButtons = document.querySelectorAll('input[type=radio]')

                    radioButtons.forEach(radioButton => {
                        radioButton.checked
                        ? radioButton.checked = false
                        : null
                    })
                }
            })
        }

        return { emitTaskData }
    },
}
</script>