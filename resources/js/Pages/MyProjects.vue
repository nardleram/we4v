<template>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">

                <!-- Modal forms -->
                <teleport to="#projectModals">
                    <Modal :show="showProjectModal">
                        <div @mouseleave="nowOutside(); mode = 'project'" @mouseenter="nowInside(); mode = 'over'" v-if="showProjectModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showProjectModal = false, showBackdrop = false">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Create a new project</h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="projectName">new project name</label>
                                        <input v-model="projectName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="projectName" placeholder="E.g.: My Dastardly Plan">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="groupName">describe project</label>
                                        <input v-model="projectDescription" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="projectDescription" placeholder="E.g.: Subdue all humanity using only my dark genius">
                                    </div>

                                    <div class="flex flex-row justify-between">
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="startDate">start date</label>
                                            <input v-model="startDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" id="startDate" placeholder="select start date">
                                        </div>
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="endDate">end date</label>
                                            <input v-model="endDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" id="endDate" placeholder="select end date">
                                        </div>
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Select group</h4>
                                    <div v-if="mygroups" class="flex flex-wrap max-w-full justify-between">
                                        <div v-for="(group, groupKey) in $page.props.mygroups" :key="groupKey" class="min-w-1/3">
                                            <input @click="addGroup" :value="group.group_id" name="group" class="group rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}</label>
                                        </div>
                                    </div>
                                    
                                    <button-grey @click="submitProjectData()">Save project</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <teleport to="#projectModals">
                    <Modal :show="showTaskModal" :name="projectName" :id="projectId" :description="projectDescription" :projectGroupName="projectGroupName">
                        <div @mouseleave="nowOutside(); mode = 'task'" @mouseenter="nowInside(); mode = 'over'" v-if="showTaskModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                            <Form>
                                <template #form>
                                    <div class="flex justify-end">
                                        <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                            <div @click="showTaskModal = false, showBackdrop = false">
                                                <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                            </div>   
                                        </div>
                                    </div>
                                    <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Create task for <span class="italic text-we4vGrey-600">{{ projectName }}</span></h4>

                                    <div>
                                        <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="taskName">task name</label>
                                        <input v-model="taskName" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" id="taskName" placeholder="E.g.: Design casing for anti-grav drive">
                                    </div>

                                    <div>
                                        <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="taskDescription">describe the task</label>
                                        <input v-model="taskDescription" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight mt-4 font-medium" type="text" id="taskDescription" placeholder="E.g.: Technical spec for anti-grav-drive casing (model no. ISV-2022)">
                                    </div>

                                    <div class="flex flex-row justify-between">
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="startDate">start date</label>
                                            <input v-model="startDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" id="startDate" placeholder="select start date">
                                        </div>
                                        <div class="w-justUnderHalf">
                                            <label class="absolute pl-4 pt-6 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="endDate">end date</label>
                                            <input v-model="endDate" class="w-full mt-4 pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="date" id="endDate" placeholder="select end date">
                                        </div>
                                    </div>

                                    <h4 class="text-we4vBlue font-semibold text-sm mt-4">Assign task to group member / whole team / team member</h4>

                                    <div v-if="projectGroupData[0].groupMembers" class="w-full m-0">
                                        <h4 class="text-sm font-regular text-we4vGrey-700 mt-2">Group members</h4>
                                        <div v-for="(groupMember, groupMemberKey) in projectGroupData[0].groupMembers" :key="groupMemberKey">
                                            <div v-if="groupMember.confirmed" class="text-xs mt-1">
                                                <label class="text-we4vGrey-500 text-xs mr-2 w-full text-center" for="groupMember">{{ groupMember.username }}</label>
                                                <input type="radio" :value="projectGroupData[0].group_id">
                                            </div>
                                            <div v-if="!groupMember.confirmed && projectGroupData[0].groupMembers.length === 1" class="text-xs mt-1">
                                                <p class="text-we4vGrey-600 text-xs mt-1">There are no confirmed members in {{ projectGroupData[0].group_name }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="projectGroupData[0].teams" class="w-full m-0">
                                        <h4 class="text-sm font-regular text-we4vGrey-700 mt-2">Team</h4>
                                        <div v-for="(team, teamKey) in projectGroupData[0].teams" :key="teamKey">
                                            <div class="text-xs mt-1">
                                                <label class="text-we4vGrey-500 text-xs mr-2 w-full text-center" for="groupMember">{{ team.team_name }}</label>
                                                <input type="radio" :value="team.team_id">
                                            </div>
                                            <div v-if="team.teamMembers">
                                                <div v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey">
                                                    <div v-if="teamMember.confirmed" class="text-xs mt-1">
                                                        <h4 class="text-sm font-regular text-we4vGrey-700 mt-2">Team members</h4>
                                                        <label class="text-we4vGrey-500 text-xs mr-2 w-full text-center" for="teamMember">{{ teamMember.username }}</label>
                                                        <input type="radio" :value="team.team_id">
                                                    </div>
                                                    <div v-if="!teamMember.confirmed && team.teamMembers.length === 1" class="text-xs mt-1">
                                                        <p class="text-we4vGrey-600 text-xs mt-1">There are no confirmed members in {{ team.team_name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button-grey @click="submitTaskData()">Assign task</button-grey>
                                </template>
                            </Form>
                        </div>
                    </Modal>
                </teleport>

                <Title>
                    <template #title>
                        Project and task management
                    </template>
                    <template #description>
                        Projects are assigned to one of your groups. Tasks are created within your projects and assigned to teams or members.
                    </template>
                </Title>

                <jet-button-blue @click="showProjectModal = true; showBackdrop = true">Create a new project</jet-button-blue>

                <!-- Projects -->
                <Subtitle>
                    <template #title>
                        My projects
                    </template>
                    <template #description>
                        Click a project name to manage your project, for example to assign tasks.
                    </template>
                </Subtitle>
                <div v-if="myprojects.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Project v-for="(project, projectKey) in myprojects" :key="projectKey" :project="project" @activate-task-modal="onActivateTaskModal" />
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
import JetButtonBlue from '@/Jetstream/ButtonBlue'
import Modal from './Components/Modal'
import ModalBackdrop from './Components/ModalBackdrop'
import Project from './Components/Project'
import manageModals from '../Pages/Composables/manageModals'
import { watch, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'MyProjects',
    
    components: {
        AppLayout,
        Title,
        Subtitle,
        Form,
        ButtonGrey,
        JetButtonBlue,
        Modal,
        ModalBackdrop,
        Project,
    },

    props: [
        'myprojects',
        'mygroups',
        'errors'
    ],

    setup() {
        const {
            amOutside, 
            amInside,
            clearInviteModals,
            mode,
            nowInside, 
            nowOutside,
            onActivateTaskModal,
            onClickOutside,
            projectDescription,
            projectGroupData,
            projectId,
            projectEndDate,
            projectName,
            projectStartDate,
            showBackdrop,
            showProjectModal,
            showTaskModal,
            taskDescription,
            taskEndDate,
            taskId,
            taskName,
            taskStartDate
        } = manageModals()

        const submitProjectData = async function () {
            let selectedGroup = document.querySelector('input[name="group"]:checked').value
            let payload = {
                'owner': usePage.props.authUser.id,
                'name': projectName.value,
                'description': projectDescription.value,
                'start_date': projectStartDate.value,
                'end_date': projectEndDate.value,
                'group_id': 'some id',
            }
            await Inertia.post('/myprojects/store', payload)
            
            onClickOutside()
        }

        watch(amOutside, () => {
            if (amOutside.value && !amInside.value) {
                document.body.addEventListener('click', onClickOutside, true)
            }
        })

        return {
            amOutside,
            amInside,
            clearInviteModals,
            mode,
            nowInside, 
            nowOutside,
            onActivateTaskModal,
            onClickOutside,
            projectDescription,
            projectEndDate,
            projectGroupData,
            projectId,
            projectName,
            projectStartDate,
            showBackdrop,
            showProjectModal,
            showTaskModal,
            submitProjectData,
            taskDescription,
            taskEndDate,
            taskId,
            taskName,
            taskStartDate
        }
    }
}
</script>
