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
const groupRole = ref(null)
const groupRequester = ref(null)
const inviteData = ref([])
const mode = ref(null)
const projectCompleted = ref(false)
const projectDescription = ref(null)
const projectGroupData = ref([])
const projectGroupId = ref(null)
const projectGroupName = ref(null)
const projectId = ref(null)
const projectName = ref(null)
const projectNotes = ref([])
const projectStartDate = ref(null)
const projectEndDate = ref(null)
const projectInputEndDate = ref(null)
const selectedGroupAssociates = ref([])
const selectedTeamAssociates = ref([])
const showBackdrop = ref(false)
const showGroupModal = ref(false)
const showInviteModal = ref(false)
const showProjectModal = ref(false)
const showEditProjectModal = ref(false)
const showEditTaskModal = ref(false)
const showEditTeamModal = ref(false)
const showEditVoteModal = ref(false)
const showPendingVoteModal = ref(false)
const showTaskModal = ref(false)
const showTeamModal = ref(false)
const showUserTaskModal = ref(false)
const showVoteModal = ref(false)
const tAdmin = ref(false)
const taskableId = ref(null)
const taskableType = ref(null)
const taskAssignee = ref(null)
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
const taskTeamName = ref([])
const taskUserId = ref(null)
const teamAdmins = ref([])
const teamFunction = ref(null)
const teamId = ref(null)
const teamMemberRoles = ref([])
const teamMembers = ref([])
const teamMembersEdit = ref([])
const teamName = ref(null)
const teamRequester = ref(null)
const teamRole = ref(null)
const type = ref(null)
const updated = ref(false)
const userId = ref(null)
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
    taskName.value = task.name
    taskDescription.value = task.description
    taskEndDate.value = task.end_date
    taskGroupName.value = task.group_name
    taskInputEndDate.value = task.input_end_date
    taskOwner.value = task.owner
    taskNotes.value = task.notes
    taskTeamName.value = task.team_name
    taskUserId.value = task.task_user_id
    showUserTaskModal.value = true
    showBackdrop.value = true
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
    selectedGroupAssociates.value = []
    selectedTeamAssociates.value = []
    tAdmin.value = false
    taskableId.value = null
    taskableType.value = null
    taskAssignee.value = null
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
    taskRecipientType.value = null
    taskStartDate.value = null
    taskTeamName.value = null
    taskUserId.value = null
    teamAdmins.value = []
    teamFunction.value = null
    teamId.value = null
    teamMemberRoles.value = []
    teamName.value = null
    teamRequester.value = null
    teamRole.value = null
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
        groupName.value = req.groupName
        groupId.value = req.groupId
        groupDescription.value = req.groupDesc
        geogArea.value = req.geogArea
        groupRole.value = req.gRole
        groupRequester.value = req.groupRequester
        groupMembers.value = req.groupMembers
        updated.value = req.updated
    }

    if (req.type === 'team') {
        tAdmin.value = req.tAdmin
        teamName.value = req.teamName
        teamId.value = req.teamId
        teamFunction.value = req.teamFunc
        teamRole.value = req.tRole
        teamRequester.value = req.teamRequester
        updated.value = req.updated
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
    groupId.value = group.group_id
    groupName.value = group.group_name
    groupDescription.value = group.group_description
    geogArea.value = group.geog_area
    groupMembers.value = group.groupMembers
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
    projectName.value = project.project_name
    projectNotes.value =  project.notes
    projectEndDate.value = project.project_end_date
    projectInputEndDate.value = project.project_input_end_date
    projectStartDate.value = project.project_start_date
    showBackdrop.value = true
    showEditProjectModal.value = true
    edit.value = true
}

const onActivateEditTaskModal = (task) => {
    usePage().props.value.mygroups.forEach(mygroup => {
        if (mygroup.group_id === task.project_group_id) {
            taskGroupData.value.push(mygroup)
        }
    })
    taskAssignee.value = task.assignee
    taskCompleted.value = task.task_completed
    taskId.value = task.task_id
    taskProjectId.value = task.task_project_id
    taskName.value = task.task_name
    taskNotes.value = task.notes
    taskDescription.value = task.task_description
    taskRecipientType.value = task.recipient_type
    taskEndDate.value = task.task_end_date
    taskStartDate.value = task.task_start_date
    taskInputEndDate.value = task.task_input_end_date
    taskableId.value = task.taskable_id
    taskableType.value = task.taskable_type
    taskUserId.value = task.task_user_id
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
    usePage().props.value.mygroups.forEach(mygroup => {
        if (mygroup.group_id === project.project_group_id) {
            projectGroupData.value.push(mygroup)
        }
    })
    projectDescription.value = project.project_description
    projectId.value = project.project_id
    projectName.value = project.project_name
    showBackdrop.value = true
    showTaskModal.value = true
}

const onActivateTeamModal = (group) => {
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
        groupRequester,
        groupRole,
        hydrateInviteModal,
        inviteData,
        mode,
        nowInside, 
        nowOutside,
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
        selectedGroupAssociates,
        selectedTeamAssociates,
        showBackdrop,
        showEditProjectModal,
        showEditTaskModal,
        showEditTeamModal,
        showEditVoteModal,
        showGroupModal,
        showInviteModal,
        showPendingVoteModal,
        showProjectModal,
        showTaskModal,
        showTeamModal,
        showUserTaskModal,
        showVoteModal,
        tAdmin,
        taskableId,
        taskableType,
        taskAssignee,
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
        taskRecipientType,
        taskStartDate,
        taskTeamName,
        taskUserId,
        teamAdmins,
        teamFunction,
        teamId,
        teamMemberRoles,
        teamMembers,
        teamMembersEdit,
        teamName,
        teamRequester,
        teamRole,
        type,
        updated,
        userId,
        voteClosingDate,
        voteInputClosingDate,
        voteElements,
        voteId,
        voteOwner,
        voteTitle,
    }
}

export default manageModals