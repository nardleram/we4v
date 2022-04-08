<template>
    <div class="grid gap-1 grid-cols-12 grid-rows-voteBox text-we4vBg bg-we4vGrey-800 p-2 w-full rounded shadow-md mb-1 tracking-tight">
        <div class="col-start-1 col-end-11 text-sm font-semibold text-we4vBlue content-center items-center pt-1 max-h-16">
            <p class="text-we4vBlue">{{ vote.vote_title }} <span class="text-we4vGrey-200 font-light text-xs italic">(assigned to {{ vote.type }} <span class="font-normal">{{ vote.group_team_name }}</span>)</span></p>
        </div>
        <div class="col-start-11 col-end-13 flex flex-row flex-nowrap justify-around p-0 content-center items-center max-h-16 text-we4vGrey-200">
            <div v-if="open" @click="$emit('activateEditVoteModal', vote)">
                <i class="fas fa-edit h-5 cursor-pointer text-lg"></i>
            </div>
            <div class="h-5 rounded-full bg-we4vGrey-200">
                <img v-if="!displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/openGlyph.svg" alt="">
                <img v-if="displayDetails" @click="displayDetails = !displayDetails" class="h-5 object-cover cursor-pointer" src="/images/closeGlyph.svg" alt="">
            </div>
        </div>

        <div v-if="displayDetails" class="col-start-1 col-end-13">
            <p class="text-sm text-we4vGrey-200 mb-2">Voting permitted until end {{ vote.closing_date }}</p>
            <p class="text-sm font-medium text-we4vGrey-300 mb-1">Voters</p>
            <div class="flex flex-wrap flex-row">
                <div v-for="(voter, voterKey) in vote.voters" :key="voterKey" class="min-w-1/5">
                    <p class="text-xs text-we4vGrey-200">{{ voter }} <span v-if="vote.users_who_voted ? vote.users_who_voted.includes(voter) : false"> <i class="fas fa-check-circle text-we4vGreen-400"></i></span></p>
                </div>
            </div>

            <p class="text-sm font-medium text-we4vGrey-300 mb-1 mt-2">Progress (approximate)</p>
            <div v-if="!vote.num_votes_cast">
                <p class="text-xs text-we4vGrey-200">0%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 8.3" class="rounded-full w-1/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 16.6" class="rounded-full w-2/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 25" class="rounded-full w-3/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 33.3" class="rounded-full w-4/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 41.6" class="rounded-full w-5/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 50" class="rounded-full w-6/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 58.3" class="rounded-full w-7/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 66.7" class="rounded-full w-8/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 75" class="rounded-full w-9/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 83.3" class="rounded-full w-10/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else-if="(vote.num_votes_cast / (vote.voters.length) * 100) <= 99" class="rounded-full w-11/12 p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>
            <div v-else class="rounded-full w-full p-2 bg-we4vDarkBlue text-we4vGrey-200">
                <p class="text-xs text-we4vGrey-200">{{ parseInt(vote.num_votes_cast / (vote.voters.length) * 100) }}%</p>
            </div>

            <p class="text-sm font-medium text-we4vGrey-300 mb-1 mt-2">Results</p>
            <div class="flex flex-wrap flex-row">
                <div v-for="(voteEl, voteElKey) in vote.elements" :key="voteElKey" class="min-w-1/3 max-w-1/4">
                    <p class="text-xs" :class="parseInt(voteEl.numElVotes / (vote.num_votes_cast) * 100) >= 60 ? 'text-we4vGreen-500 font-semibold' : 'text-we4vGrey-200'">{{ voteEl.element_title }}: {{ vote.num_votes_cast ? parseInt(voteEl.numElVotes / (vote.num_votes_cast) * 100) : 0 }}%</p>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    'name': 'Vote',

    props: [
        'vote',
        'open'
    ],

    data: () => {
        return {
            displayDetails: false
        }
    },

    emits: [
        'activateEditVoteModal'
    ],
}
</script>