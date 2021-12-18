<template>
    <div class="border-b border-we4vGrey-600 mb-2 pb-1 rounded-md flex items-center">
        <div class="pr-3">
            <p v-if="vote.vote_owner !== $page.props.authUser.username" class="text-xs text-we4vGrey-200 pl-1">{{ vote.vote_owner }} requests your vote on <span class="text-we4vGrey-100 italic">{{ vote.vote_title }}</span> by {{ vote.closing_date }}</p>
            <p v-else class="text-xs text-we4vGrey-200 pl-1">Cast your vote on <span class="text-we4vGrey-100 italic">{{ vote.vote_title }}</span> by {{ vote.closing_date }}</p>
        </div>
        <div class="cursor-pointer">
            <p @click="$emit('activatePendingVoteModal', vote)" class="text-sm text-we4vBlue font-semibold pr-1">Vote</p>
        </div>
    </div>
</template>

<script>

export default {
    name: 'PendingVotes',

    props: [
        'vote',
    ],

    emits: [
        'activatePendingVoteModal'
    ],
}
</script>