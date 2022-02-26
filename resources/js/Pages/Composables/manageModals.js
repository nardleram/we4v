import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const amInside = ref(false)
const amOutside = ref(false)
const edit = ref(false)
const geogArea = ref(null)
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
const inviteData = ref([])
const mode = ref(null)
const projectCompleted = ref(false)
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
const projectTeamName = ref(null)
const projectStartDate = ref(null)
const projectEndDate = ref(null)
const projectInputEndDate = ref(null)
const roleFieldsAccountedFor = ref(false)
const selectedAssoc = ref(null)
const selectedGroupAssociates = ref([])
const selectedTeamAssociates = ref([])
const selectedTeamMembers = ref([])
const showBackdrop = ref(false)
const showGroupModal = ref(false)
const showInviteModal = ref(false)
const showEditAdminTaskModal = ref(false)
const showEditProjectModal = ref(false)
const showEditTaskModal = ref(false)
const showEditTeamModal = ref(false)
const showEditVoteModal = ref(false)
const showNetworkModal = ref(false)
const showPendingVoteModal = ref(false)
const showProjectModal = ref(false)
const showTaskModal = ref(false)
const showTeamModal = ref(false)
const showUserTaskModal = ref(false)
const showVoteModal = ref(false)
const tAdmin = ref(false)
const taskableId = ref(null)
const taskableType = ref(null)
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
const voteClosingDate = ref(null)
const voteInputClosingDate = ref(null)
const voteElements = ref([])
const voteId = ref(null)
const voteOwner = ref(null)
const voteTitle = ref(null)

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
    showUserTaskModal.value = true
    showBackdrop.value = true
}

const checkIfProjectGroupSelected = () => {
    if (document.querySelector('input[name="groupOrTeam"]:checked').value) {
        projectGroupSelected.value = true
    }

    checkIfUserMaySubmit('project')
}

const checkIfTaskAssigneeSelected = () => {
    let myVals = document.querySelectorAll('input.selectedMembers')
    
    myVals.forEach(myVal => {
        if (myVal.checked) {
            taskAssigneeSelected.value = true
        }
    })

    checkIfUserMaySubmit('task')
}

const checkIfRoleInputFieldsFilled = () => {
    let myVals
    let emptyField = false
    let numFieldsFilled = 0
    edit.value
    ? myVals = document.querySelectorAll('input.invitedAssocsEdit')
    : myVals = document.querySelectorAll('input.invitedAssocs')

    for (const myVal of myVals) {
        if (myVal.checked) {
            let elem = document.getElementById(myVal.value)
            if (!elem.value) {
                emptyField = true
            } else {
                elem.style.border = 'solid 1px rgb(110, 108, 108)'
                numFieldsFilled++
            }
        }
    }

    emptyField
    ? roleFieldsAccountedFor.value = false
    : roleFieldsAccountedFor.value = true

    if (mode.value === 'team') {
        numFieldsFilled > 0 ? roleFieldsAccountedFor.value = true : roleFieldsAccountedFor.value = false
    }

    checkIfUserMaySubmit(mode.value)
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
        if (mode !== 'task' && edit.value) {
            document.getElementById(elIdName).value
            ? name = true
            : name = false
            
            document.getElementById(elIdDesc).value
            ? description = true
            : description = false
        } else if (mode === 'task' && edit.value) {
            name = true
            description = true
        } else {
            document.getElementById(elIdName).value
            ? name = true
            : name = false
            
            document.getElementById(elIdDesc).value
            ? description = true
            : description = false
        }

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
        }

        if (mode === 'project' || mode === 'task') {
            name && description && start && end
            ? requiredFormFields = true
            : requiredFormFields = false
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
        
        if ((requiredFormFields && roleFieldsAccountedFor.value) || 
            (requiredFormFields && projectGroupSelected.value) ||
            (requiredFormFields && taskAssigneeSelected.value)) {
            document.getElementById("submitForm").disabled = false
            document.getElementById("submitForm").classList.remove('text-we4vGrey-200')
            document.getElementById("submitForm").classList.add('text-we4vGrey-600')
            document.getElementById("submitForm").classList.add('hover:bg-we4vGrey-100')
            document.getElementById("submitForm").classList.add('cursor-pointer')
        } else {
            document.getElementById("submitForm").disabled = true
            document.getElementById("submitForm").classList.remove('text-we4vGrey-600')
            document.getElementById("submitForm").classList.remove('hover:bg-we4vGrey-100')
            document.getElementById("submitForm").classList.add('text-we4vGrey-200')
            document.getElementById("submitForm").classList.add('cursor-default')
        }
    }, 50)
}

const clearModal = () => {
    amInside.value = false
    amOutside.value = false
    edit.value = false
    gAdmin.value = false
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
    projectCompleted.value = false
    projectDescription.value = null
    projectEndDate.value = null
    projectGroupData.value = []
    projectGroupId.value = null
    projectId.value = null
    projectInputEndDate.value = null
    projectName.value = null
    projectStartDate.value = null
    projectTeamData.value = []
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
    showEditAdminTaskModal.value = false
    showEditProjectModal.value = false
    showEditTaskModal.value = false
    showEditTeamModal.value = false
    showEditVoteModal.value = false
    showGroupModal.value = false
    showProjectModal.value = false
    showTaskModal.value = false
    showTeamModal.value = false
    showUserTaskModal.value = false
    showVoteModal.value = false
    showPendingVoteModal.value = false
    showInviteModal.value = false
    showBackdrop.value = false
    teamMembersEdit.value = []
    userId.value = null
    updated_by.value = null
    voteClosingDate.value = null
    voteElements.value = []
    voteId.value = null
    voteOwner.value = null
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
    }

    showBackdrop.value = true
    showInviteModal.value = true
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
    groupMembersEdit.value = [] // Very sticky clingy critter; force emptying here
    groupId.value = group.group_id
    groupName.value = group.group_name
    groupDescription.value = group.group_description
    geogArea.value = group.geog_area
    groupMembers.value = group.groupMembers ? group.groupMembers : []
    edit.value = true
    showBackdrop.value = true
    showGroupModal.value = true
}

const onActivateEditProjectModal = (project) => {
    projectCompleted.value = project.project_completed
    projectDescription.value = project.project_description
    projectId.value = project.project_id
    projectGroupId.value = project.project_group_id
    projectGroupName.value = project.project_group_name
    projectTeamName.value = project.project_team_name
    projectName.value = project.project_name
    projectNotes.value = project.notes ? project.notes : []
    projectTasks.value = project.tasks ? project.tasks : []
    projectEndDate.value = project.project_end_date
    projectInputEndDate.value = project.project_input_end_date
    projectStartDate.value = project.project_start_date
    showBackdrop.value = true
    showEditProjectModal.value = true
    edit.value = true
}

const onActivateEditAdminTaskModal = (task) => {
    taskCompleted.value = task.task_completed
    taskId.value = task.task_id
    taskProjectId.value = task.project_id
    taskName.value = task.task_name
    projectNotes.value = task.project_notes ? task.project_notes : null
    taskNotes.value = task.task_notes ? task.task_notes : null
    taskDescription.value = task.task_description
    taskRecipientType.value = 'team'
    taskEndDate.value = task.task_end_date
    taskStartDate.value = task.task_start_date
    taskInputEndDate.value = task.task_input_end_date
    taskableId.value = task.team_id
    taskableType.value = 'App\\Models\\Team'
    taskMembers.value = task.selected_team_members
    edit.value = true
    showBackdrop.value = true
    showEditAdminTaskModal.value = true
    mode.value = 'task'
}

const onActivateEditTaskModal = (task) => {
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
    taskProjectId.value = task.project_id
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
            })
        }) 
    } else if (task.selected_task_members) {
        task.selected_task_members.forEach(member => {
            taskMembers.value.push({ 
                username: member.task_member_username, 
                created_at: member.task_member_created_at,
                user_id: member.task_member_user_id,
            })
        }) 
    }
    edit.value = true
    showBackdrop.value = true
    showEditTaskModal.value = true
    mode.value = 'task'
}

const onActivateEditTeamModal = (team) => {
    clearModal()

    groupId.value = team.group_id
    teamId.value = team.team_id
    teamName.value = team.team_name
    teamFunction.value = team.team_function
    teamOwner.value = team.team_owner
    teamMembers.value = team.teamMembers
    edit.value = true
    showBackdrop.value = true
    showEditTeamModal.value = true
}

const onActivateEditVoteModal = (vote) => {
    voteTitle.value = vote.vote_title
    voteId.value = vote.vote_id
    voteInputClosingDate.value = vote.input_closing_date
    showBackdrop.value = true
    showEditVoteModal.value = true
    edit.value = true
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

const onClickOutside = () => {
    if (amOutside.value && !amInside.value) {
        clearModal()

        document.body.removeEventListener('click', onClickOutside, true)
    }
}

const manageModals = () => {
    return {
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
        groupAdmins,
        geogArea,
        groupDescription,
        groupId,
        groupMemberRoles,
        groupMembers,
        groupMembersEdit,
        groupName,
        groupOwner,
        groupRequester,
        groupRole,
        hydrateInviteModal,
        inviteData,
        mode,
        nowInside, 
        nowOutside,
        onActivateEditAdminTaskModal,
        onActivateEditGroupModal,
        onActivateEditProjectModal,
        onActivateEditTaskModal,
        onActivateEditTeamModal,
        onActivateEditVoteModal,
        onActivatePendingVoteModal,
        onActivateTaskModal, 
        onActivateTeamModal,
        onClickOutside,
        projectCompleted,
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
        projectTeamName,
        selectedAssoc,
        selectedGroupAssociates,
        selectedTeamAssociates,
        selectedTeamMembers,
        showBackdrop,
        showEditAdminTaskModal,
        showEditProjectModal,
        showEditTaskModal,
        showEditTeamModal,
        showEditVoteModal,
        showGroupModal,
        showInviteModal,
        showNetworkModal,
        showPendingVoteModal,
        showProjectModal,
        showTaskModal,
        showTeamModal,
        showUserTaskModal,
        showVoteModal,
        tAdmin,
        taskableId,
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
        voteClosingDate,
        voteInputClosingDate,
        voteElements,
        voteId,
        voteOwner,
        voteTitle,
    }
}

export default manageModals