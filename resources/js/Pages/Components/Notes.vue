<template>
    <div v-if="Object.keys(taskNotes).length > 0" class="mb-2">
        <p class="font-bold text-sm text-we4vGrey-500 mb-1 mt-2">Task-note log</p>
        <div class="max-h-48 overflow-y-scroll">
            <table class="mb-2 w-full">
                <thead class="text-we4vBlue table-fixed border-b border-t border-we4vGrey-200 text-left">
                    <tr class="py-2 px1 text-xs">
                        <th class="w-3/12">Date</th>
                        <th class="w-7/12">Note</th>
                        <th class="w-2/12">Author</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(taskNote, taskNoteKey) in taskNotes" :key="taskNoteKey" class="border-b border-gray-200 mb-1 bg-we4vGrey-100 leading-tight">
                        <td class="text-we4vBlue font-semibold text-xs tracking-tight pl-1">{{ taskNote.task_note_created_at }}</td>
                        <td class="pr-1 text-sm leading-tight"><small>{{ taskNote.task_note_body }}</small></td>
                        <td class="pr-1 text-xs leading-tight italic text-we4vGrey-400"><small>{{ taskNote.task_note_author }}</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div v-if="Object.keys(projectNotes).length > 0" class="mb-2">
        <p class="font-bold text-sm text-we4vGrey-500 mb-1 mt-2">Project-note log</p>
        <div class="max-h-48 overflow-y-scroll">
            <table class="w-full">
                <thead class="text-we4vBlue table-fixed border-b border-t border-we4vGrey-200 text-left">
                    <tr class="py-2 px1 text-xs">
                        <th class="w-3/12">Date</th>
                        <th class="w-7/12">Note</th>
                        <th class="w-2/12">Author</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(projectNote, projectNoteKey) in projectNotes" :key="projectNoteKey" class="border-b border-gray-200 mb-1 bg-we4vGrey-100 leading-tight">
                        <td class="text-we4vBlue font-semibold text-xs tracking-tight pl-1">{{ projectNote.project_note_created_at }}</td>
                        <td class="pr-1 text-sm leading-tight"><small>{{ projectNote.project_note_body }}</small></td>
                        <td class="pr-1 text-xs leading-tight italic text-we4vGrey-400"><small>{{ projectNote.project_note_author }}</small></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

export default {
    name: 'Notes',

    props: [
        'taskNotes',
        'projectNotes'
    ]
}
</script>
