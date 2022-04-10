<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight">

                <!-- Modal forms -->
                <teleport to="#projectModals">
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
                                    <Input :name="projectName" :modelValue="projectName" :id="'projectName'" :label="'project name'" :placeholder="'E.g.: My Dastardly Plan'" :type="'text'" required @update-model-value="projectName = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                </div>

                                <div>
                                    <Input :name="projectDescription" :modelValue="projectDescription" :id="'projectDescription'" :label="'describe project (required)'" :placeholder="'E.g.: Subdue humanity using only my dark genius'" :type="'text'" required @update-model-value="projectDescription = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                </div>

                                <div class="flex flex-row justify-between">
                                    <div class="w-justUnderHalf">
                                        <Input :name="projectStartDate" :modelValue="projectStartDate" :id="'projectStartDate'" :label="'start date (required)'" :placeholder="'select start date'" :type="'date'" required @update-model-value="projectStartDate = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                    </div>
                                    <div class="w-justUnderHalf">
                                        <Input :name="projectEndDate" :modelValue="projectEndDate" :id="'projectEndDate'" :label="'end date (required)'" :placeholder="'select end date'" :type="'date'" required @update-model-value="projectEndDate = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                    </div>
                                </div>

                                <h4 class="text-we4vBlue font-semibold text-sm mt-4">Assign project to a group or (administrated) team (required)</h4>
                                <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Groups</h5>
                                <div v-if="myGroups" class="flex flex-wrap max-w-full justify-between mb-2">
                                    <div v-for="(group, groupKey) in $page.props.myGroups" :key="groupKey" class="min-w-1/3">
                                        <input @click="checkIfProjectGroupSelected()" :value="group.group_id" name="groupOrTeam" id="group" type="radio">
                                        <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}<span v-if="group.am_admin" class="text-we4vOrange font-semibold">*</span></label>
                                    </div>
                                </div>
                                <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Teams</h5>
                                <div v-if="myAdminTeams" class="flex flex-wrap max-w-full justify-between">
                                    <div v-for="(team, teamKey) in $page.props.myAdminTeams" :key="teamKey" class="min-w-1/3">
                                        <input @click="checkIfProjectGroupSelected()" :value="team.team_id" name="groupOrTeam" id="team" type="radio">
                                        <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ team.team_id }}">{{ team.team_name }}</label>
                                    </div>
                                </div>
                                
                                <button-grey @click="greyButtonEnabled ? submitProjectData() : null" id="submitForm" :enabled="greyButtonEnabled">Save project</button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#projectModals">
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

                                    <h4 class="uppercase text-we4vBlue font-semibold -mt-8">Edit <span class="italic text-we4vGrey-600">{{ projectName }}</span></h4>
                                    <p class="text-red-600 text-xs font-medium -mt-1 mb-4">(A project may only be reassigned to a different group or team if that project has no tasks. A task may only be deleted if that task has no notes, i.e., is not active.)</p>

                                    <div>
                                        <Input :name="projectName" :modelValue="projectName" :id="'projectName'" :label="'project name'" :placeholder="'E.g.: My Dastardly Plan'" :type="'text'" required @update-model-value="projectName = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                    </div>

                                    <div>
                                        <Input :name="projectDescription" :modelValue="projectDescription" :id="'projectDescription'" :label="'describe project (required)'" :placeholder="'E.g.: Subdue humanity using only my dark genius'" :type="'text'" required @update-model-value="projectDescription = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                    </div>

                                    <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Extend deadline</h5>
                                    <div class="w-justUnderHalf mb-4">
                                        <Input :name="projectEndDate" :modelValue="projectInputEndDate" :id="'projectEndDate'" :label="'end date (required)'" :placeholder="'select end date'" :type="'date'" required @update-model-value="projectInputEndDate = $event" @check-if-user-may-submit="checkIfUserMaySubmit('project')"/>
                                    </div>

                                    <div v-if="projectTasks.length === 0">
                                        <h4 class="text-we4vBlue font-semibold text-sm mt-4">Reassign project</h4>
                                        <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Groups</h5>
                                        <div v-if="myGroups" class="flex flex-wrap max-w-full justify-between mb-2">
                                            <div v-for="(group, groupKey) in $page.props.myGroups" :key="groupKey" class="min-w-1/3">
                                                <input @click="checkIfProjectGroupSelected()" :value="group.group_id" name="groupOrTeam" id="group" type="radio" :checked="projectGroupId === group.group_id">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}<span v-if="group.am_admin" class="text-we4vOrange font-semibold">*</span></label>
                                            </div>
                                        </div>

                                        <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Teams</h5>
                                        <div v-if="myAdminTeams" class="flex flex-wrap max-w-full justify-between">
                                            <div v-for="(team, teamKey) in $page.props.myAdminTeams" :key="teamKey" class="min-w-1/3">
                                                <input @click="checkIfProjectGroupSelected()" :value="team.team_id" name="groupOrTeam" id="team" type="radio" :checked="projectTeamId === team.team_id">
                                                <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ team.team_id }}">{{ team.team_name }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <Notes :taskNotes="taskNotes" :projectNotes="projectNotes" />
                                    </div>

                                    <h5 class="text-we4vBlue font-semibold text-sm mt-4">Log a note</h5>
                                    <textarea v-model="projectNoteBody" name="projectNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs outline-none border border-we4vGrey-200 focus:shadow-md"></textarea>

                                    <input @click="projectCompleted = !projectCompleted" :value="projectCompleted" class="rounded-sm border-indigo-100 shadow-sm focus:outline-none" type="checkbox" :checked="projectCompleted">
                                    <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ projectId }}">Project completed</label>
                                    
                                    <button-grey @click="greyButtonEnabled ? submitProjectData() : null" id="submitForm" :enabled="greyButtonEnabled">Update project</button-grey>
                                </template>
                            </Form>
                        </div>
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
                                        <Input :name="taskName" :modelValue="taskName" :id="'taskName'" :label="'task name'" :placeholder="'E.g.: My Splendid Task'" :type="'text'" required @update-model-value="taskName = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                    </div>

                                    <div>
                                        <Input :name="taskDescription" :modelValue="taskDescription" :id="'taskDescription'" :label="'task description'" :placeholder="'E.g.: Technical spec for anti-grav-drive casing (model no. ISV-2022)'" :type="'text'" required @update-model-value="taskDescription = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                    </div>

                                    <div class="flex flex-row justify-between">
                                        <div class="w-justUnderHalf">
                                            <Input :name="taskStartDate" :modelValue="taskStartDate" :id="'taskStartDate'" :label="'task start date'" :placeholder="'select start date'" :type="'date'" required @update-model-value="taskStartDate = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                        </div>
                                        <div class="w-justUnderHalf">
                                            <Input :name="taskEndDate" :modelValue="taskEndDate" :id="'taskEndDate'" :label="'task end date'" :placeholder="'select end date'" :type="'date'" required @update-model-value="taskEndDate = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                        </div>
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Assign task to group member(s) / whole team / team member(s)</h4>

                                    <group-team-assignment :groupData="projectGroupData" :teamData="projectTeamData" :taskableId="null" :taskableType="null" :taskMembers="[]" @send-task-data="onSendTaskData"/>

                                    <button-grey @click="greyButtonEnabled ? submitTaskData() : null" :enabled="greyButtonEnabled" id="submitForm">Assign task</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#projectModals">
                    <div v-if="showEditTaskModal" @mouseleave="nowOutside(); mode = 'task'" @mouseenter="nowInside(); mode = 'task'" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-28 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="showEditTaskModal = false; clearModal()">
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>   
                                    </div>
                                </div>

                                <h4 class="uppercase text-we4vBlue font-semibold -mt-8">Edit <span class="italic text-we4vGrey-600">{{ taskName }}</span></h4>

                                <div>
                                    <Input :name="taskName" :modelValue="taskName" :id="'taskName'" :label="'task name'" :placeholder="'E.g.: Design anti-grav casing'" :type="'text'" required @update-model-value="taskName = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                </div>

                                <div>
                                    <Input :name="taskDescription" :modelValue="taskDescription" :id="'taskDescription'" :label="'task description'" :placeholder="'E.g.: Technical spec for anti-grav-drive casing (model no. ISV-2022)'" :type="'text'" required @update-model-value="taskDescription = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                </div>

                                <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Extend deadline</h5>
                                <div class="w-justUnderHalf mb-4">
                                    <Input :name="taskEndDate" :modelValue="taskInputEndDate" :id="'taskEndDate'" :label="'end date (required)'" :placeholder="'select end date'" :type="'date'" required @update-model-value="taskInputEndDate = $event" @check-if-user-may-submit="checkIfUserMaySubmit('task')"/>
                                </div>

                                <div>
                                    <Notes :taskNotes="taskNotes" :projectNotes="projectNotes" />
                                </div>

                                <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a note</h5>
                                <textarea v-model="taskNoteBody" name="taskNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none"></textarea>

                                <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 mt-2 tracking-tight">Change task-assignment details</h5>

                                <group-team-assignment :groupData="taskGroupData" :teamData="taskTeamData" :taskableId="taskableId" :taskableType="taskableType" :taskMembers="taskMembers" :taskAssingee="taskAssignee" @send-task-data="onSendTaskData"/>

                                <div class="mt-3">
                                    <input @click="taskCompleted = !taskCompleted" :value="taskCompleted" type="checkbox" :checked="taskCompleted">
                                    <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ taskId }}">Task completed</label>
                                </div>
                                

                                <button-grey @click="greyButtonEnabled ? submitTaskData() : null" :enabled="greyButtonEnabled" id="submitForm">Update task</button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#projectModals">
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

                                <button-grey @click="greyButtonEnabled ? submitTaskData() : null" :enabled="greyButtonEnabled" id="submitForm">Update task</button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#projectModals">
                    <div @mouseleave="nowOutside(); mode = 'project'" @mouseenter="nowInside(); mode = 'project'" v-if="showCompletedProjectModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="showCompletedProjectModal = false; clearModal()">
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>   
                                    </div>
                                </div>

                                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">{{ projectName }} <span class="text-we4vGrey-700 normal-case">– report</span></h4>

                                <p class="text-xs">Assigned to: <span class="font-medium">{{ projectGroupName ? projectGroupName : projectTeamName }}</span></p>
                                <p class="text-xs">Description: <span class="font-medium">{{ projectDescription }}</span></p>

                                <div class="flex flex-row justify-between">
                                    <div class="w-1/3">
                                        <p class="text-xs">Started: <span class="font-medium">{{ projectStartDate }}</span></p>
                                    </div>
                                    <div class="w-1/3">
                                        <p class="text-xs">Planned end: <span class="font-medium">{{ projectEndDate }}</span></p>
                                    </div>
                                    <div class="w-1/3">
                                        <p class="text-xs text-we4vGreen-500">Completed: <span class="font-medium">{{ projectCompletedAt }}</span></p>
                                    </div>
                                </div>

                                <div>
                                    <Notes :taskNotes="[]" :projectNotes="projectNotes" />
                                </div>

                                <h4>Tasks</h4>

                                <div v-for="(task, taskKey) in projectTasks" :key="taskKey">
                                    <h5 class="font-semibold text-sm mb-1">{{ task.task_name }}</h5>
                                    <p class="text-xs">Assigned to: <span class="font-medium">{{ task.task_team_name }}</span></p>
                                    <p class="text-xs">Description: <span class="font-medium">{{ task.task_description }}</span></p>
                                    <div class="flex flex-row justify-start">
                                        <div class="w-1/3">
                                            <p class="text-xs">Started: <span class="font-medium">{{ task.task_start_date }}</span></p>
                                        </div>
                                        <div class="w-1/3">
                                            <p class="text-xs">Planned end: <span class="font-medium">{{ task.task_end_date }}</span></p>
                                        </div>
                                        <div class="w-1/3">
                                            <p class="text-xs text-we4vGreen-500">Completed: <span class="font-medium">{{ task.task_updated_at }}</span></p>
                                        </div>
                                    </div>
                                    <div v-if="task.selected_task_members" class="flex flex-wrap">
                                        <p class="text-xs mr-1">Task members:</p>
                                        <div v-for="(member, memberKey) in task.selected_task_members" :key="memberKey">
                                            <p class="text-xs mr-2 font-medium">{{ member.task_member_username }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <Notes :taskNotes="task.notes" :projectNotes="[]" />
                                    </div>
                                </div>

                                <button-grey @click="reopenProject(projectId)" :enabled="true" >Reopen project</button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <!-- Main page -->
                <Title>
                    <template #title>
                        Project and task management
                    </template>
                    <template #description>
                        Projects are assigned to one of your groups. Tasks are created within your projects and assigned to whole teams or individual members of that group and its teams. A project may be deleted if it has not tasks. A task may be deleted if it has no notes. Notes may not be deleted, and as such effectively activate the project.
                    </template>
                </Title>

                <button-blue v-if="$page.props.myGroups.length > 0" @click="showProjectModal = true; showBackdrop = true; checkIfUserMaySubmit('project')">Create a new project</button-blue>

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
                        <Project v-for="(project, projectKey) in myProjects" :key="projectKey" :project="project"  @activate-task-modal="onActivateTaskModal" @activate-edit-task-modal="onActivateEditTaskModal"
                        @activate-edit-project-modal="onActivateEditProjectModal"/>
                    </div>
                </div>

                <button-grey v-if="myProjects.some(project => project.project_completed)"  @click="showCompletedProjects = !showCompletedProjects" :enabled="true">
                    <span v-if="!showCompletedProjects" >Show completed projects</span>
                    <span v-if="showCompletedProjects" >Hide completed projects</span>
                </button-grey>

                <div v-if="showCompletedProjects" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <CompletedProject v-for="(project, projectKey) in myProjects" :key="projectKey" :project="project" @activate-show-completed-project-modal="onActivateShowCompletedProjectModal"/>
                    </div>
                </div>
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
import Input from './Components/Input'
import InputNoLabel from './Components/InputNoLabel'
import Modal from './Components/Modal'
import ModalBackdrop from './Components/ModalBackdrop'
import Notes from './Components/Notes'
import Project from './Components/Project'
import CompletedProject from './Components/CompletedProject'
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
        Input,
        InputNoLabel,
        Modal,
        ModalBackdrop,
        Notes,
        Project,
        CompletedProject,
        Task,
        ErrorMessage,
        FlashMessage,
        GroupTeamAssignment
    },

    props: [
        'myProjects',
        'myCompletedProjects',
        'myAdminTeams',
        'myGroups',
        'showCompletedProjects',
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
            greyButtonEnabled,
            mode,
            nowInside, 
            nowOutside,
            onActivateEditProjectModal,
            onActivateEditTaskModal,
            onActivateShowCompletedProjectModal,
            onActivateTaskModal,
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
            showBackdrop,
            showCompletedProjectModal,
            showEditAdminTaskModal,
            showEditProjectModal,
            showEditTaskModal,
            showProjectModal,
            showTaskModal,
            taskAssignee,
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
            checkIfTaskAssigneeSelected(true)
            taskableId.value = taskData.taskableId
            taskableType.value = taskData.taskableType
            updatedTaskMembers.value = taskData.taskMemberships
        }

        const getCompletedProjects = async function () {
            try {
                await Inertia.get('/myprojects/completed')
                props.errors = null
            } catch (err) {
                props.errors = err
                error.value = true
            }
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

            if (!selectedGroup && !selectedTeam && projectTasks.value.length === 0) {
                usePage().props.value.errors = { 'Error': 'Neither group nor team selected! Project could not be updated.' }
                error.value = true
            }
            
            let payload = {
                'owner': usePage().props.value.authUser.id,
                'name': projectName.value,
                'description': projectDescription.value,
                'start_date': projectStartDate.value,
                'end_date': projectEndDate.value,
                'completed': projectCompleted.value,
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
                'project_id': projectId.value,
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

        const reopenProject = async function (id) {
            let note = {
                'body': 'Project reopened',
                'noteable_id': id,
                'noteable_type': 'App\\Models\\Project'
            }

            let payload = {
                'id': id,
                'owner': usePage().props.value.authUser.id,
                'name': projectName.value,
                'description': projectDescription.value,
                'start_date': projectStartDate.value,
                'end_date': projectEndDate.value,
                'completed': projectCompleted.value,
                'group_id': projectGroupId.value,
                'team_id': projectTeamId.value,
                'completed': false,
                'note': note,
                '_token': usePage().props.value.csrf_token
            }

            try {
                await Inertia.patch('/myprojects/update', payload)
                flashMessage.value = true
                props.errors = null
            } catch (err) {
                props.errors = err
            }

            clearModal()
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
            getCompletedProjects,
            greyButtonEnabled,
            mode,
            nowInside, 
            nowOutside,
            onActivateEditTaskModal,
            onActivateEditProjectModal,
            onActivateShowCompletedProjectModal,
            onActivateTaskModal,
            onClickOutside,
            onSendTaskData,
            projectCompleted,
            projectCompletedAt,
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
            projectTeamId,
            projectTeamName,
            reopenProject,
            showBackdrop,
            showCompletedProjectModal,
            showEditAdminTaskModal,
            showEditProjectModal,
            showEditTaskModal,
            showProjectModal,
            showTaskModal,
            submitProjectData,
            submitTaskData,
            taskAssignee,
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
            taskRecipientType,
            taskStartDate,
            taskTeamData,
            taskMembers,
            userId
        }
    }
}
</script>
