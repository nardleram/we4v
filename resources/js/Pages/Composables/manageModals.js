import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const amInside = ref(false)
const amOutside = ref(false)
const assocCheckboxesAccountedFor = ref(false)
const edit = ref(false)
const geogArea = ref(null)
const greyButtonEnabled = ref(false)
const gAdmin = ref(false)
const groupAdmins = ref([])
const groupId = ref(null)
const groupDescription = ref(null)
const groupMemberRoles = ref([])
const groupMembers = ref([])
const groupMembersEdit = ref([])
const groupName = ref(null)
const groupOwner = ref(null)
const groupRole = ref(null)
const groupRequester = ref(null)
const groupsNotInNetwork = ref([])
const inviteData = ref([])
const membershipId = ref(null)
const mode = ref(null)
const networkId = ref(null)
const networkDescription = ref(null)
const networkGroups = ref([])
const networkName = ref(null)
const networkOwner = ref(null)
const projectCompleted = ref(false)
const projectCompletedAt = ref(null)
const projectDescription = ref(null)
const projectGroupData = ref([])
const projectGroupId = ref(null)
const projectGroupName = ref(null)
const projectGroupSelected = ref(false)
const projectId = ref(null)
const projectName = ref(null)
const projectNotes = ref([])
const projectTasks = ref([])
const projectTeamData = ref([])
const projectTeamId = ref(null)
const projectTeamName = ref(null)
const projectStartDate = ref(null)
const projectEndDate = ref(null)
const projectInputEndDate = ref(null)
const roleFieldsAccountedFor = ref(false)
const selectedAssoc = ref(false)
const selectedGroupAssociates = ref([])
const selectedTeamAssociates = ref([])
const selectedTeamMembers = ref([])
const showBackdrop = ref(false)
const showCompletedProjectModal = ref(false)
const showGroupModal = ref(false)
const showInviteModal = ref(false)
const showEditAdminTaskModal = ref(false)
const showEditNetworkModal = ref(false)
const showEditProjectModal = ref(false)
const showEditTaskModal = ref(false)
const showEditTeamModal = ref(false)
const showEditVoteModal = ref(false)
const showGroup = ref(false)
const showNetworkInviteModal = ref(false)
const showNetworkModal = ref(false)
const showPendingVoteModal = ref(false)
const showProjectModal = ref(false)
const showTaskModal = ref(false)
const showTeamModal = ref(false)
const showTransferGroupModal = ref(false)
const showTransferNetworkModal = ref(false)
const showUserTaskModal = ref(false)
const showVoteModal = ref(false)
const showVoteResultsModal = ref(false)
const tAdmin = ref(false)
const taskableId = ref(null)
const taskableType = ref(null)
const taskAssignee = ref(null)
const taskAssigneeSelected = ref(false)
const taskCompleted = ref(false)
const taskDescription = ref(null)
const taskEndDate = ref(null)
const taskGroupData = ref([])
const taskGroupName = ref([])
const taskId = ref(null)
const taskInputEndDate = ref(null)
const taskName = ref(null)
const taskNotes = ref([])
const taskOwner = ref(null)
const taskProjectId = ref(null)
const taskRecipientType = ref(null)
const taskStartDate = ref(null)
const taskTeamData = ref([])
const taskTeamName = ref([])
const taskMembers = ref([])
const taskProjectName = ref(null)
const teamAdmins = ref([])
const teamFunction = ref(null)
const teamId = ref(null)
const teamMemberRoles = ref([])
const teamMembers = ref([])
const teamMembersEdit = ref([])
const teamName = ref(null)
const teamOwner = ref(null)
const teamRequester = ref(null)
const teamRole = ref(null)
const type = ref(null)
const updated_by = ref(false)
const userId = ref(null)
const userMaySubmitForm = ref(false)
const voteableId = ref(null)
const voteableType = ref(null)
const voteClosingDate = ref(null)
const voteInputClosingDate = ref(null)
const voteElements = ref([])
const voteGroupName = ref(null)
const voteId = ref(null)
const voteOwner = ref(null)
const voteResults = ref([])
const voteTeamName = ref(null)
const voteTitle = ref(null)

const activateShowClosedVoteModal = (vote) => {
    showBackdrop.value = false

    voteTitle.value = vote.vote_title
    voteClosingDate.value = vote.close_date
    voteGroupName.value = vote.group_name
    voteTeamName.value = vote.team_name
    voteResults.value = vote.results

    showBackdrop.value = true
    showVoteResultsModal.value = true
}

const activateShowGroupModal = (group) => {
    showBackdrop.value = false
    groupId.value = group.id
    groupName.value = group.name
    groupDescription.value = group.description
    geogArea.value = group.geog_area
    groupOwner.value = group.owner
    showBackdrop.value = true
}

const activateUserTaskModal = (task) => {
    taskableId.value = task.taskable_id
    taskableType.value = task.taskable_type
    taskCompleted.value = task.completed
    taskId.value = task.id
    taskProjectId.value = task.project_id
    taskName.value = task.name
    taskDescription.value = task.description
    taskEndDate.value = task.end_date
    taskGroupName.value = task.group_name
    taskInputEndDate.value = task.input_end_date
    taskOwner.value = task.owner
    taskMembers.value = task.task_members
    taskNotes.value = task.task_notes
    projectNotes.value = task.project_notes
    taskTeamName.value = task.team_name
    taskProjectName.value = task.project_name

    greyButtonEnabled.value = true
    showUserTaskModal.value = true
    showBackdrop.value = true
}

const checkAssocsAgainstRoles = () => {
    let myAssocVals
    let emptyField = false
    let teamAssocs = 0

    edit.value
    ? myAssocVals = document.querySelectorAll('input.invitedAssocsEdit')
    : myAssocVals = document.querySelectorAll('input.invitedAssocs')

    for (const myAssoc of myAssocVals) {
        if (myAssoc.checked) {
            let elem = document.getElementById(myAssoc.value)
            if (!elem.value) {
                emptyField = true
            } else {
                elem.style.border = 'solid 1px rgb(81, 168, 186)'
                
                mode.value === 'team'
                ? ++teamAssocs
                : null
            }
        }
    }

    emptyField
    ? roleFieldsAccountedFor.value = false
    : roleFieldsAccountedFor.value = true

    teamAssocs === 0 && mode.value === 'team'
    ? roleFieldsAccountedFor.value = false
    : null
}

const checkRolesAgainstAssocs = () => {
    let myRoleVals
    let uncheckedBox = false

    edit.value 
    ? myRoleVals = document.querySelectorAll('input.assocRolesEdit')
    : myRoleVals = document.querySelectorAll('input.assocRoles')

    for (const myRole of myRoleVals) {
        if (myRole.value) {
            let elem = document.getElementById('checkbox_'+myRole.id)
            !elem.checked
            ? uncheckedBox = true
            : null
        }
    }

    uncheckedBox
    ? assocCheckboxesAccountedFor.value = false
    : assocCheckboxesAccountedFor.value = true
}

const checkIfProjectGroupSelected = () => {
    if (document.querySelector('input[name="groupOrTeam"]:checked').value) {
        projectGroupSelected.value = true
    }

    checkIfUserMaySubmit('project')
}

const checkIfRoleInputFieldsFilled = () => {
    checkRolesAgainstAssocs()
    checkAssocsAgainstRoles()
    checkIfUserMaySubmit(mode.value)
}

const checkIfTaskAssigneeSelected = (checkUser) => {
    let myVals = document.querySelectorAll('input.selectedMembers')
    
    myVals.forEach(myVal => {
        if (myVal.checked) {
            taskAssigneeSelected.value = true
            greyButtonEnabled.value = true
        }
    })

    checkUser
    ? checkIfUserMaySubmit('task')
    : null
}

const checkIfUserMaySubmit = (mode) => {
    let elIdName
    let elIdDesc
    let elIdStart
    let elIdEnd
    let name = false
    let description = false
    let start = false
    let end = false
    let requiredFormFields = false

    checkRolesAgainstAssocs()
    checkAssocsAgainstRoles()

    if (mode === 'group') {
        elIdName = 'groupName'
        elIdDesc = 'groupDescription'
    } 
    
    if (mode === 'team') {
        elIdName = 'teamName'
        elIdDesc = 'teamFunction'
    }

    if (mode === 'project') {
        elIdName = 'projectName'
        elIdDesc = 'projectDescription'
        elIdStart = 'projectStartDate'
        elIdEnd = 'projectEndDate'
    }

    if (mode === 'task') {
        elIdName = 'taskName'
        elIdDesc = 'taskDescription'
        elIdStart = 'taskStartDate'
        elIdEnd = 'taskEndDate'
    }

    setTimeout(function() {
        document.getElementById(elIdName).value
        ? name = true
        : name = false
        
        document.getElementById(elIdDesc).value
        ? description = true
        : description = false

        if (mode === 'project' || mode === 'task') {
            if (!edit.value) {
                document.getElementById(elIdStart).value
                ? start = true
                : start = false
            } else {
                start = true
            }
            
            document.getElementById(elIdEnd).value
            ? end = true
            : end = false
        }

        if (mode === 'group' || mode === 'team') {
            name && description
            ? requiredFormFields = true
            : requiredFormFields = false

            !edit.value && mode === 'group'
            ? assocCheckboxesAccountedFor.value = true 
            : null
        }

        if (mode === 'project' || mode === 'task') {
            name && description && start && end
            ? requiredFormFields = true
            : requiredFormFields = false

            // These two are set to true when zero assocs and roles;
            // set to false in the case of projects and tasks
            assocCheckboxesAccountedFor.value = false
            roleFieldsAccountedFor.value = false
        }

        if (mode === 'project' && edit.value && projectTasks.value.length === 0) {
            if (document.querySelector('input[name="groupOrTeam"]:checked').value) {
                projectGroupSelected.value = true
            }
        }

        if ((mode === 'project' || mode === 'task') && edit.value) {
            mode === 'project' && projectTasks.value
            ? projectGroupSelected.value = true
            : null

            if (mode === 'task') {
                let checked = false
                let myVals = document.querySelectorAll('input.selectedMembers')

                myVals.forEach(myVal => {
                    myVal.checked
                    ? checked = true
                    : null
                })
    
                checked
                ? taskAssigneeSelected.value = true
                : taskAssigneeSelected.value = false
            }
        }

        if (mode === 'group') {
            let myVals
            let checked
            edit.value
            ? myVals = document.querySelectorAll('input.invitedAssocsEdit')
            : myVals = document.querySelectorAll('input.invitedAssocs')

            for (const myVal of myVals) {
                myVal.checked
                ? checked = true
                : null
            }

            checked 
            ? null
            : roleFieldsAccountedFor.value = true
        }

        if ((requiredFormFields && assocCheckboxesAccountedFor.value && roleFieldsAccountedFor.value) || 
            (requiredFormFields && projectGroupSelected.value) ||
            (requiredFormFields && taskAssigneeSelected.value)) {
            greyButtonEnabled.value = true
        } else {
            greyButtonEnabled.value = false
        }
    }, 150)
}

const clearModal = () => {
    amInside.value = false
    amOutside.value = false
    assocCheckboxesAccountedFor.value = false
    edit.value = false
    gAdmin.value = false
    greyButtonEnabled.value = false
    geogArea.value = null
    groupAdmins.value = []
    groupDescription.value = null
    groupId.value = null
    groupMemberRoles.value = []
    groupMembers.value = []
    groupMembersEdit.value = []
    groupName.value = null
    groupRequester.value = null
    groupRole.value = null
    inviteData.value = []
    membershipId.value = null
    mode.value = null
    networkId.value = null
    networkDescription.value = null
    networkGroups.value = []
    networkName.value = null
    networkOwner.value = null
    projectCompleted.value = false
    projectCompletedAt.value = null
    projectDescription.value = null
    projectEndDate.value = null
    projectGroupData.value = []
    projectGroupId.value = null
    projectId.value = null
    projectInputEndDate.value = null
    projectName.value = null
    projectTeamId.value = null
    projectStartDate.value = null
    projectTeamData.value = []
    roleFieldsAccountedFor.value = false
    selectedGroupAssociates.value = []
    selectedTeamAssociates.value = []
    selectedTeamMembers.value = []
    tAdmin.value = false
    taskableId.value = null
    taskableType.value = null
    taskCompleted.value = false
    taskDescription.value = null
    taskEndDate.value = null
    taskGroupData.value = []
    taskGroupName.value = null
    taskId.valeu = null
    taskInputEndDate.value = null
    taskName.value = null
    taskNotes.value = []
    taskProjectId.value = null
    taskProjectName.value = null
    taskRecipientType.value = null
    taskStartDate.value = null
    taskTeamData.value = []
    taskTeamName.value = null
    taskMembers.value = []
    teamAdmins.value = []
    teamFunction.value = null
    teamId.value = null
    teamMemberRoles.value = []
    teamName.value = null
    teamOwner.value = null
    teamRequester.value = null
    teamRole.value = null
    showBackdrop.value = false
    showCompletedProjectModal.value = false
    showEditAdminTaskModal.value = false
    showEditProjectModal.value = false
    showEditTaskModal.value = false
    showEditTeamModal.value = false
    showEditVoteModal.value = false
    showGroup.value = false
    showGroupModal.value = false
    showNetworkModal.value = false
    showNetworkInviteModal.value = false
    showEditNetworkModal.value = false
    showTransferGroupModal.value = false
    showTransferNetworkModal.value = false
    showProjectModal.value = false
    showTaskModal.value = false
    showTeamModal.value = false
    showUserTaskModal.value = false
    showVoteModal.value = false
    showVoteResultsModal.value = false
    showPendingVoteModal.value = false
    showInviteModal.value = false
    teamMembersEdit.value = []
    userId.value = null
    updated_by.value = null
    voteableId.value = null
    voteableType.value = null
    voteClosingDate.value = null
    voteElements.value = []
    voteGroupName.value = null
    voteId.value = null
    voteOwner.value = null
    voteResults.value = []
    voteTeamName.value = null
    voteTitle.value = null
}

const hydrateInviteModal = (req) => {
    clearModal()

    amOutside.value = true
    type.value = req.type
    inviteData.value.push(req)

    if (req.type === 'group') {
        gAdmin.value = req.gAdmin
        groupOwner.value = req.groupOwner
        groupName.value = req.groupName
        groupId.value = req.groupId
        groupDescription.value = req.groupDesc
        geogArea.value = req.geogArea
        groupRole.value = req.gRole
        groupRequester.value = req.groupRequester
        groupMembers.value = req.groupMembers
        updated_by.value = req.groupRequester
        membershipId.value = req.membership_id
    }

    if (req.type === 'team') {
        tAdmin.value = req.tAdmin
        teamOwner.value = req.teamOwner
        teamName.value = req.teamName
        teamId.value = req.teamId
        teamFunction.value = req.teamFunc
        teamRole.value = req.tRole
        teamRequester.value = req.teamRequester
        updated_by.value = req.teamRequester
        membershipId.value = req.membership_id
    }

    showBackdrop.value = true
    showInviteModal.value = true
}

const hydrateNetworkInviteModal = (req) => {
    clearModal()

    amOutside.value = true
    type.value = req.type
    inviteData.value.push(req)

    networkId.value = req.networkId
    networkName.value = req.networkName
    networkDescription.value = req.networkDescription
    networkOwner.value = req.username
    groupName.value = req.groupName
    groupId.value = req.groupId
    groupDescription.value = req.groupDescription

    showBackdrop.value = true
    showNetworkInviteModal.value = true
}

const nowOutside = () => {
    amOutside.value = true
    amInside.value = false
}

const nowInside = () => {
    amOutside.value = false
    amInside.value = true
}

const onActivateEditGroupModal = (group) => {
    edit.value = true
    checkIfUserMaySubmit('group')

    groupMembersEdit.value = [] // Very sticky clingy critter; force emptying here
    groupId.value = group.group_id
    groupName.value = group.group_name
    groupDescription.value = group.group_description
    geogArea.value = group.geog_area
    groupMembers.value = group.groupMembers ? group.groupMembers : []
    showBackdrop.value = true
    showGroupModal.value = true
}

const onActivateEditNetworkModal = (network) => {
    edit.value = true

    usePage().props.value.myGroups.forEach(group => {
        if (!groupsNotInNetwork.value.some(filteredGroup => filteredGroup.group_id === group.group_id)) {
            if (!network.groups.some(nGroup => nGroup.group_id === group.group_id))
            groupsNotInNetwork.value.push(group)
        }
    })

    networkId.value = network.network_id
    networkName.value = network.network_name
    networkDescription.value = network.network_description
    networkGroups.value = network.groups ? network.groups : []

    showBackdrop.value = true
    showEditNetworkModal.value = true
}

const onActivateEditProjectModal = (project) => {
    edit.value = true

    projectCompleted.value = project.project_completed
    projectDescription.value = project.project_description
    projectId.value = project.project_id
    projectGroupId.value = project.project_group_id
    projectTeamId.value = project.project_team_id
    projectGroupName.value = project.project_group_name
    projectTeamName.value = project.project_team_name
    projectName.value = project.project_name
    projectNotes.value = project.notes ? project.notes : []
    projectTasks.value = project.tasks ? project.tasks : []
    projectEndDate.value = project.project_end_date
    projectInputEndDate.value = project.project_input_end_date
    projectStartDate.value = project.project_start_date

    checkIfUserMaySubmit('project')

    showBackdrop.value = true
    showEditProjectModal.value = true
}

// const onActivateEditAdminTaskModal = (task) => {
//     edit.value = true
//     mode.value = 'task'

//     checkIfUserMaySubmit('task')

//     taskCompleted.value = task.task_completed
//     taskId.value = task.task_id
//     taskProjectId.value = task.project_id
//     taskName.value = task.task_name
//     projectNotes.value = task.project_notes ? task.project_notes : null
//     taskNotes.value = task.task_notes ? task.task_notes : null
//     taskDescription.value = task.task_description
//     taskRecipientType.value = 'team'
//     taskEndDate.value = task.task_end_date
//     taskStartDate.value = task.task_start_date
//     taskInputEndDate.value = task.task_input_end_date
//     taskableId.value = task.team_id
//     taskableType.value = 'App\\Models\\Team'
//     taskMembers.value = task.selected_team_members

//     showBackdrop.value = true
//     showEditAdminTaskModal.value = true
// }

const onActivateEditTaskModal = (task) => {
    edit.value = true
    mode.value = 'task'

    checkIfUserMaySubmit('task')

    usePage().props.value.myGroups.forEach(mygroup => {
        if (mygroup.group_id === task.project_group_id) {
            taskGroupData.value.push(mygroup)
        }
    })
    usePage().props.value.myAdminTeams.forEach(myteam => {
        if (myteam.team_id === task.taskable_id) {
            taskTeamData.value.push(myteam)
        }
    })
    
    taskCompleted.value = task.task_completed
    taskId.value = task.task_id
    taskAssignee.value = task.assignee
    projectId.value = task.project_id
    taskName.value = task.task_name
    projectNotes.value = task.project_notes ? task.project_notes : []
    taskNotes.value = task.notes ? task.notes : []
    taskDescription.value = task.task_description
    taskRecipientType.value = task.recipient_type
    taskEndDate.value = task.task_end_date
    taskStartDate.value = task.task_start_date
    taskInputEndDate.value = task.task_input_end_date
    taskableId.value = task.taskable_id
    taskableType.value = task.taskable_type
    if (task.team_members) {
        task.team_members.forEach(member => {
            taskMembers.value.push({ 
                username: member.task_member_username, 
                created_at: member.task_member_created_at,
                user_id: member.task_member_user_id,
                team_name: member.task_team_name
            })
        }) 
    } else if (task.selected_task_members) {
        task.selected_task_members.forEach(member => {
            taskMembers.value.push({ 
                username: member.task_member_username, 
                created_at: member.task_member_created_at,
                user_id: member.task_member_user_id,
                team_name: member.task_team_name
            })
        }) 
    }
    
    showBackdrop.value = true
    showEditTaskModal.value = true
}

const onActivateEditTeamModal = (team) => {
    clearModal()
    edit.value = true

    checkIfUserMaySubmit('team')

    groupId.value = team.group_id
    teamId.value = team.team_id
    teamName.value = team.team_name
    teamFunction.value = team.team_function
    teamOwner.value = team.team_owner
    teamMembers.value = team.teamMembers
    showBackdrop.value = true
    showEditTeamModal.value = true
}

const onActivateEditVoteModal = (vote) => {
    edit.value = true

    voteTitle.value = vote.vote_title
    voteId.value = vote.vote_id
    voteableId.value = vote.voteable_id
    voteableType.value = vote.voteable_type
    voteInputClosingDate.value = vote.input_closing_date

    showBackdrop.value = true
    showEditVoteModal.value = true
}

const onActivatePendingVoteModal = (vote) => {
    voteTitle.value = vote.vote_title
    voteId.value = vote.vote_id
    voteClosingDate.value = vote.closing_date
    voteElements.value = vote.elements
    voteOwner.value = vote.vote_owner

    showBackdrop.value = true
    showPendingVoteModal.value = true
}

const onActivateShowCompletedProjectModal = (project) => {
    projectCompleted.value = project.project_completed
    projectDescription.value = project.project_description
    projectName.value = project.project_name
    projectCompletedAt.value = project.project_updated_at
    projectId.value = project.project_id
    projectGroupId.value = project.project_group_id
    projectTeamId.value = project.project_team_id
    projectGroupName.value = project.project_group_name
    projectTeamName.value = project.project_team_name
    projectNotes.value = project.notes ? project.notes : []
    projectTasks.value = project.tasks ? project.tasks : []
    projectEndDate.value = project.project_end_date
    projectInputEndDate.value = project.project_input_end_date
    projectStartDate.value = project.project_start_date

    showBackdrop.value = true
    showCompletedProjectModal.value = true
}

const onActivateTaskModal = (project) => {
    checkIfUserMaySubmit('task')

    usePage().props.value.myGroups.forEach(mygroup => {
        if (mygroup.group_id === project.project_group_id) {
            projectGroupData.value.push(mygroup)
        }
    })
    usePage().props.value.myAdminTeams.forEach(myteam => {
        if (myteam.team_id === project.project_team_id) {
            projectTeamData.value.push(myteam)
        }
    })
    projectDescription.value = project.project_description
    projectId.value = project.project_id
    projectName.value = project.project_name
    showBackdrop.value = true
    showTaskModal.value = true
}

const onActivateTeamModal = (group) => {
    checkIfUserMaySubmit('team')
    groupId.value = group.group_id
    groupName.value = group.group_name
    groupDescription.value = group.group_description
    geogArea.value = group.geogArea
    groupMembers.value = group.groupMembers
    showBackdrop.value = true
    showTeamModal.value = true
    mode.value = 'team'
}

const onActivateTransferGroupOwnership = (group) => {
    groupId.value = group.group_id
    groupName.value = group.group_name
    showBackdrop.value = true
    showTransferGroupModal.value = true
}

const onActivateTransferNetworkOwnership = (network) => {
    networkId.value = network.network_id
    networkName.value = network.network_name
    showBackdrop.value = true
    showTransferNetworkModal.value = true
}

const onClickOutside = () => {
    if (amOutside.value && !amInside.value) {
        clearModal()

        document.body.removeEventListener('click', onClickOutside, true)
    }
}

const manageModals = () => {
    return {
        activateShowClosedVoteModal,
        activateShowGroupModal,
        activateUserTaskModal,
        amOutside, 
        amInside,
        checkIfProjectGroupSelected,
        checkIfRoleInputFieldsFilled,
        checkIfTaskAssigneeSelected,
        checkIfUserMaySubmit,
        clearModal,
        edit,
        gAdmin,
        geogArea,
        greyButtonEnabled,
        groupAdmins,
        groupDescription,
        groupId,
        groupMemberRoles,
        groupMembers,
        groupMembersEdit,
        groupName,
        groupOwner,
        groupRequester,
        groupRole,
        groupsNotInNetwork,
        hydrateInviteModal,
        hydrateNetworkInviteModal,
        inviteData,
        membershipId,
        mode,
        networkDescription,
        networkId,
        networkGroups,
        networkName,
        networkOwner,
        nowInside, 
        nowOutside,
        onActivateEditGroupModal,
        onActivateEditNetworkModal,
        onActivateEditProjectModal,
        onActivateEditTaskModal,
        onActivateEditTeamModal,
        onActivateEditVoteModal,
        onActivatePendingVoteModal,
        onActivateShowCompletedProjectModal,
        onActivateTaskModal, 
        onActivateTeamModal,
        onActivateTransferGroupOwnership,
        onActivateTransferNetworkOwnership,
        onClickOutside,
        projectCompleted,
        projectCompletedAt,
        projectDescription,
        projectEndDate,
        projectGroupData,
        projectGroupId,
        projectGroupName,
        projectId,
        projectInputEndDate,
        projectName,
        projectNotes,
        projectStartDate,
        projectTasks,
        projectTeamData,
        projectTeamId,
        projectTeamName,
        selectedAssoc,
        selectedGroupAssociates,
        selectedTeamAssociates,
        selectedTeamMembers,
        showBackdrop,
        showCompletedProjectModal,
        showEditAdminTaskModal,
        showEditNetworkModal,
        showEditProjectModal,
        showEditTaskModal,
        showEditTeamModal,
        showEditVoteModal,
        showGroup,
        showGroupModal,
        showInviteModal,
        showNetworkInviteModal,
        showNetworkModal,
        showPendingVoteModal,
        showProjectModal,
        showTaskModal,
        showTeamModal,
        showTransferGroupModal,
        showTransferNetworkModal,
        showUserTaskModal,
        showVoteModal,
        showVoteResultsModal,
        tAdmin,
        taskableId,
        taskAssignee,
        taskableType,
        taskCompleted,
        taskDescription,
        taskEndDate,
        taskGroupData,
        taskGroupName,
        taskId,
        taskInputEndDate,
        taskName,
        taskNotes,
        taskOwner,
        taskProjectId,
        taskProjectName,
        taskRecipientType,
        taskStartDate,
        taskTeamData,
        taskTeamName,
        taskMembers,
        teamAdmins,
        teamFunction,
        teamId,
        teamMemberRoles,
        teamMembers,
        teamMembersEdit,
        teamName,
        teamOwner,
        teamRequester,
        teamRole,
        type,
        updated_by,
        userId,
        userMaySubmitForm,
        voteableId,
        voteableType,
        voteClosingDate,
        voteGroupName,
        voteInputClosingDate,
        voteElements,
        voteId,
        voteOwner,
        voteResults,
        voteTeamName,
        voteTitle,
    }
}

export default manageModals