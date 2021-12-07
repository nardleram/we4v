<template>
    <div>
        <p class="font-semibold text-we4vGrey-500 mb-1">Note log</p>
        <table class="mb-2 w-full">
            <thead class="text-we4vBlue table-fixed border-b border-t border-we4vGrey-200 text-left">
                <tr class="py-2 px1 text-xs">
                    <th class="w-3/12">Date</th>
                    <th class="min-w-9/12">Note</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(note, noteKey) in notes" :key="noteKey" class="border-b border-gray-200 mb-1 bg-we4vGrey-100 leading-tight">
                    <td class="text-we4vBlue font-semibold text-xs tracking-tight pl-1">{{ note.note_created_at }}</td>
                    <td class="pr-1 text-sm leading-tight"><small>{{ note.note_body }}</small></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    name: 'Notes.vue',

    props: [
        'notes',
    ]
}
</script>
