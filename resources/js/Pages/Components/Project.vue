<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-projectBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight">
        <div @click="$emit('activateTaskModal', project)" class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center cursor-pointer pt-1 max-h-16">
            <p>{{ project.project_name }} <span class="text-we4vGrey-200 font-light text-xs italic">(assigned to <span class="font-normal">{{ project.project_group_name }}</span>)</span></p>
            <p class="text-xs font-light text-we4vGrey-200 italic my-1">{{ project.project_description }}</p>
            <p class="text-we4vGrey-300">Project timeframe: {{ project.project_start_date }} <i class="fa fa-arrow-right"></i> {{ project.project_end_date }}</p>
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