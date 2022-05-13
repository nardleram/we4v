<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-projectBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight min-h-24">
        <div @click="$emit('activateEditAdminTaskModal', task)" class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center cursor-pointer pt-1">
            <p>{{ task.task_name }} <span class="text-we4vGrey-200 font-light text-xs italic">(assigned to <span class="font-normal">{{ task.team_name }}</span>)</span></p>
            <p class="text-xs font-light text-we4vGrey-200 italic my-1">{{ task.task_description }}</p>
            <p class="text-we4vGrey-300">Task timeframe: {{ task.task_start_date }} <i class="fa fa-arrow-right"></i> {{ task.task_end_date }} – 
                <span v-if="task.task_deadline_passed" class="text-red-600 font-semibold uppercase">Deadline passed</span>
                <span v-else-if="task.task_days_remaining < 5 && !task.task_deadline_passed" class="text-red-600 font-semibold uppercase">{{ task.task_days_remaining }} days to go</span>
                <span v-else>{{ task.task_days_remaining }} days to go</span>
            </p>
        </div>
        <div class="col-start-11 col-end-13 flex flex-row flex-nowrap justify-around p-0 content-center items-center max-h-16 text-we4vGrey-200">
            <div @click="$emit('activateEditAdminTaskModal', task)">
                <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
            </div>
            <div>
                <i v-if="!displayDetails" @click="displayDetails = !displayDetails" class="fas fa-lock h-5 cursor-pointer text-lg"></i>
                <i v-if="displayDetails" @click="displayDetails = !displayDetails" class="fas fa-unlock h-5 cursor-pointer text-lg"></i>
            </div>
        </div>

        <div v-if="displayDetails" class="col-start-1 col-end-13 mt-3">
            <p class="text-xs mt-2 text-we4vGrey-300">(Task belongs to <span class="italic">{{ task.project_name }}</span> and was created by {{ task.task_owner }})</p>
            <div v-if="task.team_members">
                <h4 class="text-sm font-regular text-we4vGrey-200 mt-3">Team members</h4>
                <div class="bg-we4vBg text-sm font-normal rounded my-2 p-2 max-h-48 overflow-y-scroll">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <p v-for="(member, memberKey) in task.team_members" :key="memberKey" class="mt-2 mb-1 font-bold text-xs text-we4vGrey-400 mr-3">{{ member.username }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    'name': 'Task',

    props: [
        'task'
    ],

    data: () => {
        return {
            displayDetails: false
        }
    },

    emits: [
        'activateEditAdminTaskModal'
    ],
}
</script>