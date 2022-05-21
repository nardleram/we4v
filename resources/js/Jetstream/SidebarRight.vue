<template>
    <teleport to="#membRequestModals">
        <div v-if="showInviteModal" @mouseleave="nowOutside(); mode = type" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
            <div class="flex justify-end">
                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                    <div @click="showInviteModal = false; clearModal()">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>

            <h4 v-if="!updated" class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ updated_by }} cordially invites you to join the {{ type }} </h4>
            <h4 v-else class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ updated_by }} has changed your membership details for the {{ type }} </h4>
            <h3 class="font-serif font-semibold text-we4vBlue text-center text-2xl">{{ groupName ? groupName : teamName }}</h3>

            <h5 class="text-we4vGrey-700 text-sm mt-4">Description: {{ groupDescription ? groupDescription : teamFunction  }}</h5>
            <h5 v-if="geogArea" class="text-we4vGrey-700 text-sm mt-1">Geographical area: {{ geogArea }}</h5>
            <h5 v-if="groupRole || teamRole" class="text-we4vGrey-700 text-sm mt-1">Proposed role: {{ groupRole ? groupRole : teamRole }}</h5>
            <h5 v-if="gAdmin || tAdmin" class="text-we4vOrange text-sm font-medium mt-1">
                {{ updated_by }} requests that you become {{ type }} administrator.
            </h5>
            
            <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
            @click="storeInviteResponse(inviteData, true)">
                Accept
            </button>
            <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-red-600 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none my-4 py-2"
            @click="storeInviteResponse(inviteData, false)">
                Reject
            </button>
        </div>
    </teleport>

    <teleport to="#membRequestModals">
        <div v-if="showMembModal" @mouseleave="nowOutside" @mouseenter="nowInside" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-550 overflow-y-scroll">
            <div class="flex justify-end">
                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                    <div @click="showInviteModal = false; clearModal()">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>

            <h4 class="text-we4vBlue text-lg mb-1 -mt-8 pr-10">{{ groupName }} <span class="lowercase text-sm text-we4vGrey-500 font-light">– {{ type }}</span> <span class="text-sm text-we4vGrey-500 font-light">(owner: {{ groupOwner ? groupOwner : teamOwner }})</span></h4>
            <p v-if="groupDescription" class="text-sm">Description:  <span class="font-light text-we4vGrey-500">{{ groupDescription }}</span></p>
            <p v-if="teamFunction" class="text-sm">Function:  <span class="font-light text-we4vGrey-500">{{ teamFunction }}</span></p>
            <p class="text-sm">Your role in this <span class="lowercase">{{ type }}</span>: <span class="font-light text-we4vGrey-500">{{ role }}</span></p>
            <p v-if="teamGroupName" class="text-xs">(This team belongs to the group <span class="text-we4vBlue font-semibold">{{ teamGroupName }}</span>)</p>

            <p class="font-medium mt-3 -mb-1">Fellow <span class="lowercase">{{ type }}</span> members</p>
            <div class="flex flex-row flex-wrap w-full">
                <div v-for="(member, memberKey) in fellowMembers" :key="memberKey">
                    <div class="mr-4">
                        <inertia-link :href="route('user-show', member.slug)" as="button">
                            <small @click="clearModal" class="text-xs text-we4vBlue font-medium cursor-pointer hover:text-we4vDarkBlue">{{ member.username }}<span v-if="member.admin" class="text-we4vOrange font-bold">*</span> <span class="text-we4vGrey-500 font-light hover:text-we4vGrey-500">({{ member.role }})</span></small>
                        </inertia-link>
                    </div>
                </div>
            </div>

            <div v-if="groupTeams.length > 0">
                <p class="font-medium mt-3 -mb-1">Teams belonging to <span class="text-we4vBlue font-semibold">{{ groupName }}</span></p>
                <div v-for="(team, teamKey) in groupTeams" :key="teamKey">
                    <p class="text-xs ml-3 text-we4vBlue font-semibold -mb-1 mt-2">{{ team.name }} <span class="text-we4vGrey-500 font-medium ml-2">Description:</span> <span class="font-light text-we4vGrey-500">{{ team.function }}</span></p>
                    
                    <div class="flex flex-row flex-wrap w-full ml-6 justify-start">
                        <!-- <div class="text-xs font-semibold mr-1 w-16 p-0">Members:</div> -->
                        <div v-for="(teamMember, teamMemberKey) in team.members" :key="teamMemberKey">
                            <div class="mr-2 p-0 h-5">
                                <inertia-link :href="route('user-show', teamMember.slug)" as="button">
                                    <p @click="clearModal" class="p-0 text-xs text-we4vBlue font-medium cursor-pointer hover:text-we4vDarkBlue">{{ teamMember.username }}<span v-if="teamMember.admin" class="text-we4vOrange font-bold">*</span> <span class="text-we4vGrey-500 font-light hover:text-we4vGrey-500">({{ teamMember.role }})</span></p>
                                </inertia-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="groupTeamProjects.length > 0">
                <p class="font-medium mt-3">Projects assigned to <span class="text-we4vBlue font-semibold">{{ groupName }}</span></p>
                <div v-for="(project, projectKey) in groupTeamProjects" :key="projectKey">
                    <p class="-mb-1 text-sm text-we4vBlue font-semibold">{{ project.project_name }}</p>
                    <p class="-mb-1 text-xs text-we4vGrey-600 font-medium">Description: <span class="font-light text-we4vGrey-500">{{ project.project_description }}</span></p>
                    <p class="-mb-1 text-xs text-we4vGrey-600 font-medium">Deadline: <span class="font-light text-we4vGrey-500">{{ project.project_end_date }}</span></p>
                    <p v-if="project.project_completed" class="-mb-1 text-xs text-we4vGrey-600 font-medium">Status: <span class="font-light text-we4vGreen-500">Closed</span></p>
                    <p v-if="!project.project_completed" class="-mb-1 text-xs text-we4vGrey-600 font-medium">Status: <span class="font-light text-red-600">Open</span></p>

                    <p class="text-sm text-we4vGrey-600 font-medium mt-2 ml-3">Tasks assigned to <span class="text-we4vBlue">{{ project.project_name }}</span></p>
                    <div class="ml-3 mb-3" v-for="(task, taskKey) in project.tasks" :key="taskKey">
                        <p class="-mb-1 text-xs text-we4vBlue font-semibold">{{ task.task_name }}</p>
                        <p class="-mb-1 text-xs text-we4vGrey-600 font-medium">Description: <span class="font-light text-we4vGrey-500">{{ task.task_description }}</span></p>
                        <p class="-mb-1 text-xs text-we4vGrey-600 font-medium">Deadline: <span  class="font-light text-we4vGrey-500">{{ task.task_end_date }}</span></p>
                        <p v-if="task.task_completed" class="-mb-1 text-xs text-we4vGrey-600 font-medium">Status: <span class="font-light text-we4vGreen-500">Completed</span></p>
                    <p v-if="!task.task_completed" class="-mb-1 text-xs text-we4vGrey-500 font-medium">Status: <span class="font-light text-red-600">Open</span></p>
                    </div>
                </div>
            </div>
            
        </div>
    </teleport>

    <teleport to="#membRequestModals">
        <div v-if="showNetworkInviteModal" @mouseleave="nowOutside(); mode = type" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
            <div class="flex justify-end">
                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                    <div @click="showNetworkInviteModal = false; clearModal()">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>

            <h4 class="text-we4vGrey-600 text-sm mb-6 -mt-8 pr-10">{{ networkOwner }} cordially invites you to join your group <span class="text-we4vBlue">{{ groupName }}</span> to the network</h4>
            
            <h3 class="font-serif font-semibold text-we4vBlue text-center text-2xl">{{ networkName }}</h3>

            <h5 class="text-we4vGrey-700 text-sm mt-4">Description: {{ networkDescription }}</h5>
            
            <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-we4vBlue font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4 py-2"
            @click="storeInviteResponse(inviteData, true)">
                Accept
            </button>
            <button class="hover:bg-we4vGrey-100 border-we4vGrey-300 text-red-600 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none my-4 py-2"
            @click="storeInviteResponse(inviteData, false)">
                Reject
            </button>
        </div>
    </teleport>

    <teleport to="#pendingVoteModals">
        <div v-if="showPendingVoteModal" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
            <div class="flex justify-end">
                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                    <div @click="showPendingVoteModal = false; userCanVote = false; clearModal()">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>

            <div>
                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8 w-10/12">{{ voteOwner }} requests your vote to help decide on <span class="italic text-we4vGrey-600">{{ voteTitle }}</span></h4>
                <p class="text-we4vGrey-500 text-sm">Closing date: <span class="italic font-medium">{{ voteClosingDate }}</span></p>
                <h5 class="text-sm font-medium text-we4vGrey-500 mb-1 tracking-tight mt-4">Vote options</h5>
                <div v-for="(element, elementKey) in voteElements" :key="elementKey">
                    <input @click="userCanVote = true" :value="element.element_id" name="element" class="group" type="radio">
                    <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ element.element_id }}">{{ element.element_title }}</label>
                </div>
            </div>

            <button-grey @click="storeVoteResponse()" :enabled="userCanVote" id="submitForm">Submit vote</button-grey>
            
        </div>
    </teleport>

    <teleport to="#projectModals">
        <div v-if="showUserTaskModal" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
            <div class="flex justify-end">
                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                    <div @click="showUserTaskModal = false; clearModal()">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>

            <div>
                <h4 class="text-we4vBlue font-semibold mb-4 -mt-8 w-10/12">{{ taskName }}
                <p class="text-we4vGrey-500 text-sm">Deadline: <span class="italic font-medium">{{ taskEndDate }}</span></p></h4>
                <p class="text-we4vGrey-500 text-sm">Description: <span class="italic font-medium">{{ taskDescription }}</span></p>
                <p class="text-we4vGrey-500 text-sm">In project: <span class="italic font-medium">{{ taskProjectName }}</span></p>
                <p class="text-we4vGrey-500 text-sm">Task assigned to: <span class="italic font-medium">{{ taskGroupName ? taskGroupName : taskTeamName }}</span></p>
                <div class="flex flex-wrap">
                    <p class="text-we4vGrey-500 text-sm">Fellow task members: <span v-if="!taskMembers">None</span></p>
                    <p v-for="(member, memberKey) in taskMembers" :key="memberKey" class="text-we4vGrey-500 text-sm italic font-medium mx-2">{{ member.username}}</p>
                </div>
            </div>

            <div v-if="taskNotes || projectNotes">
                <Notes :taskNotes="taskNotes" :projectNotes="projectNotes" />
            </div>

            <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a task note</h5>
            <textarea v-model="taskNoteBody" name="taskNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none"></textarea>

            <h5 class="text-sm font-semibold text-we4vGrey-500 mb-1 tracking-tight">Log a project note</h5>
            <textarea v-model="projectNoteBody" name="projectNoteBody" cols="30" rows="5" class="w-full text-we4vGrey-600 text-xs focus:outline-none mb-3"></textarea>

            <input v-model="taskCompleted" class="rounded-sm border-indigo-100 shadow-sm focus:outline-none" type="checkbox" >
            <label class="text-we4vGreen-500 font-semibold text-xs ml-2 w-full text-center" for="{{ taskCompleted }}">Task completed</label>

            <button-grey @click="updateTask()" :enabled="true" id="submitForm">Update task</button-grey>
            
        </div>
    </teleport>

    <teleport to="#projectModals">
        <div v-if="showVoteResultsModal" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6 max-h-600 overflow-y-scroll">
            <div class="flex justify-end">
                <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                    <div @click="showVoteResultsModal = false; clearModal()">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>

            <div>
                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8 w-10/12">{{ voteTitle }}
                <p class="text-we4vGrey-500 text-sm">Vote closed on: <span class="italic font-medium">{{ voteClosingDate }}</span></p></h4>
                <p class="text-we4vGrey-500 text-sm">Vote was put to members of: <span class="italic font-medium">{{ voteGroupName ? voteGroupName : voteTeamName }}</span></p>
                <div class="flex flex-wrap">
                    <p class="text-we4vGrey-500 text-sm">Results: <span v-if="!voteResults">None</span></p>
                    <p v-for="(result, resultKey) in voteResults" :key="resultKey" class="text-we4vGrey-500 text-sm italic font-medium mx-2">{{ result.element_title}}: {{ result.numElVotes}}</p>
                </div>
            </div>
            
        </div>
    </teleport>

    <div class="fixed top-16 right-0 w-1/4 h-screen p-2 pl-4 pt-4 bg-we4vGrey-800 text-we4vBg font-light">
        <sidebar-right-element>
            <template #title>
                Association requests
            </template>

            <template #bell>
                <span v-if="$page.props.myPendingAssocReqs.length > 0" class="font-extrabold text-we4vOrange cursor-pointer"
                    @click="showAssocReqs = !showAssocReqs">
                    <i class="far fa-bell text-sm"></i>
                </span>

                <span v-else class="font-extrabold text-we4vGrey-600 text-sm">
                    <i class="far fa-bell"></i>
                </span>
            </template>
        </sidebar-right-element>
        <div v-if="showAssocReqs" class="w-full -mt-2 ml-3 -mr-2 mb-2 bg-we4vGrey-900 rounded-bl-xl border-b border-we4vGrey-600">
            <pending-assoc-reqs v-for="(req, reqKey) in $page.props.myPendingAssocReqs" :key="reqKey" :req="req" />
        </div>

        <sidebar-right-element>
            <template #title>
                Group/team invitations
            </template>

            <template #bell>
                <span v-if="$page.props.myPendingMembReqs.length > 0" class="text-sm font-extrabold text-we4vOrange cursor-pointer"
                    @click="showMembReqs = !showMembReqs">
                    <i class="far fa-bell"></i>
                </span>
                <span v-else class="font-extrabold text-we4vGrey-600 text-sm">
                    <i class="far fa-bell"></i>
                </span>
            </template>
        </sidebar-right-element>
        <table v-if="showMembReqs" class="w-full -mt-2 ml-3 -mr-2 mb-2 bg-we4vGrey-900 rounded-bl-xl border-b border-we4vGrey-600">
            <thead class="text-we4vBlue table-fixed border border-we4vGrey-800 text-left text-xs">
                <tr class="py-2 px1">
                    <th class="w-2/12 border border-we4vGrey-800 py-2 px-1">From</th>
                    <th class="w-4/12 border border-we4vGrey-800 py-2 px-1">Name</th>
                    <th class="w-1/2 border border-we4vGrey-800 py-2 px-1">Description</th>
                </tr>
            </thead>
            <tbody class="border-b rounded-bl-xl border-we4vGrey-800 text-we4vGrey-100">
                <pending-memb-reqs @activate-invite-modal="hydrateInviteModal" v-for="(req, reqKey) in $page.props.myPendingMembReqs" :key="reqKey" :req="req" />
            </tbody>
        </table>

        <sidebar-right-element>
            <template #title>
                Network requests
            </template>

            <template #bell>
                 <span v-if="$page.props.myPendingNetworkReqs.length > 0" class="text-sm font-extrabold text-we4vOrange cursor-pointer"
                    @click="showNetworkReqs = !showNetworkReqs">
                    <i class="far fa-bell"></i>
                </span>

                <span v-else class="font-extrabold text-we4vGrey-600 text-sm">
                    <i class="far fa-bell"></i>
                </span>
            </template>
        </sidebar-right-element>
        <table v-if="showNetworkReqs" class="w-full -mt-2 ml-3 -mr-2 mb-2 bg-we4vGrey-900 rounded-bl-xl">
            <thead class="text-we4vBlue table-fixed border border-we4vGrey-800 text-left">
                <tr>
                    <th class="text-xs w-1/3 border border-we4vGrey-800 pt-2 pb-1 px-1">From</th>
                    <th class="text-xs w-1/3 border border-we4vGrey-800 pt-2 pb-1 px-1">Network</th>
                    <th class="text-xs w-1/3 border border-we4vGrey-800 pt-2 pb-1 px-1">Group</th>
                </tr>
            </thead>
            <tbody class="border border-we4vGrey-800 text-we4vGrey-100">
                <pending-network-reqs @activate-network-invite-modal="hydrateNetworkInviteModal" v-for="(req, reqKey) in $page.props.myPendingNetworkReqs" :key="reqKey" :req="req" />
            </tbody>
        </table>

        <sidebar-right-element>
            <template #title>
                Pending votes
            </template>

            <template #bell>
                 <span v-if="Object.keys($page.props.myPendingVotes).length > 0" class="text-sm font-extrabold text-we4vOrange cursor-pointer"
                    @click="showPendingVotes = !showPendingVotes">
                    <i class="far fa-bell"></i>
                </span>

                <span v-else class="font-extrabold text-we4vGrey-600 text-sm">
                    <i class="far fa-bell"></i>
                </span>
            </template>
        </sidebar-right-element>
        <div v-if="showPendingVotes" class="-mt-2 ml-3 -mr-2 mb-2 bg-we4vGrey-900 rounded-bl-xl">
            <pending-votes @activate-pending-vote-modal="onActivatePendingVoteModal" v-for="(vote, voteKey) in $page.props.myPendingVotes" :key="voteKey" :vote="vote" />
        </div>

        <sidebar-right-element>
            <template #title>
                <span @click="showAssocs = !showAssocs" class="cursor-pointer">Associates</span>
            </template>
        </sidebar-right-element>
        <div v-if="showAssocs" class="-mt-2 ml-3 -mr-2 mb-2 bg-we4vGrey-900 rounded-bl-xl">
            <Associates />
        </div>

        <sidebar-right-element>
            <template #title>
                <span @click="showMembs = !showMembs" class="cursor-pointer">Memberships</span>
            </template>
        </sidebar-right-element>
        <div v-if="showMembs && Object.keys($page.props.myMemberships).length" class="-mt-2 ml-3 -mr-2 mb-2 bg-we4vGrey-900 rounded-bl-xl max-h-48 overflow-y-scroll">
            <Memberships @activate-memb-modal="onActivateMembModal" @mouseover="nowOutside" />
        </div>
        <div v-if="showMembs && !Object.keys($page.props.myMemberships).length" class="tracking-tight text-right -mt-2 mb-1">
            <small class="text-we4vGrey-200 text-xs">You are not yet a member of any group or team</small>
        </div>

        <sidebar-right-element>
            <template #title>
                <span @click="showTasks = !showTasks" class="cursor-pointer">Assigned tasks</span>
            </template>
        </sidebar-right-element>
        <div v-if="showTasks && Object.keys($page.props.myOpenTasks).length">
            <table v-if="showTasks" class="text-xs font-light tracking-tighter bg-we4vGrey-900 w-full -mt-2 ml-3 -mr-2 mb-2 rounded-md">
                <thead class="text-we4vBlue table-fixed border border-we4vGrey-800 text-left">
                    <tr>
                        <th class="w-3/12 border border-we4vGrey-800 py-2 px-1">From</th>
                        <th class="w-6/12 border border-we4vGrey-800 py-2 px-1">Name</th>
                        <th class="w-3/2 border border-we4vGrey-800 py-2 px-1">Deadline</th>
                    </tr>
                </thead>
                <tbody class="border border-we4vGrey-800 text-we4vGrey-100">
                    <tr v-for="(task, taskKey) in $page.props.myOpenTasks" :key="taskKey" @click="activateUserTaskModal(task)" class="cursor-pointer hover:bg-we4vBlue">
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ task.owner}}
                        </td>
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ task.name}}
                        </td>
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ task.end_date}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showTasks && !Object.keys($page.props.myOpenTasks).length" class="text-right -mt-2 mb-1">
            <small class="text-we4vGrey-200 text-xs">There are no tasks currently assigned to you</small>
        </div>

        <sidebar-right-element>
            <template #title>
                <span @click="showVoteResults = !showVoteResults" class="cursor-pointer">Closed votes</span>
            </template>
        </sidebar-right-element>
        <div v-if="showVoteResults && $page.props.closedVotes.length">
            <table class="text-xs font-light tracking-tighter bg-we4vGrey-900 w-full -mt-2 ml-3 -mr-2 mb-2 rounded-md">
                <thead class="text-we4vBlue table-fixed border border-we4vGrey-800 text-left">
                    <tr>
                        <th class="w-3/12 border border-we4vGrey-800 py-2 px-1">Vote</th>
                        <th class="w-6/12 border border-we4vGrey-800 py-2 px-1">Group/team</th>
                        <th class="w-3/2 border border-we4vGrey-800 py-2 px-1">End date</th>
                    </tr>
                </thead>
                <tbody class="border border-we4vGrey-800 text-we4vGrey-100">
                    <tr v-for="(cVote, cVoteKey) in $page.props.closedVotes" :key="cVoteKey" @click="activateShowClosedVoteModal(cVote)" class="cursor-pointer hover:bg-we4vBlue">
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ cVote.vote_title }}
                        </td>
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ cVote.group_name ? cVote.group_name : cVote.team_name  }}
                        </td>
                        <td class="px-1 py-2 border border-we4vGrey-800">
                            {{ cVote.close_date }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showVoteResults && Object.keys($page.props.closedVotes).length === 0" class="text-right -mt-2 mb-1">
            <small class="text-we4vGrey-200 text-xs">No closed-vote results to display</small>
        </div>
    </div>
</template>

<script>
import SidebarRightElement from '../Pages/Components/SidebarRightElement'
import PendingAssocReqs from '../Pages/Components/PendingAssocReqs'
import PendingMembReqs from '../Pages/Components/PendingMembReqs'
import PendingNetworkReqs from '../Pages/Components/PendingNetworkReqs'
import PendingVotes from '../Pages/Components/PendingVotes'
import Associates from '../Pages/Components/MyAssociates'
import Networks from '../Pages/Components/MyNetworks'
import Memberships from '../Pages/Components/MyMemberships'
import Notes from '../Pages/Components/Notes'
import Modal from '../Pages/Components/Modal'
import manageModals from '../Pages/Composables/manageModals'
import ButtonGrey from '../Jetstream/ButtonGrey.vue'
import { watch, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'SidebarRight',

    components: {
        ButtonGrey,
        SidebarRightElement,
        PendingAssocReqs,
        PendingMembReqs,
        PendingNetworkReqs,
        PendingVotes,
        Associates,
        Networks,
        Memberships,
        Modal,
        Notes,
    },

    data: () => {
        return {
            showAssocs: false,
            showMembs: false,
            showNetworks: false,
            showTasks: false,
            showVoteResults: false
        }
    },

    props: [
        'success',
        'errors',
    ],

    emits: [
        'activateUserTaskModal'
    ],

    setup(props) {
        const {
            activateShowClosedVoteModal,
            activateUserTaskModal,
            amOutside, 
            amInside,
            clearModal,
            fellowMembers,
            gAdmin,
            geogArea,
            groupDescription,
            groupId,
            groupName,
            groupOwner,
            groupRequester,
            groupRole,
            groupTeamProjects,
            groupTeams,
            hydrateInviteModal,
            hydrateNetworkInviteModal,
            inviteData,
            membershipId,
            mode,
            networkDescription,
            networkId,
            networkName,
            networkOwner,
            nowInside, 
            nowOutside,
            onActivateMembModal,
            onActivatePendingVoteModal,
            onClickOutside,
            projectCompleted,
            projectNotes,
            role,
            showInviteModal,
            showMembModal,
            showNetworkInviteModal,
            showPendingVoteModal,
            showUserTaskModal,
            showVoteResultsModal,
            tAdmin,
            taskableId,
            taskableType,
            taskCompleted,
            taskDescription,
            taskEndDate,
            taskGroupName,
            taskId,
            taskInputEndDate,
            taskMembers,
            taskName,
            taskNotes,
            taskOwner,
            taskProjectId,
            taskProjectName,
            taskTeamName,
            teamFunction,
            teamGroupName,
            teamName,
            teamOwner,
            teamId,
            teamRequester,
            teamRole,
            type,
            updated_by,
            updated,
            voteClosingDate,
            voteElements,
            voteGroupName,
            voteId,
            voteOwner,
            voteResults,
            voteTeamName,
            voteTitle,
        } = manageModals()

        const showUnansweredInvites = ref(false)
        const showMembReqs = ref(false)
        const taskNoteBody = ref(null)
        const projectNoteBody = ref(null)
        const showAssocReqs = ref(false)
        const showNetworkReqs = ref(false)
        const userCanVote = ref(false)
        const showPendingVotes = ref(false)

        const storeInviteResponse = async (inviteData, response) => {
            let payload = {}

            if (inviteData[0].type === 'group') {
                payload = {
                    'id': inviteData[0].membership_id,
                    'membershipable_id': inviteData[0].groupId,
                    'confirmed': response,
                    'user_id': usePage().props.value.authUser.id,
                    'group_id': null,
                }
            }
            if (inviteData[0].type === 'team') {
                payload = {
                    'id': inviteData[0].membership_id,
                    'membershipable_id': inviteData[0].teamId,
                    'confirmed': response,
                    'user_id': usePage().props.value.authUser.id,
                    'group_id': null,
                }
            }
            if (!inviteData[0].type) {
                payload = {
                    'id': inviteData[0].membership_id,
                    'membershipable_id': inviteData[0].networkId,
                    'confirmed': response,
                    'group_id': inviteData[0].groupId,
                    'user_id': null
                }
            }

            try {
                await Inertia.patch('/memberships/accept-reject', payload)
                props.errors = null
            } catch(err) {
                props.errors = err
            }

            showMembReqs.value = false
            showNetworkReqs.value = false
            clearModal()
        }

        const storeVoteResponse = async () => {
            let selectedElement = document.querySelector('input[name="element"]:checked').value

            var payload = {
                'vote_id': voteId.value,
                'element_id': selectedElement,
                'user_id': usePage().props.value.authUser.id
            }

            try {
                await Inertia.post('/cast-vote/store', payload)
                props.errors = null
            } catch(err) {
                props.errors = err
            }
            
            usePage().props.value.myPendingVotes.length === 0
            ? showPendingVotes.value = false
            : null

            clearModal()
            showPendingVoteModal.value = false
        }

        const updateTask = async function () {
            let taskNote = {
                'body': taskNoteBody.value,
                'noteable_id': taskId.value,
                'noteable_type': 'App\\Models\\Task'
            }

            let projectNote = {
                'body': projectNoteBody.value,
                'noteable_id': taskProjectId.value,
                'noteable_type': 'App\\Models\\Project'
            }

            let payload = {
                'id': taskId.value,
                'name': taskName.value,
                'description': taskDescription.value,
                'completed': taskCompleted.value,
                'membershipable_type': 'App\\Models\\Task',
                'membershipable_id': taskId.value,
                'taskable_id': taskableId.value,
                'taskable_type': taskableType.value,
                'end_date': taskInputEndDate.value,
                'taskNote': taskNote,
                'projectNote': projectNote,
                'members': []
            }

            console.log(payload)
            
            try {
                await Inertia.patch('/mytasks/update', payload)
                props.errors = null
                taskNoteBody.value = null
                projectNoteBody.value = null
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

        return {
            activateShowClosedVoteModal,
            activateUserTaskModal,
            amOutside, 
            amInside,
            clearModal,
            fellowMembers,
            gAdmin,
            geogArea,
            groupId,
            groupName,
            groupOwner,
            groupDescription,
            groupRequester,
            groupRole,
            groupTeamProjects,
            groupTeams,
            hydrateInviteModal,
            hydrateNetworkInviteModal,
            inviteData,
            membershipId,
            mode,
            networkDescription,
            networkId,
            networkName,
            networkOwner,
            nowInside, 
            nowOutside,
            onActivateMembModal,
            onActivatePendingVoteModal,
            onClickOutside,
            projectCompleted,
            projectNoteBody,
            projectNotes,
            role,
            showAssocReqs,
            showInviteModal,
            showMembModal,
            showNetworkInviteModal,
            showMembReqs,
            showNetworkReqs,
            showPendingVoteModal,
            showPendingVotes,
            showUnansweredInvites,
            showUserTaskModal,
            showVoteResultsModal,
            storeInviteResponse,
            storeVoteResponse,
            taskableId,
            taskableType,
            taskCompleted,
            taskDescription,
            taskEndDate,
            taskGroupName,
            taskId,
            taskInputEndDate,
            taskMembers,
            taskName,
            taskNoteBody,
            taskNotes,
            taskOwner,
            taskProjectId,
            taskProjectName,
            taskTeamName,
            tAdmin,
            teamFunction,
            teamGroupName,
            teamId,
            teamName,
            teamOwner,
            teamRequester,
            teamRole,
            type,
            updated_by,
            updated,
            updateTask,
            userCanVote,
            voteClosingDate,
            voteGroupName,
            voteElements,
            voteId,
            voteOwner,
            voteResults,
            voteTeamName,
            voteTitle,
        }
    },

}
</script>

<style scoped>
    .fade-enter-active, .face-leave-active {
        transition: opacity .7s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0; 
    }
</style>