import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const amInside = ref(false)
const amOutside = ref(false)
const groupId = ref('')
const groupDescription = ref('')
const groupName = ref('')
const groupRole = ref('')
const groupRequester = ref('')
const geogArea = ref('')
const mode = ref('')
const projectId = ref('')
const projectDescription = ref('')
const projectGroupData = ref([])
const projectName = ref('')
const projectStartDate = ref('')
const projectEndDate = ref('')
const showBackdrop = ref(false)
const showGroupModal = ref(false)
const showInviteModal = ref(false)
const showProjectModal = ref(false)
const showTaskModal = ref(false)
const showTeamModal = ref(false)
const taskDescription = ref('')
const taskName = ref('')
const taskStartDate = ref('')
const taskEndDate = ref('')
const taskableId = ref('')
const taskableType = ref('')
const teamFunction = ref('')
const teamId = ref('')
const teamName = ref('')
const teamRequester = ref('')
const teamRole = ref('')
const type = ref('')

const hydrateInviteModal = (req) => {
    clearModal()

    amOutside.value = true
    type.value = req.type

    if (req.type === 'group') {
        groupName.value = req.groupName
        groupId.value = req.groupId
        groupDescription.value = req.groupDesc
        geogArea.value = req.geogArea
        groupRole.value = req.gRole
        groupRequester.value = req.groupRequester
    }

    if (req.type === 'team') {
        teamName.value = req.teamName
        teamId.value = req.teamId
        teamFunction.value = req.teamFunc
        teamRole.value = req.tRole
        teamRequester.value = req.teamRequester
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

const onActivateTeamModal = (group) => {
    groupId.value = group.group_id
    groupName.value = group.group_name
    groupDescription.value = group.group_description
    showBackdrop.value = true
    showTeamModal.value = true
}

const onActivateTaskModal = (project) => {
    usePage().props.value.mygroups.forEach(mygroup => {
        if (mygroup.group_id === project.project_group_id) {
            projectGroupData.value.push(mygroup)
        }
    })
    projectId.value = project.project_id
    projectName.value = project.project_name
    projectDescription.value = project.project_description
    showBackdrop.value = true
    showTaskModal.value = true
}

const clearModal = () => {
    geogArea.value = ''
    groupDescription.value = ''
    groupId.value = ''
    groupName.value = ''
    groupRequester.value = ''
    groupRole.value = ''
    teamFunction.value = ''
    teamName.value = ''
    teamId.value = ''
    teamRequester.value = ''
    teamRole.value = ''
    amInside.value = false
    amOutside.value = false
    showGroupModal.value = false
    showTeamModal.value = false
    showInviteModal.value = false
    showBackdrop.value = false
}

const onClickOutside = () => {
    if (amOutside.value && !amInside.value) {
        if (mode.value === 'group') {
            showGroupModal.value = false
            geogArea.value = ''
            groupDescription.value = ''
            groupId.value = ''
            groupName.value = ''
            groupRequester.value = ''
            groupRole.value = ''
            type.value = ''
        }
        if (mode.value === 'team') {
            showTeamModal.value = false
            teamName.value = ''
            teamFunction.value = ''
            teamRequester.value = ''
            teamRole.value = ''
            type.value = ''
        }
        if (mode.value === 'proj') {
            showProjectModal.value = false
            projectName.value = ''
            projectId.value = ''
            projectDescription.value = ''
            projectStartDate.value = ''
            projectEndDate.value = ''
        }
        if (mode.value === 'task') {
            showTaskModal.value = false
            taskName.value = ''
            taskDescription.value = ''
            taskStartDate.value = ''
            taskEndDate.value = ''
            taskableId.value = ''
            taskableType.value = ''
            projectId.value = ''
        }
        if (showInviteModal.value) {
            showInviteModal.value = false
        }
        showBackdrop.value = false
        amOutside.value = false
        amInside.value = false
        document.body.removeEventListener('click', onClickOutside, true)
    }
}

const manageModals = () => {
    return {
        amOutside, 
        amInside,
        clearModal,
        geogArea,
        groupDescription,
        groupId,
        groupName,
        groupRequester,
        groupRole,
        mode,
        projectId,
        projectName,
        projectDescription,
        projectGroupData,
        projectStartDate,
        projectEndDate,
        showBackdrop,
        showGroupModal,
        showTeamModal,
        showProjectModal,
        showTaskModal,
        showInviteModal,
        taskName,
        taskDescription,
        taskStartDate,
        taskEndDate,
        taskableId,
        taskableType,
        teamFunction,
        teamId,
        teamName,
        teamRequester,
        teamRole,
        type,
        nowInside, 
        nowOutside,
        onActivateTeamModal,
        onActivateTaskModal, 
        onClickOutside,
        hydrateInviteModal
    }
}

export default manageModals