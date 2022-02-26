<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">

                <!-- Modal forms -->
                <teleport to="#projectModals">
                    <Modal :show="showProjectModal">
                        <div @mouseleave="nowOutside(); mode = 'project'" @mouseenter="nowInside(); mode = 'project'" v-if="showProjectModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showProjectModal = false; clearModal()">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Create a new project</h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="projectName">new project name (required)<span class="text-red-600">*</span></label>
                                        <input @blur="checkIfUserMaySubmit('project')" v-model="projectName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="projectName" placeholder="E.g.: My Dastardly Plan">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">describe project (required)<span class="text-red-600">*</span></label>
                                        <input @blur="checkIfUserMaySubmit('project')" v-model="projectDescription" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="projectDescription" placeholder="E.g.: Subdue all humanity using only my dark genius">
                                    </div>

                                    <div class="flex flex-row justify-between">
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="startDate">start date (required)<span class="text-red-600">*</span></label>
                                            <input @blur="checkIfUserMaySubmit('project')" v-model="projectStartDate" id="projectStartDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" placeholder="select start date">
                                        </div>
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="endDate">end date (required)<span class="text-red-600">*</span></label>
                                            <input @blur="checkIfUserMaySubmit('project')" v-model="projectEndDate" id="projectEndDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" placeholder="select end date">
                                        </div>
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Assign project to a group or (administrated) team (required)<span class="text-red-600">*</span></h4>
                                    <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Groups</h5>
                                    <div v-if="myGroups" class="flex flex-wrap max-w-full justify-between mb-2">
                                        <div v-for="(group, groupKey) in $page.props.myGroups" :key="groupKey" class="min-w-1/3">
                                            <input @click="checkIfProjectGroupSelected()" :value="group.group_id" name="groupOrTeam" id="group" class="rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}<span v-if="group.am_admin" class="text-we4vOrange font-semibold">*</span></label>
                                        </div>
                                    </div>
                                    <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Teams</h5>
                                    <div v-if="myAdminTeams" class="flex flex-wrap max-w-full justify-between">
                                        <div v-for="(team, teamKey) in $page.props.myAdminTeams" :key="teamKey" class="min-w-1/3">
                                            <input @click="checkIfProjectGroupSelected()" :value="team.team_id" name="groupOrTeam" id="team" class="rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ team.team_id }}">{{ team.team_name }}</label>
                                        </div>
                                    </div>
                                    
                                    <button-grey @click="submitProjectData()" id="submitForm">Save project</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#projectModals">
                    <Modal :show="showEditProjectModal">
                        <div @mouseleave="nowOutside(); mode = 'project'" @mouseenter="nowInside(); mode = 'project'" v-if="showEditProjectModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showEditProjectModal = false; clearModal()">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Edit <span class="italic text-we4vGrey-600">{{ projectName }}</span></h4>

                                    <div class="text-we4vGrey-600 text-sm mb-2 tracking-tight">
                                        <p class="mb-2"><span class="font-semibold text-we4vGrey-500">Description: </span>{{ projectDescription }}</p>
                                        <p class="mb-2"><span class="font-semibold text-we4vGrey-500">Assigned to: </span>{{ projectGroupName ? projectGroupName : projectTeamName }}</p>
                                    </div>

                                    <div>
                                        <Notes :taskNotes="taskNotes" :projectNotes="projectNotes" />
                                    </div>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a note</h5>
                                    <textarea v-model="projectNoteBody" name="projectNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none"></textarea>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Extend deadline</h5>
                                    <div class="w-justUnderHalf mb-4">
                                        <input v-model="projectInputEndDate" class="w-full p-3 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date">
                                    </div>

                                    <div v-if="projectTasks.length === 0" class="mb-3">
                                        <h4 class="text-we4vBlue font-semibold text-sm mt-4">Reassign project</h4>
                                        <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Groups</h5>
                                        <div v-if="myGroups" class="flex flex-wrap max-w-full justify-between">
                                            <div v-for="(group, groupKey) in $page.props.myGroups" :key="groupKey" class="min-w-1/3">
                                                <input id="group" :value="group.group_id" name="group" class="group rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio" :checked="projectGroupId === group.group_id">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}</label>
                                            </div>
                                        </div>

                                        <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Teams</h5>
                                        <div v-if="myAdminTeams" class="flex flex-wrap max-w-full justify-between">
                                            <div v-for="(team, teamKey) in $page.props.myAdminTeams" :key="teamKey" class="min-w-1/3">
                                                <input id="team" :value="team.team_id" name="groupOrTeam" class="rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ team.team_id }}">{{ team.team_name }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <input :value="projectCompleted" class="rounded-sm border-indigo-100 shadow-sm focus:outline-none" type="checkbox" :checked="projectCompleted">
                                    <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ projectId }}">Project completed</label>
                                    
                                    <button-grey @click="submitProjectData()">Update project</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#projectModals">
                    <Modal :show="showTaskModal">
                        <div @mouseleave="nowOutside(); mode = 'task'" @mouseenter="nowInside(); mode = 'task'" v-if="showTaskModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showTaskModal = false; clearModal()">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Create task for <span class="italic text-we4vGrey-600">{{ projectName }}</span></h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="taskName">task name (required)<span class="text-red-600">*</span></label>
                                        <input @blur="checkIfUserMaySubmit('task')" v-model="taskName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="taskName" placeholder="E.g.: Design casing for anti-grav drive">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="taskDescription">describe the task (required)<span class="text-red-600">*</span></label>
                                        <input @blur="checkIfUserMaySubmit('task')" v-model="taskDescription" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight mt-4 font-medium" type="text" id="taskDescription" placeholder="E.g.: Technical spec for anti-grav-drive casing (model no. ISV-2022)">
                                    </div>

                                    <div class="flex flex-row justify-between">
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="startDate">start date (required)<span class="text-red-600">*</span></label>
                                            <input @blur="checkIfUserMaySubmit('task')" v-model="taskStartDate" id="taskStartDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" placeholder="select start date">
                                        </div>
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="endDate">end date (required)<span class="text-red-600">*</span></label>
                                            <input @blur="checkIfUserMaySubmit('task')" v-model="taskEndDate" id="taskEndDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" placeholder="select end date">
                                        </div>
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Assign task to group member(s) / whole team / team member(s) (required)<span class="text-red-600">*</span></h4>

                                    <group-team-assignment :groupData="projectGroupData" :teamData="projectTeamData" :taskableId="null" :taskableType="null" :assignees="[]" :selectedTeamMembers="selectedTeamMembers" @send-task-data="onSendTaskData"/>

                                    <button-grey @click="submitTaskData()" id="submitForm">Assign task</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#projectModals">
                    <Modal :show="showEditTaskModal" :name="taskName" :id="taskId" :description="taskDescription">
                        <div @mouseleave="nowOutside(); mode = 'task'" @mouseenter="nowInside(); mode = 'task'" v-if="showEditTaskModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-28 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showEditTaskModal = false; clearModal()">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-2 -mt-8">Edit task <span class="italic text-we4vGrey-600">{{ taskName }}</span></h4>
                                    <div class="text-we4vGrey-600 text-sm mb-2 tracking-tight">
                                        <p class="mb-2"><span class="font-semibold text-we4vGrey-500">Description: </span>{{ taskDescription }}</p>
                                    </div>

                                    <div>
                                        <Notes :taskNotes="taskNotes" :projectNotes="projectNotes" />
                                    </div>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a note</h5>
                                    <textarea v-model="taskNoteBody" name="taskNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none"></textarea>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Change task-assignment settings</h5>

                                    <group-team-assignment :groupData="taskGroupData" :teamData="taskTeamData" :taskableId="taskableId" :taskableType="taskableType" :assignees="taskMembers" @send-task-data="onSendTaskData"/>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Extend deadline</h5>
                                    <div class="w-justUnderHalf mb-4">
                                        <input v-model="taskInputEndDate" id="taskEndDate" class="w-full p-3 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date">
                                    </div>

                                    <input :value="taskCompleted" class="rounded-sm border-indigo-100 shadow-sm focus:outline-none" type="checkbox" :checked="taskCompleted">
                                    <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ taskId }}">Task completed</label>

                                    <button-grey @click="submitTaskData()" id="submitForm">Update task</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#projectModals">
                    <Modal :show="showEditAdminTaskModal" :name="taskName" :id="taskId" :description="taskDescription">
                        <div @mouseleave="nowOutside(); mode = 'task'" @mouseenter="nowInside(); mode = 'task'" v-if="showEditAdminTaskModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-28 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showEditAdminTaskModal = false; clearModal()">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-2 -mt-8">Edit task <span class="italic text-we4vGrey-600">{{ taskName }}</span></h4>
                                    <div class="text-we4vGrey-600 text-sm mb-2 tracking-tight">
                                        <p class="mb-2"><span class="font-semibold text-we4vGrey-500">Description: </span>{{ taskDescription }}</p>
                                        <p class="mb-2"><span class="font-semibold text-we4vGrey-500">Assignee: </span>(team)</p>
                                    </div>

                                    <div v-if="taskNotes">
                                        <Notes :notes="taskNotes" />
                                    </div>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a note</h5>
                                    <textarea v-model="taskNoteBody" name="taskNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none"></textarea>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Extend deadline</h5>
                                    <div class="w-justUnderHalf mb-4">
                                        <input v-model="taskInputEndDate" class="w-full p-3 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date">
                                    </div>

                                    <input @click="taskCompleted = !taskCompleted" :value="taskCompleted" class="rounded-sm border-indigo-100 shadow-sm focus:outline-none" type="checkbox" :checked="taskCompleted">
                                    <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ taskId }}">Task completed</label>

                                    <button-grey @click="submitTaskData()">Update task</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <!-- Main page -->
                <Title>
                    <template #title>
                        Project and task management
                    </template>
                    <template #description>
                        Projects are assigned to one of your groups. Tasks are created within your projects and assigned to whole teams or individual members of that group and its teams.
                    </template>
                </Title>

                <button-blue v-if="$page.props.myGroups.length > 0" @click="showProjectModal = true; showBackdrop = true; checkIfUserMaySubmit('project')">Create a new project</button-blue>

                <button-grey v-else>
                    <a :href="route('mygroups', $page.props.authUser.id)">
                        Create a group before setting up a project
                    </a>
                </button-grey>

                <!-- Main page – Projects -->
                <Subtitle>
                    <template #title>
                        My projects
                    </template>
                    <template #description>
                        Click a project name to assign tasks.
                    </template>
                </Subtitle>
                <div v-if="myProjects.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Project v-for="(project, projectKey) in myProjects" :key="projectKey" :project="project" @activate-task-modal="onActivateTaskModal" @activate-edit-task-modal="onActivateEditTaskModal"
                        @activate-edit-project-modal="onActivateEditProjectModal"/>
                    </div>
                </div>

                <Subtitle>
                    <template #title>
                        Tasks I administrate
                    </template>
                    <template #description>
                        Click a task name or its edit icon to edit a task.
                    </template>
                </Subtitle>
                <!-- <div v-if="myAdminTasks.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Task v-for="(task, taskKey) in myAdminTasks" :key="taskKey" :task="task" @activate-edit-admin-task-modal="onActivateEditAdminTaskModal"/>
                    </div>
                </div> -->
            </div>
        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Title from '@/Jetstream/SectionTitle'
import Subtitle from '@/Jetstream/Subtitle'
import Form from '@/Jetstream/FormSection'
import ButtonGrey from '@/Jetstream/ButtonGrey'
import ButtonBlue from '@/Jetstream/ButtonBlue'
import Modal from './Components/Modal'
import ModalBackdrop from './Components/ModalBackdrop'
import Notes from './Components/Notes'
import Project from './Components/Project'
import Task from './Components/Task'
import manageModals from '../Pages/Composables/manageModals'
import { watch, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'
import GroupTeamAssignment from './Components/GroupTeamAssignment'

export default {
    name: 'MyProjects',
    
    components: {
        AppLayout,
        Title,
        Subtitle,
        Form,
        ButtonGrey,
        ButtonBlue,
        Modal,
        ModalBackdrop,
        Notes,
        Project,
        Task,
        ErrorMessage,
        FlashMessage,
        GroupTeamAssignment
    },

    props: [
        'myProjects',
        'myAdminTasks',
        'myAdminTeams',
        'myGroups',
        'errors'
    ],

    setup(props) {
        const {
            amOutside, 
            amInside,
            checkIfProjectGroupSelected,
            checkIfTaskAssigneeSelected,
            checkIfUserMaySubmit,
            clearModal,
            edit,
            mode,
            nowInside, 
            nowOutside,
            onActivateEditAdminTaskModal,
            onActivateEditProjectModal,
            onActivateEditTaskModal,
            onActivateTaskModal,
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
            showBackdrop,
            showEditAdminTaskModal,
            showEditProjectModal,
            showEditTaskModal,
            showProjectModal,
            showTaskModal,
            taskableId,
            taskableType,
            taskCompleted,
            taskDescription,
            taskEndDate,
            taskGroupData,
            taskId,
            taskInputEndDate,
            taskName,
            taskNotes,
            taskProjectId,
            taskRecipientType,
            taskStartDate,
            taskTeamData,
            taskMembers,
            userId
        } = manageModals()

        const taskNoteBody = ref(null)
        const projectNoteBody = ref(null)
        const updatedTaskMembers = ref([])
        const error = ref(false)
        const flashMessage = ref(false)

        const onSendTaskData = (taskData) => {
            checkIfTaskAssigneeSelected()
            taskableId.value = taskData.taskableId
            taskableType.value = taskData.taskableType
            updatedTaskMembers.value = taskData.taskMemberships
        }

        const submitProjectData = async function () {
            let note = {}
            edit.value
            ? note = {
                'body': projectNoteBody.value,
                'noteable_id': projectId.value,
                'noteable_type': 'App\\Models\\Project'
            }
            : null
            
            let selectedGroup = null
            let selectedTeam = null
            // Below only works in edit mode if input[id="team"]:checked works,
            // when, e.g., project has no task and can therefore be reassigned.
            if (edit.value && projectTasks.value.length === 0) {
                if (document.querySelector('input[id="group"]:checked')) {
                    selectedGroup = document.querySelector('input[id="group"]:checked').value
                }
                if (document.querySelector('input[id="team"]:checked')) {
                    selectedTeam = document.querySelector('input[id="team"]:checked').value
                }
            }
            if (!edit.value) {
                if (document.querySelector('input[id="group"]:checked')) {
                    selectedGroup = document.querySelector('input[id="group"]:checked').value
                }
                if (document.querySelector('input[id="team"]:checked')) {
                    selectedTeam = document.querySelector('input[id="team"]:checked').value
                }
            }

            if (!selectedGroup && !selectedTeam) {
                usePage().props.value.errors = { 'Error': 'Neither group nor team selected! Project not updated.' }
                error.value = true
            }
            
            let payload = {
                'owner': usePage().props.value.authUser.id,
                'name': projectName.value,
                'description': projectDescription.value,
                'start_date': projectStartDate.value,
                'end_date': projectEndDate.value,
                'group_id': selectedGroup ? selectedGroup: null,
                'team_id': selectedTeam ? selectedTeam: null,
                'note': note,
                '_token': usePage().props.value.csrf_token
            }

            if (edit.value) {
                payload.end_date = projectInputEndDate.value
                payload.id = projectId.value
            }

            try {
                if (edit.value && !error.value) {
                    await Inertia.patch('/myprojects/update', payload)
                    flashMessage.value = true
                    props.errors = null
                }
                if (!edit.value && !error.value) {
                    await Inertia.post('/myprojects/store', payload)
                    flashMessage.value = true
                    props.errors = null
                }
            } catch (err) {
                props.errors = err
                error.value = true
            }
            
            clearModal()
            projectNoteBody.value = null
        }

        const submitTaskData = async function () {
            let taskNote = {}
            edit.value
            ? taskNote = {
                'body': taskNoteBody.value,
                'noteable_id': taskId.value,
                'noteable_type': 'App\\Models\\Task'
            }
            : null

            let payload = {
                'owner': usePage().props.value.authUser.id,
                'name': taskName.value,
                'id': taskId.value,
                'completed': taskCompleted.value,
                'description': taskDescription.value,
                'start_date': taskStartDate.value,
                'end_date': taskEndDate.value,
                'taskable_id': taskableId.value,
                'taskable_type': taskableType.value,
                'membershipable_type': 'App\\Models\\Task',
                'membershipable_id': taskId.value,
                'project_id': taskProjectId.value,
                'members': updatedTaskMembers.value,
                'taskNote': taskNote,
                'projectNote': [],
                '_token': usePage().props.value.csrf_token
            }
            
            if (edit.value) {
                payload.end_date = taskInputEndDate.value
            }
            
            try {
                edit.value
                ? await Inertia.patch('/mytasks/update', payload)
                : await Inertia.post('/mytasks/store', payload) 
                flashMessage.value = true
                props.errors = null
            } catch (err) {
                props.errors = err
            }
            
            clearModal()
            taskNoteBody.value = null
        }

        watch(amOutside, () => {
            if (amOutside.value && !amInside.value) {
                document.body.addEventListener('click', onClickOutside, true)
            }
        })

        watch(error, () => {
            setTimeout(() => { 
                usePage().props.value.errors = {} 
                error.value = false 
            }, 2500)
        })

        watch(flashMessage, () => {
            setTimeout(() => {
                usePage().props.value.jetstream.flash.message = ''
            }, 2500 )
        })

        return {
            amOutside,
            amInside,
            checkIfProjectGroupSelected,
            checkIfTaskAssigneeSelected,
            checkIfUserMaySubmit,
            clearModal,
            edit,
            mode,
            nowInside, 
            nowOutside,
            onActivateEditAdminTaskModal,
            onActivateEditTaskModal,
            onActivateEditProjectModal,
            onActivateTaskModal,
            onClickOutside,
            onSendTaskData,
            projectCompleted,
            projectDescription,
            projectGroupId,
            projectEndDate,
            projectGroupData,
            projectGroupName,
            projectId,
            projectInputEndDate,
            projectName,
            projectNoteBody,
            projectNotes,
            projectStartDate,
            projectTasks,
            projectTeamData,
            projectTeamName,
            showBackdrop,
            showEditAdminTaskModal,
            showEditProjectModal,
            showEditTaskModal,
            showProjectModal,
            showTaskModal,
            submitProjectData,
            submitTaskData,
            taskableId,
            taskableType,
            taskCompleted,
            taskDescription,
            taskEndDate,
            taskGroupData,
            taskId,
            taskInputEndDate,
            taskName,
            taskNotes,
            taskNoteBody,
            taskProjectId,
            taskRecipientType,
            taskStartDate,
            taskTeamData,
            taskMembers,
            userId
        }
    }
}
</script>
