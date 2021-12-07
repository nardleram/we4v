<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop">
    </modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">
                <teleport to="#voteModals">
                        <Modal :show="showVoteModal">
                            <div @mouseleave="nowOutside(); mode = 'vote'" @mouseenter="nowInside(); mode = 'vote'" v-if="showVoteModal" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                                <Form>
                                    <template #form>
                                        <div class="flex justify-end">
                                            <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                                <div @click="showVoteModal = false; clearModal()">
                                                    <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                                </div>   
                                            </div>
                                        </div>

                                        <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Set up vote</h4>
                                        
                                        <div class="mb-2">
                                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="projectName">vote title</label>
                                            <input v-model="voteTitle" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" placeholder="E.g.: Build cob or straw-bale house?">
                                        </div>

                                        <div id="voteElements" class="my-3">
                                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="projectName">vote options (hit enter to build list of options)</label>
                                            <input @keydown.enter.prevent="addVoteEl" v-model="voteEl" class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text">
                                        </div>

                                        <h5 v-if="voteEls.length > 0" class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight ml-4">List of vote options (click option to remove it)</h5>
                                        <div id="voteEls" class="flex flex-row w-full mb-2 ml-4">
                                            <div v-for="voteEl in voteEls" :key="voteEl" class="text-xs text-we4vBlue mr-3 hover:text-we4vDarkBlue cursor-pointer">
                                                <p @click="deleteEl(voteEl)">{{ voteEl }}</p>
                                            </div>
                                        </div>

                                        <h5 class="text-sm font-semibold text-we4vGrey-500 mb-2 tracking-tight mt-3">Submit vote group or team</h5>
                                        
                                        <div v-if="$page.props.mygroups">
                                            <h5 class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight mt-2">Groups</h5>
                                            <div class="flex flex-wrap max-w-full">
                                                <div v-for="(group, groupKey) in $page.props.mygroups" :key="groupKey" class="min-w-1/3">
                                                    <input @click="voteable_type = 'App\\Models\\Group'" :value="group.group_id" name="group" class="group rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                                                    <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}</label>
                                                </div>
                                            </div>
                                            <h5 class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight mt-2">Teams</h5>
                                            <div class="flex flex-wrap max-w-full">
                                                <div v-for="(group, groupKey) in $page.props.mygroups" :key="groupKey" class="min-w-1/3">
                                                    <div v-for="(team, teamKey) in group.teams" :key="teamKey">
                                                        <input @click="voteable_type = 'App\\Models\\Team'" :value="team.team_id" name="group" class="group rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                                                        <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ team.team_id }}">{{ team.team_name }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button-grey @click="submitVoteData()">Save vote</button-grey>
                                    </template>
                                </Form>
                            </div>
                        </Modal>
                </teleport>

                <Title>
                    <template #title>
                        My votes
                    </template>
                    <template #description>
                        <p>Depending on your choice, votes are assigned to either:</p> 
                        <ol class="list-decimal ml-5 my-2 text-xs">
                            <li>
                                The associates in one of your groups (including its teams), or
                            </li>
                            <li>
                                The associates in one of your teams.
                            </li>
                        </ol>
                        <p>Enter vote options into the relevant input field one by one. Hitting return after entering an option adds that option to the list you build. When the list is finished, you’ve entered the title and selected the group of associates who will vote, your data can be saved. Those invited to vote will then be notified.</p>
                    </template>
                </Title>

                <button-blue @click="showVoteModal = true; showBackdrop = true">Set up a vote</button-blue>

                <!-- Main page – Projects -->
                <Subtitle>
                    <template #title>
                        My votes
                    </template>
                    <template #description>
                        Click open the vote panel to view results and progress.
                    </template>
                </Subtitle>
                <div v-if="$page.props.myvotes.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Vote v-for="(vote, voteKey) in myvotes" :key="voteKey" :vote="vote" />
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
import ButtonBlue from '../Jetstream/ButtonBlue'
import ButtonGrey from '@/Jetstream/ButtonGrey'
import Form from '@/Jetstream/FormSection'
import Modal from './Components/Modal'
import Vote from './Components/Vote'
import ModalBackdrop from './Components/ModalBackdrop'
import manageModals from './Composables/manageModals'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'
import { ref } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

export default {
    name: 'MyVotes',

    props: [
        'myvotes',
        'mygroups'
    ],

    components: {
        AppLayout,
        Title,
        Subtitle,
        Modal,
        ModalBackdrop,
        ButtonBlue,
        ButtonGrey,
        FlashMessage,
        ErrorMessage,
        Form,
        Vote
    },

    setup(props) {
        const {
            amInside,
            amOutside, 
            clearModal,
            nowInside, 
            nowOutside,
            onClickOutside,
            showBackdrop,
            showVoteModal,
            voteTitle,
        } = manageModals()

        const voteEl = ref(null)
        const voteEls = ref([])
        const voteable_type = ref(null)

        const addVoteEl = () => {
            if (!voteEls.value.includes(voteEl.value)) {
                voteEls.value.push(voteEl.value)
            }
            voteEl.value = ''
        }

        const deleteEl = (el) => {
            let result = voteEls.value.filter(voteEl => voteEl !== el)

            voteEls.value = result
        }

        const submitVoteData = async function () {
            let selectedGroup = document.querySelector('input[name="group"]:checked').value

            let payload = {
                'owner': usePage().props.value.authUser.id,
                'title': voteTitle.value,
                'voteable_id': selectedGroup,
                'voteable_type': voteable_type.value,
                'vote_elements': voteEls.value
            }

            try {
                await Inertia.post('/myvotes/store', payload)
                props.errors = null

                usePage().props.value.myvotes.elements.forEach(element => {
                    usePage().props.value.myvotes.num_votes_cast += element.numElVotes
                })
            } catch (err) {
                props.errors = err
            }

            voteEls.value = []
            clearModal()
        }

        const addTeamMembersToGroupVoters = () => {
            // INSANE drill down into mygroups to get at team members within relevant groups to augment myvotes array
            let groupTeams = ref([])
            let teamMembers = ref([])
            let teamMemberUsernames = ref([])
            let voteGroups = usePage().props.value.myvotes.filter(myvote => myvote.type === 'group')

            voteGroups.forEach(voteGroup => {
                let hit = usePage().props.value.mygroups.filter(mygroup => mygroup.group_name === voteGroup.group_team_name)
                hit[0].teams
                ? groupTeams.value.push({teams: hit[0].teams, groupName: hit[0].group_name})
                : null
            })

            groupTeams.value.forEach(groupTeam => {
                groupTeam.teams.forEach(team => {
                    teamMembers.value.push({members: team.teamMembers, groupName: groupTeam.groupName})
                })

            })

            teamMembers.value.forEach(teamMember => {
                teamMember.members.forEach(user => {
                    teamMemberUsernames.value.push({username: user.username, groupName: teamMember.groupName})
                })
            })

            const loop = ref(0)
            usePage().props.value.myvotes.forEach(myVote => {
                teamMemberUsernames.value.forEach(member => {
                    if (myVote.type === 'group' && member.groupName === myVote.group_team_name) {
                        !Object.values(usePage().props.value.myvotes[loop.value].voters).includes(member.username)
                        ? usePage().props.value.myvotes[loop.value].voters.push(member.username)
                        : null //console.log('Did NOT push ' + member.username + ' into voters array in ' + usePage().props.value.myvotes[loop.value].vote_title)
                    }
                })
                ++loop.value
            })
        }

        const countGroupVoters = () => {
            const loop = ref(0)
            usePage().props.value.myvotes.forEach(myVote => {
                usePage().props.value.myvotes[loop.value].numberVoters = myVote.voters.length
                ++loop.value
            })
        }

        addTeamMembersToGroupVoters()
        countGroupVoters()

        return {
            addTeamMembersToGroupVoters,
            addVoteEl,
            amInside, 
            amOutside, 
            clearModal, 
            countGroupVoters,
            deleteEl,
            nowInside, 
            nowOutside, 
            onClickOutside, 
            showBackdrop, 
            showVoteModal,
            submitVoteData,
            voteable_type,
            voteTitle,
            voteEl,
            voteEls,
        }
    }
    
}
</script>