import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

const amInside = ref(false)
const amOutside = ref(false)
const edit = ref(false)
const geogArea = ref('')
const gAdmin = ref(false)
const groupAdmins = ref([])
const groupId = ref('')
const groupDescription = ref('')
const groupMembers = ref([])
const groupMembersEdit = ref([])
const groupName = ref('')
const groupRole = ref('')
const groupRequester = ref('')
const inviteData = ref([])
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
const tAdmin = ref(false)
const taskDescription = ref('')
const taskName = ref('')
const taskStartDate = ref('')
const taskEndDate = ref('')
const taskableId = ref('')
const taskableType = ref('')
const teamAdmins = ref([])
const teamFunction = ref('')
const teamId = ref('')
const teamMembers = ref([])
const teamMembersEdit = ref([])
const teamName = ref('')
const teamRequester = ref('')
const teamRole = ref('')
const type = ref('')
const updated = ref(false)
const userId = ref(null)

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

const clearModal = () => {
    amInside.value = false
    amOutside.value = false
    edit.value = false
    gAdmin.value = false
    geogArea.value = ''
    groupAdmins.value = []
    groupDescription.value = ''
    groupId.value = ''
    groupMembers.value = []
    groupMembersEdit.value = []
    groupName.value = ''
    groupRequester.value = ''
    groupRole.value = ''
    projectDescription.value = ''
    projectGroupData.value = []
    projectId.value = ''
    projectName.value = ''
    tAdmin.value = false
    taskableId.value = null
    taskableType.value = null
    taskName.value = null
    taskDescription.value = null
    taskEndDate.value = null
    taskStartDate.value = null
    userId.value = null
    teamAdmins.value = []
    teamFunction.value = ''
    teamName.value = ''
    teamId.value = ''
    teamRequester.value = ''
    teamRole.value = ''
    showGroupModal.value = false
    showProjectModal.value = false
    showTaskModal.value = false
    showTeamModal.value = false
    showInviteModal.value = false
    showBackdrop.value = false
}

const onClickOutside = () => {
    if (amOutside.value && !amInside.value) {
        clearModal()

        document.body.removeEventListener('click', onClickOutside, true)
    }
}

const manageModals = () => {
    return {
        amOutside, 
        amInside,
        clearModal,
        edit,
        gAdmin,
        groupAdmins,
        geogArea,
        groupDescription,
        groupId,
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
        onActivateTeamModal,
        onActivateTaskModal, 
        onClickOutside,
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
        tAdmin,
        taskName,
        taskDescription,
        taskStartDate,
        taskEndDate,
        taskableId,
        taskableType,
        teamAdmins,
        teamFunction,
        teamId,
        teamMembers,
        teamMembersEdit,
        teamName,
        teamRequester,
        teamRole,
        type,
        updated,
        userId
    }
}

export default manageModals