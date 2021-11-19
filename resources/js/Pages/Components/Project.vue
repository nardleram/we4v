<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-projectBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight">
        <div @click="$emit('activateTaskModal', project)" class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center cursor-pointer pt-1 max-h-16">
            <p>{{ project.project_name }} <span class="text-we4vGrey-200 font-light text-xs italic">(assigned to <span class="font-normal">{{ project.project_group_name }}</span>)</span></p>
            <p class="text-xs font-light text-we4vGrey-200 italic my-1">{{ project.project_description }}</p>
            <p class="text-we4vGrey-300">Project timeframe: {{ project.project_start_date }} <i class="fa fa-arrow-right"></i> {{ project.project_end_date }} – 
                <span v-if="project.project_deadline_passed" class="text-red-600 font-semibold uppercase">Deadline passed</span>
                <span v-else-if="project.project_days_remaining < 5 && !project.project_deadline_passed" class="text-red-600 font-semibold uppercase">{{ project.project_days_remaining }} days remaining</span>
                <span v-else>{{ project.project_days_remaining }} days remaining</span>
            </p>
        </div>
        <div class="col-start-11 col-end-13 flex flex-row flex-nowrap justify-between p-0 content-center items-center max-h-16 text-we4vGrey-200">
            <div @click="updateProject(project.id)">
                <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
            </div>
            <div @click="deleteProject(project.id)">
                <i class="fas fa-trash h-5 cursor-pointer text-lg"></i>
            </div>
            <div class="h-5 rounded-full bg-we4vGrey-200">
                <img v-if="!displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/openGlyph.svg" alt="">
                <img v-if="displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/closeGlyph.svg" alt="">
            </div>
        </div>

        <div v-if="displayDetails" class="col-start-1 col-end-13 mt-3">
            <div v-if="project.tasks">
                <h4 class="text-sm font-regular text-we4vGrey-200">Tasks</h4>
                <div class="bg-we4vBg text-sm font-normal rounded my-2 p-2">
                    <table>
                        <thead class="text-we4vBlue table-fixed border-b border-we4vGrey-200 text-left">
                            <tr class="py-2 px1">
                                <th class="w-4/12 pl-1">Name</th>
                                <th class="w-4/12 pl-1">Timeframe</th>
                                <th class="w-2/12 pl-1">Assignee</th>
                                <th class="w-2/12 pl-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, taskKey) in project.tasks" :key="taskKey" class="border-b border-gray-200">
                                <td class="font-bold text-xs text-we4vBlue mr-2">
                                    {{ task.task_name }}
                                </td>
                                <td class="font-regular text-we4vGrey-600 text-xs">
                                    <p>{{task.task_start_date }} <i class="fa fa-arrow-right"></i> {{task.task_end_date }}</p>
                                </td>
                                <td class="font-regular text-we4vGrey-600 text-xs">
                                    <p v-if="task.team_member_username || task.group_member_username">{{ task.team_member_username ? task.team_member_username : task.group_member_username }}</p>
                                    <p v-else>{{ task.team_name }} (team)</p>
                                </td>
                                <td class="font-regular text-we4vGrey-600 text-xs">
                                    <p>View | Complete</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    'name': 'Project',

    props: [
        'project'
    ],

    data: () => {
        return {
            displayDetails: false
        }
    },

    emits: [
        'activateTaskModal'
    ],
}
</script>