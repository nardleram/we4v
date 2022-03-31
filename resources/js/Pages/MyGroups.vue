<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>

    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight">
                <!-- Modal forms -->
                <teleport to="#groupModals">
                    <div @mouseleave="nowOutside(); mode = 'group'" @mouseenter="nowInside(); mode = 'group'" v-if="showGroupModal" class="bg-white z-50 fixed top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()" >
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>   
                                    </div>
                                </div>
                                <h4 v-if="!edit" class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Add a group</h4>
                                <h4 v-if="edit" class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Edit group <span class="italic text-we4vGrey-600">{{ groupName }}</span></h4>

                                <div>
                                    <Input :name="groupName" :modelValue="groupName" :id="'groupName'" :label="'group name'" :placeholder="'Enter group name (required)'" :type="'text'" required @update-model-value="groupName = $event" @check-if-user-may-submit="checkIfUserMaySubmit('group')"/>
                                </div>

                                <div>
                                    <Input :name="groupDescription" :modelValue="groupDescription" :id="'groupDescription'" :label="'describe group'" :placeholder="'Enter group description (required)'" :type="'text'" required @update-model-value="groupDescription = $event" @check-if-user-may-submit="checkIfUserMaySubmit('group')"/>
                                </div>

                                <div>
                                    <Input :name="geogArea" :modelValue="geogArea" :label="'geographic area'" :placeholder="'Enter group’s geagraphic area (optional)'" :type="'text'" @update-model-value="geogArea = $event" />
                                </div>

                                <h4  v-if="!edit" class="text-we4vBlue font-semibold text-sm mt-4">Invite associates to join your group (optional)</h4>
                                <h4  v-if="edit" class="text-we4vBlue font-semibold text-sm mt-4">Add/remove associates to/from your group (optional)</h4>

                                <div v-if="!edit" class="w-full m-0 flex flex-col items-center max-h-48 overflow-y-scroll pl-1">
                                    <div v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="w-full flex flex-row justify-between items-center">
                                        <div class="w-1/4">
                                            <input :id="'checkbox_'+associate.user_id" class="invitedAssocs" @click="addRemoveAssociate('group', associate.user_id), selectedAssoc = !selectedAssoc, roleUserId = associate.user_id" :value="associate.user_id" type="checkbox">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                            <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                        </div>

                                        <div class="w-1/2">
                                            <InputNoLabel class="assocRoles" :id="associate.user_id" :placeholder="'define role'" :type="'text'" @collect-member-roles="onCollectMemberRoles" />
                                        </div>

                                        <div class="w-1/4 pl-8">
                                            <input class="admins" @click="makeAdmin('group')" :value="associate.user_id" type="checkbox">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="edit" class="w-full m-0 flex flex-col items-center max-h-48 overflow-y-scroll pl-1">
                                    <div v-for="(groupMember, groupMembersEditKey) in groupMembersEdit" :key="groupMembersEditKey" class="w-full flex flex-row justify-between items-center">
                                        <div class="w-1/4">
                                            <input :id="'checkbox_'+groupMember.user_id" class="invitedAssocsEdit" @click="addRemoveAssociate('group', groupMember.user_id), selectedAssoc = !selectedAssoc, roleUserId = groupMember.user_id" :value="groupMember.user_id" :checked="groupMember.invited && !groupMember.declined" type="checkbox">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ groupMember.user_id }}">{{ groupMember.username }}</label>
                                            <img :src="'/'+groupMember.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                        </div>

                                        <div class="w-1/2">
                                            <InputNoLabel class="assocRolesEdit" :id="groupMember.user_id" :modelValue="groupMember.role" :placeholder="'define role'" :type="'text'" @collect-member-roles="onCollectMemberRoles" @update-model-value="groupMember.role = $event"/>
                                        </div>

                                        <div class="w-1/4 pl-8">
                                            <input class="adminsEdit" @click="makeAdmin('group')" :value="groupMember.user_id" :checked="groupMember.admin" type="checkbox">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add input field for tags -->

                                <button-grey @click="submitGroupData()" :type="'submit'" id="submitForm" :enabled="greyButtonEnabled">
                                    <span v-if="!edit">Save group, send invites</span>
                                    <span v-else>Update group (send invites)</span>
                                </button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div @mouseleave="nowOutside(); mode = 'group'" @mouseenter="nowInside(); mode = 'group'" v-if="showTransferGroupModal" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()" >
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>   
                                    </div>
                                </div>
                                <h4 class="uppercase text-we4vBlue font-semibold -mt-8">Transfer group ownership – <span class="text-we4vGrey-600">{{ groupName }}</span></h4>
                                <p class="text-red-600 text-xs font-medium -mt-1 mb-4">(Transfering ownership of a group entails tranfering ownership of its teams, all projects assigned to it, as well as all tasks – <span class="italic">not</span> including those tasks set up by team administrators – belonging to those projects, and all associated votes.)</p>

                                <h4 class="text-we4vBlue font-semibold text-sm mt-4">Select the associate who will assume ownership of {{ groupName }}</h4>
                                <div class="w-full flex flex-row flex-wrap max-h-48 overflow-y-scroll items-center justify-start">
                                    <div class="lg:w-1/4 md:w-1/3 sm:w-1/2 mb-2" v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey">
                                        <div class="w-full">
                                            <input :value="associate.user_id" name="transferToAssocs" type="radio" @click="greyButtonEnabled = true">
                                            <label class="text-we4vGrey-500 text-xs ml-2 text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                            <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                        </div>
                                    </div>
                                </div>

                                <button-grey @click="submitTransferGroupData()" :type="'submit'" :disabled="!transferGroupSelected" :enabled="greyButtonEnabled">
                                    <span>Transfer group</span>
                                </button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div v-if="showTeamModal" @mouseleave="nowOutside(); mode = 'team'" @mouseenter="nowInside(); mode = 'team'" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <div class="w-9/12 text-sm font-bold mb-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.members">{{ errors.members }}
                        </div>
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()">
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>  
                                    </div>
                                </div>
                                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Add team to <span class="italic text-we4vGrey-600">{{ groupName }}</span></h4>

                                <div>
                                    <Input :id="'teamName'" :name="teamName" :modelValue="teamName" :label="'team name'" :placeholder="'Enter team name (required)'" :type="'text'" required @update-model-value="teamName = $event" @check-if-user-may-submit="checkIfUserMaySubmit('team')"/>
                                </div>

                                <div>
                                    <Input :id="'teamFunction'" :name="teamFunction" :modelValue="teamFunction" :label="'team function'" :placeholder="'Define team’s function (required)'" :type="'text'" required @update-model-value="teamFunction = $event" @check-if-user-may-submit="checkIfUserMaySubmit('team')" />
                                </div>

                                <h4 class="text-we4vBlue font-semibold text-sm mt-4">Invite at least one associate to join your team (required)</h4>
                                <div class="w-full m-0 flex flex-row flex-wrap justify-between max-h-48 overflow-y-scroll pl-1">
                                    <div v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey" class="w-full flex flex-row justify-between items-center">
                                        <div class="w-1/4">
                                            <input :id="'checkbox_'+associate.user_id" class="invitedAssocs" @click="addRemoveAssociate('team', associate.user_id), selectedAssoc = !selectedAssoc, roleUserId = associate.user_id" :value="associate.user_id" type="checkbox">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                            <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                        </div>
                                        <div class="w-1/2">
                                            <InputNoLabel class="assocRoles" :id="associate.user_id" :placeholder="'define role'" :type="'text'" @collect-member-roles="onCollectMemberRoles" />
                                        </div>
                                        <div class="w-1/4 pl-8">
                                            <input class="admins" @click="makeAdmin('team')" :value="associate.user_id" type="checkbox">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ associate.user_id }}">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <button-grey @click="submitTeamData()" :type="'submit'" id="submitForm" :enabled="greyButtonEnabled">
                                    <span>Save team, send invites</span>
                                </button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div v-if="showEditTeamModal" @mouseleave="nowOutside(); mode = 'team'" @mouseenter="nowInside(); mode = 'team'" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <div class="w-9/12 text-sm font-bold mb-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.members">
                            {{ errors.members }}
                        </div>
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()">
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>  
                                    </div>
                                </div>
                                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Edit <span class="italic text-we4vGrey-600">{{ teamName }}</span></h4>

                                <div>
                                    <Input :id="'teamName'" :name="teamName" :modelValue="teamName" :label="'team name'" :placeholder="'Enter team name (required)'" :type="'text'" required @update-model-value="teamName = $event" />
                                </div>

                                <div>
                                    <Input :id="'teamFunction'" :name="teamFunction" :modelValue="teamFunction" :label="'team function'" :placeholder="'Define team’s function (required)'" :type="'text'" required @update-model-value="teamFunction = $event" />
                                </div>

                                <h4 class="text-we4vBlue font-semibold text-sm mt-4">Add/Remove associates to/from this team</h4>
                                <div class="w-full m-0 flex flex-row flex-wrap justify-between max-h-48 overflow-y-scroll pl-1">
                                    <div v-for="(teamMember, teamMembersEditKey) in teamMembersEdit" :key="teamMembersEditKey" class="w-full flex flex-row justify-between items-center">
                                        <div class="w-1/4">
                                            <input :id="'checkbox_'+teamMember.user_id" @click="addRemoveAssociate('team', teamMember.user_id), selectedAssoc = !selectedAssoc, roleUserId = teamMember.user_id" :value="teamMember.user_id" class="invitedAssocsEdit" type="checkbox" :checked="teamMember.invited && !teamMember.declined">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ teamMember.user_id }}">{{ teamMember.username }}</label>
                                            <img :src="'/'+teamMember.path" alt="" class="rounded-full w-8 h-8 object-cover ml-6">
                                        </div>
                                        <div class="w-1/2">
                                            <InputNoLabel class="assocRolesEdit" :id="teamMember.user_id" :modelValue="teamMember.role" :placeholder="'define role'" :type="'text'" @collect-member-roles="onCollectMemberRoles" />
                                        </div>
                                        <div class="w-1/4 pl-8">
                                            <input @click="makeAdmin('team')" :value="teamMember.user_id" class="adminsEdit" type="checkbox" :checked="teamMember.admin">
                                            <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" :for="teamMember.user_id">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <button id="submitForm" class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                @click="submitTeamData()">
                                    <span>Update team (send invites)</span>
                                </button>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div v-if="showNetworkModal" @mouseleave="nowOutside(); mode = 'network'" @mouseenter="nowInside(); mode = 'network'" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <div class="w-9/12 text-sm font-bold mb-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.members">
                            {{ errors.members }}
                        </div>
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()">
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>  
                                    </div>
                                </div>
                                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Create network</h4>

                                <div>
                                    <Input :name="networkName" :label="'network name'" :placeholder="'Enter network name (required)'" :type="'text'" required />
                                </div>

                                <div>
                                <Input :name="networkDescription" :label="'network description'" :placeholder="'Enter network’s description (required)'" :type="'text'" required />
                                </div>

                                <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                @click="submitNetworkData()">
                                    <span>Save network</span>
                                </button>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div v-if="showEditNetworkModal" @mouseleave="nowOutside(); mode = 'network'" @mouseenter="nowInside(); mode = 'network'" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <div class="w-9/12 text-sm font-bold mb-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.members">
                            {{ errors.members }}
                        </div>
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()">
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>  
                                    </div>
                                </div>
                                <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Edit {{ networkName }}</h4>

                                <div>
                                    <Input :id="networkName" :name="networkName" :modelValue="networkName" :label="'network name'" :placeholder="'Enter network name (required)'" :type="'text'" required @update-model-value="networkName = $event" />
                                </div>

                                <div>
                                    <Input :id="networkDescription" :name="networkDescription" :modelValue="networkDescription" :label="'network description'" :placeholder="'Enter network description (required)'" :type="'text'" required @update-model-value="networkDescription = $event" />
                                </div>

                                <h4 class="text-we4vBlue font-semibold text-sm mt-4">Add/remove groups to/from {{ networkName }}<span class="text-red-600">*</span></h4>
                                <h5 class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">My groups (not currently in this network)</h5>
                                <div v-if="groupsNotInNetwork" class="flex flex-wrap max-w-full justify-between mb-2">
                                    <div v-for="(group, groupKey) in groupsNotInNetwork" :key="groupKey" class="min-w-1/3">
                                        <input :value="group.group_id" name="group" class="selected-groups rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox">
                                        <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ group.group_id }}">{{ group.group_name }}</label>
                                    </div>
                                </div>
                                <h5 v-if="networkGroups" class="text-sm font-semibold text-we4vGrey-500 my-1 tracking-tight">Uncheck a group to remove it from this network</h5>
                                <div v-if="networkGroups" class="flex flex-wrap max-w-full justify-between mb-2">
                                    <div v-for="(nGroup, nGroupKey) in networkGroups" :key="nGroupKey" class="min-w-1/3">
                                        <input :value="nGroup.group_id" name="group" class="selected-groups rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="checkbox" :checked="networkGroups.some(group => group.group_id === nGroup.group_id)">
                                        <label class="text-we4vGrey-500 text-xs ml-2 w-full text-center" for="{{ nGroup.group_id }}">{{ nGroup.group_name }}</label>
                                    </div>
                                </div>

                                <button class="text-we4vGrey-600 hover:bg-we4vGrey-100 border-we4vGrey-300 font-bold text-sm tracking-tight flex justify-center rounded-lg w-full border focus:outline-none mr-1 my-4"
                                @click="submitNetworkData()">
                                    <span>Update network</span>
                                </button>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div @mouseleave="nowOutside(); mode = 'group'" @mouseenter="nowInside(); mode = 'group'" v-if="showTransferNetworkModal" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <Form>
                            <template #form>
                                <div class="flex justify-end">
                                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                        <div @click="clearModal()" >
                                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                        </div>   
                                    </div>
                                </div>
                                <h4 class="uppercase text-we4vBlue font-semibold -mt-8">Transfer network ownership – <span class="text-we4vGrey-600">{{ networkName }}</span></h4>

                                <h4 class="text-we4vBlue font-semibold text-sm mt-4">Select an associate who will assume ownership of {{ networkName }}</h4>
                                <div class="w-full flex flex-row flex-wrap max-h-44 overflow-y-scroll items-center justify-around">
                                    <div class="lg:w-1/4 md:w-1/3 sm:w-full mr-1 mb-2" v-for="(associate, associateKey) in $page.props.myAssociates" :key="associateKey">
                                        <div class="w-full flex flex-nowrap items-center justify-evenly">
                                            <input :value="associate.user_id" name="transferToAssocs" type="radio">
                                            <label class="text-we4vGrey-500 text-xs ml-2 text-center" for="{{ associate.user_id }}">{{ associate.username }}</label>
                                            <img :src="'/'+associate.path" alt="" class="rounded-full w-8 h-8 object-cover ml-2">
                                        </div>
                                    </div>
                                </div>

                                <button-grey @click="submitTransferNetworkData()" :type="'submit'" :enabled="greyButtonEnabled">
                                    <span>Transfer network</span>
                                </button-grey>
                            </template>
                        </Form>
                    </div>
                </teleport>

                <teleport to="#groupModals">
                    <div v-if="showNetworkGroupMember" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white top-28 left-1/4 w-1/2 rounded-md p-6 max-h-600 overflow-y-scroll">
                        <div class="w-9/12 text-sm font-bold mb-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.members">{{ errors.members }}</div>

                        <div class="flex justify-end">
                            <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                                <div @click="clearModal(); showNetworkGroupMember = false">
                                    <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                                </div>  
                            </div>
                        </div>

                        <h4 class="uppercase text-we4vBlue font-semibold mb-4 -mt-8">Group details</h4>

                        <div class="flex flex-row flex-wrap" v-for="(groupDetails, memberKey) in networkGroupMember" :key="memberKey">
                            <h5 class="w-full tracking-tight font-bold">{{ groupDetails.group_name }}</h5>
                            <small class="w-full tracking-tight text-we4vGrey-500">Owner: {{ groupDetails.owner }}</small>
                            <small class="w-full tracking-tight text-we4vGrey-500">Description: {{ groupDetails.group_description }}</small>
                            <small class="w-full tracking-tight text-we4vGrey-500">Area: {{ groupDetails.geog_area }}</small>

                            <h5 class="font-semibold tracking-tight text-sm text-we4vBlue mt-3 mb-2 w-full">Group members</h5>
                            <div class="flex flex-row mb-1 items-center" v-for="(member, memberKey) in groupDetails.groupMembers" :key="memberKey">
                                <inertia-link @click="clearModal(); showNetworkGroupMember = false" class="mr-1" :href="route('user-show', member.slug)" as="button">
                                    <img :src="'/'+member.path" alt="" class="cursor-pointer rounded-full w-8 h-8 object-cover mr-1">
                                </inertia-link>
                                <small class="w-full tracking-tight text-we4vGrey-500 mr-3">{{ member.username }}<span v-if="member.admin" class="text-we4vOrange">*</span>, {{ member.role }}</small>
                            </div>

                            <h5 v-if="groupDetails.teams" class="font-semibold tracking-tight text-we4vBlue mt-3 mb-1 w-full">Teams</h5>
                            <div class="flex flex-row flex-wrap" v-for="(team, teamKey) in groupDetails.teams" :key="teamKey">
                                <h5 class="w-full tracking-tight font-semibold text-sm">{{ team.team_name }}</h5>
                                <small class="w-full tracking-tight text-we4vGrey-500">Function: {{ team.team_function }}</small>

                                <h5 class="w-full tracking-tight text-we4vBlue font-semibold text-sm mt-2">{{ team.team_name }}'s members</h5>
                                <div class="flex flex-row mb-1 mt-2 items-center" v-for="(teamMember, teamMemberKey) in team.teamMembers" :key="teamMemberKey">
                                <inertia-link @click="clearModal(); showNetworkGroupMember = false" class="mr-1" :href="route('user-show', teamMember.slug)" as="button">
                                    <img :src="'/'+teamMember.path" alt="" class="cursor-pointer rounded-full w-8 h-8 object-cover ">
                                </inertia-link>
                                <small class="w-full tracking-tight text-we4vGrey-500 mr-3">{{ teamMember.username }}<span v-if="teamMember.admin" class="text-we4vOrange">*</span>, {{ teamMember.role }} <span v-if="!teamMember.confirmed" class="font-semibold text-we4vGrey-300">(TBC)</span></small>
                            </div>
                            </div>
                        </div>
                    </div>
                </teleport>

                <!-- Main -->
                <Title>
                    <template #title>
                        Group, team and network management
                    </template>
                    <template #description>
                        <p>Group functionality lies at the heart of we4v.</p>
                        <p class="mt-2">Groups house the teams or members (associates) you add to them. Teams house only members; they cannot house groups. Teams only exist within groups. Invite associates to become members of your groups or teams as required by the complexity of your ambitions. After setting up your groups and teams, you can then create projects for them.</p>
                    </template>
                </Title>
                <button-blue :type="'submit'" @click="showGroupModal = true; showBackdrop = true; checkIfUserMaySubmit('group')">
                    Create a new group
                </button-blue>

                <Subtitle>
                    <template #title>
                        Groups (and teams) I manage
                    </template>
                    <template #description>
                        Click a group name to add a team to that group. Click the edit icon to the right of a group’s name to make changes to that group; clicking the wastebin icon will delete the group and its teams. Teams can be edited and deleted by clicking on their relevant icons.
                    </template>
                </Subtitle>

                <!-- Groups and teams -->
                <div v-if="myGroups.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Group v-for="(group, groupKey) in myGroups" :key="groupKey" :group="group" :teams="group.teams" @activate-team-modal="onActivateTeamModal" @activate-edit-group-modal="onActivateEditGroupModal" @activate-edit-team-modal="onActivateEditTeamModal" @activate-transfer-group-ownership="onActivateTransferGroupOwnership"/>
                    </div>
                </div>

                <Subtitle>
                    <template #title>
                        Groups and teams I administrate
                    </template>
                    <template #description>
                        Instructions as above, but with fewer rights than for groups you own.
                    </template>
                </Subtitle>

                <div v-if="myAdminGroups.length > 0" class="w-full m-0 m-auto">
                    <h5 class="font-medium mb-1 text-we4vGrey-600 -mt-2">Groups</h5>
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Group v-for="(adminGroup, adminGroupKey) in myAdminGroups" :key="adminGroupKey" :group="adminGroup" :teams="adminGroup.teams" @activate-team-modal="onActivateTeamModal" @activate-edit-group-modal="onActivateEditGroupModal" @activate-edit-team-modal="onActivateEditTeamModal"/>
                    </div>
                </div>

                <div v-if="myAdminTeams.length > 0" class="w-full m-0 m-auto">
                    <h5 class="font-medium mb-1 mt-3 text-we4vGrey-600">Teams</h5>
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Team v-for="(adminTeam, adminTeamKey) in myAdminTeams" :key="adminTeamKey" :team="adminTeam" :members="adminTeam.team_members" @activate-edit-team-modal="onActivateEditTeamModal"/>
                    </div>
                </div>

                <Subtitle>
                    <template #title>
                        Networks
                    </template>
                    <template #description>
                        <p>If you wish, you may affiliate groups into networks. Networks facilitate easier communication between far larger numbers of people than is possible with groups and teams alone. First, create a network; all it needs is a name and description (button below). Next, use the search tool to find a group you want to affiliate with one or more of yours (when inviting a group to join one of your networks, you are asked to select one of the networks you manage).</p>
                    </template>
                </Subtitle>

                <button-blue :type="'submit'" @click="showNetworkModal = true; showBackdrop = true; checkIfUserMaySubmit('network')">
                    Create a new network
                </button-blue>

                <Subtitle>
                    <template #title>
                        Networks I manage
                    </template>
                    <template #description>
                        Click the edit icon to the right of a network’s name to add groups and/or make changes to that network’s name and description.
                    </template>
                </Subtitle>

                <!-- Networks -->
                <div v-if="myNetworks.length > 0" class="w-full m-0 m-auto">
                    <div class="w-full m-0 flex flex-row flex-wrap justify-start">
                        <Network v-for="(network, networkKey) in myNetworks" :key="networkKey" :network="network"  @activate-edit-network-modal="onActivateEditNetworkModal" @activate-show-group-modal="onActivateShowGroupModal" @activate-transfer-network-ownership="onActivateTransferNetworkOwnership"/>
                    </div>
                </div>

            </div>
        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import ButtonGrey from '@/Jetstream/ButtonGrey'
import ButtonBlue from '@/Jetstream/ButtonBlue'
import Form from '@/Jetstream/FormSection'
import Group from './Components/Group'
import Network from './Components/Network'
import Input from './Components/Input'
import InputNoLabel from './Components/InputNoLabel'
import Team from './Components/Team'
import Modal from './Components/Modal'
import ModalBackdrop from './Components/ModalBackdrop'
import Title from '@/Jetstream/SectionTitle'
import Subtitle from '@/Jetstream/Subtitle'
import manageModals from '../Pages/Composables/manageModals'
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'

export default {
    name: 'mygroups',

    components: {
        AppLayout,
        ButtonGrey,
        ButtonBlue,
        Form,
        Input,
        InputNoLabel,
        Modal,
        ModalBackdrop,
        Group,
        Network,
        Team,
        Title,
        Subtitle,
        FlashMessage,
        ErrorMessage,
    },

    props: [
        'myGroups',
        'myAdminGroups',
        'myAdminTeams',
        'myNetworks',
        'errors',
        'user',
    ],

    setup(props) {
        const {
            amOutside, 
            amInside,
            checkIfRoleInputFieldsFilled,
            checkIfUserMaySubmit,
            clearInviteModals,
            clearModal,
            edit,
            geogArea,
            greyButtonEnabled,
            groupAdmins,
            groupDescription,
            groupId,
            groupMemberRoles,
            groupMembers,
            groupMembersEdit,
            groupName,
            groupsNotInNetwork,
            mode,
            networkName,
            networkDescription,
            networkGroups,
            networkId,
            nowInside, 
            nowOutside,
            onActivateEditGroupModal,
            onActivateEditNetworkModal,
            onActivateEditTeamModal,
            onActivateTeamModal,
            onActivateTransferGroupOwnership,
            onActivateTransferNetworkOwnership,
            onClickOutside,
            selectedAssoc,
            selectedGroupAssociates,
            selectedTeamAssociates,
            showBackdrop,
            showEditTeamModal,
            showGroupModal,
            showNetworkModal,
            showEditNetworkModal,
            showTeamModal,
            showTransferGroupModal,
            showTransferNetworkModal,
            teamAdmins,
            teamFunction,
            teamId,
            teamMemberRoles,
            teamMembers,
            teamMembersEdit,
            teamName,
            teamOwner,
            userMaySubmitForm
        } = manageModals()

        const error = ref(false)
        const flashMessage = ref(false)
        const showNetworkGroupMember = ref(null)
        const networkGroupMember = ref(null)
        const roleUserId = ref(null)

        const submitGroupData = async function () {
            let members = []
            let role
            let admin

            if (edit.value) {
                groupMembersEdit.value.forEach(groupMember => {
                    groupMember.invited
                    ? selectedGroupAssociates.value.push({
                        id: groupMember.user_id, 
                        invited: true, 
                        confirmed: groupMember.confirmed,
                        created_at: groupMember.created_at
                    })
                    : null

                    if (!groupMemberRoles.value.find(role => role.id === groupMember.user_id)) {
                        groupMemberRoles.value.push({id: groupMember.user_id, role: groupMember.role})
                    }

                    if (groupMember.admin && groupAdmins.value.includes(groupMember.user_id)) {
                        groupAdmins.value.push(groupMember.user_id)
                    }
                })
            }
            
            selectedGroupAssociates.value.forEach(assoc => {
                role = groupMemberRoles.value.filter(groupMember => groupMember.id === assoc.id)
                admin = groupAdmins.value.filter(groupAdmin => groupAdmin === assoc.id)
                admin[0] ? admin = true : admin = false
                members.push({
                    user_id: assoc.id,
                    role: role[0].role,
                    is_admin: admin,
                    invited: assoc.invited,
                    confirmed: assoc.confirmed,
                    created_at: assoc.created_at
                })
            })
            
            let payload = {
                'owner': usePage().props.value.authUser.id,
                'name': groupName.value,
                'description': groupDescription.value,
                'geog_area': geogArea.value,
                'membershipable_type': 'App\\Models\\Group',
                'members': members,
                '_token': usePage().props.value.csrf_token
            }

            edit.value ? payload.membershipable_id = groupId.value : null

            try {
                edit.value
                ? await Inertia.patch('/mygroups/update', payload)
                : await Inertia.post('/mygroups/store', payload)
                flashMessage.value = true
                props.errors = null
            } catch (err) {
                error.value = true
                props.errors  = err
            }

            clearModal()
        }

        const submitTeamData = async function () {
            let members = []
            let role
            let admin

            if (edit.value) {
                teamMembersEdit.value.forEach(teamMember => {
                    teamMember.invited
                    ? selectedTeamAssociates.value.push({
                        id: teamMember.user_id, 
                        invited: true, 
                        confirmed: teamMember.confirmed,
                        created_at: teamMember.created_at
                    })
                    : null

                    if (!teamMemberRoles.value.find(role => role.id === teamMember.user_id)) {
                        teamMemberRoles.value.push({id: teamMember.user_id, role: teamMember.role})
                    }
                    
                    if (teamMember.admin && teamAdmins.value.includes(teamMember.user_id)) {
                        teamAdmins.value.push(teamMember.user_id)
                    }
                })
            }

            selectedTeamAssociates.value.forEach(assoc => {
                role = teamMemberRoles.value.filter(teamMember => teamMember.id === assoc.id)
                admin = teamAdmins.value.filter(teamAdmin => teamAdmin === assoc.id)
                admin[0] ? admin = true : admin = false
                members.push({
                    user_id: assoc.id,
                    role: role[0].role,
                    is_admin: admin,
                    invited: assoc.invited,
                    confirmed: assoc.confirmed,
                    created_at: assoc.created_at
                })
            })

            let payload = {
                'owner': usePage().props.value.authUser.id,
                'group_id': groupId.value,
                'name': teamName.value,
                'function': teamFunction.value,
                'membershipable_type': 'App\\Models\\Team',
                'members': members,
                '_token': usePage().props.value.csrf_token
            }

            edit.value ? payload.membershipable_id = teamId.value : null

            try {
                edit.value
                ? await Inertia.patch('/myteams/update', payload)
                : await Inertia.post('/myteams/store', payload)
                flashMessage.value = true
                props.errors  = null
            } catch (err) {
                error.value = true
                props.errors  = err
            }

            clearModal()
        }

        const submitNetworkData = async function () {
            let selectedGroups = []
            let fullNetworkGroups = []
            let myVals = document.querySelectorAll('input.selected-groups')

            myVals.forEach(myVal => {
                if (myVal.checked) {
                    selectedGroups.push({ 
                        'group_id': myVal.value,
                    })
                }
            })

            selectedGroups.forEach(sGroup => {
                let nHit = networkGroups.value.find(nGroup => nGroup.group_id === sGroup.group_id)

                if (nHit) {
                    fullNetworkGroups.push({
                        'group_id': nHit.group_id,
                        'membership_created_at': nHit.membership_created_at,
                        'membership_confirmed': nHit.membership_confirmed,
                    })
                } else {
                    fullNetworkGroups.push({
                        'group_id': sGroup.group_id,
                        'membership_created_at': null,
                        'membership_confirmed': true,
                    })
                }
            })

            let payload = {
                'id': networkId.value,
                'membershipable_type': 'App\\Models\\Network',
                'name': networkName.value,
                'description': networkDescription.value,
                'groups': fullNetworkGroups
            }

            try {
                edit.value
                ? await Inertia.patch('/mynetworks/update', payload)
                : await Inertia.post('/mynetworks/store', payload)
                flashMessage.value = true
                props.errors  = null
            } catch (err) {
                error.value = true
                props.errors  = err
            }

            clearModal()
        }

        const submitTransferGroupData = async function () {
            let radios = document.getElementsByName('transferToAssocs');
            let userId
            for (var radio of radios)
            {
                if (radio.checked) {
                    userId = radio.value
                }
            }

            let payload = {
                'group_id': groupId.value,
                'user_id': userId
            }

            try {
                await Inertia.patch('/mygroups/transfer', payload)
                flashMessage.value = true
                props.errors  = null
            } catch (err) {
                error.value = true
                props.errors  = err
            }

            clearModal()
        }

        const submitTransferNetworkData = async function () {
            let radios = document.getElementsByName('transferToAssocs');
            let userId
            for (var radio of radios)
            {
                if (radio.checked) {
                    userId = radio.value
                }
            }

            let payload = {
                'network_id': networkId.value,
                'user_id': userId
            }

            try {
                await Inertia.patch('/mynetworks/transfer', payload)
                flashMessage.value = true
                props.errors  = null
            } catch (err) {
                error.value = true
                props.errors  = err
            }

            clearModal()
        }

        const onActivateShowGroupModal = async function (group) {
            await axios.get('/mygroups/show/'+group)
            .then((response) => {
                showNetworkGroupMember.value = true
                networkGroupMember.value = response.data
                showBackdrop.value = true
            }).catch((err => {
                error.value = true
                props.errors.value  = err
            })) 
        }

        const onUpdateModelValue = (data) => {
            teamName.vaue = data
        }

        const addRemoveAssociate = (mode, user_id_role) => {
            checkIfRoleInputFieldsFilled(user_id_role)
            checkIfUserMaySubmit(mode)

            selectedGroupAssociates.value = []
            selectedTeamAssociates.value = []
            let myVals
            edit.value
            ? myVals = document.querySelectorAll('input.invitedAssocsEdit')
            : myVals = document.querySelectorAll('input.invitedAssocs')

            if (mode === 'group' && !edit.value) {
                selectedGroupAssociates.value = []
                for (const myVal of myVals) {
                    if (myVal.checked) {
                        selectedGroupAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                    }
                }
            }
            if (mode === 'group' && edit.value) {
                for (const groupMember of groupMembersEdit.value) {
                    if (!groupMember.invited) {
                        for (const myVal of myVals) {
                            if (myVal.checked && myVal.value === groupMember.user_id) {
                                selectedGroupAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                            }
                        }
                    }
                }
            }

            if (mode === 'team' && !edit.value) {
                for (const myVal of myVals) {
                    if (myVal.checked) {
                        selectedTeamAssociates.value.push({id: myVal.value, invited: false, confirmed: false})
                    }
                }
            }
            if (mode === 'team' && edit.value) {
                for (const teamMember of teamMembersEdit.value) {
                    if (!teamMember.invited) {
                        for (const myVal of myVals) {
                            if (myVal.checked && myVal.value === teamMember.user_id) {
                                selectedTeamAssociates.value.push({
                                    id: myVal.value, 
                                    invited: false, 
                                    confirmed: false
                                })
                            }
                        }
                    }
                }
            }
        }

        const makeAdmin = (mode) => {
            groupAdmins.value = []
            teamAdmins.value = []
            let myVals = []

            edit.value
            ? myVals = document.querySelectorAll('input.adminsEdit')
            : myVals = document.querySelectorAll('input.admins')

            myVals.forEach(myVal => {
                if (mode === 'group') {
                    myVal.checked ? groupAdmins.value.push(myVal.value) : null
                }
                if (mode === 'team') {
                    myVal.checked ? teamAdmins.value.push(myVal.value) : null
                }
            })
        }

        const onCollectMemberRoles = (e) => {
            selectedAssoc.value = !selectedAssoc.value
            roleUserId.value = e.id

            checkIfRoleInputFieldsFilled(e.id)

            if (mode.value === 'group') {
                groupMemberRoles.value.push({id: e.id, role: e.value})
            }

            if (mode.value === 'team') {
                if (teamMemberRoles.value.length > 0) {
                    teamMemberRoles.value.forEach(member => {
                        e.id !== member.id
                        ? teamMemberRoles.value.push({id: e.id, role: e.value})
                        : null
                    })
                } else {
                    teamMemberRoles.value.push({id: e.id, role: e.value})
                }
            }
        }

        watch(amOutside, () => {
            if (amOutside.value && !amInside.value) {
                document.body.addEventListener('click', onClickOutside, true)
            }
        })

        watch(mode, () => {
            makeAdmin(mode.value) // Must be executed at least once; admin roles might be left unedited
        })

        watch(edit, () => {
            if (groupMembers.value.length > 0) {
                groupMembers.value.forEach(groupMember => {
                    !groupMember.declined ? groupMembersEdit.value.push(groupMember) : null
                })
                usePage().props.value.myAssociates.forEach(associate => {
                    if (!groupMembersEdit.value.some(groupMemberEdit => groupMemberEdit.user_id === associate.user_id)) {
                        associate.invited = false
                        associate.confirmed = false
                        groupMembersEdit.value.push(associate)
                    }
                })
            } else {
                groupMembersEdit.value = usePage().props.value.myAssociates
            }

            if (teamMembers.value.length) {
                teamMembers.value.forEach(teamMember => {
                    !teamMember.declined ? teamMembersEdit.value.push(teamMember) : null
                })
                usePage().props.value.myAssociates.forEach(associate => {
                    if (!teamMembersEdit.value.some(teamMemberEdit => teamMemberEdit.user_id === associate.user_id)) {
                        associate.invited = false
                        associate.confirmed = false
                        teamOwner.value !== associate.username
                        ? teamMembersEdit.value.push(associate) : null
                        
                    }
                })
            }
        })

        watch(selectedAssoc, () => {
            // Each time an assoc is clicked, check if its role field is empty, change border color accordingly.
            let elem = document.getElementById(roleUserId.value)
            let cb = document.getElementById('checkbox_'+roleUserId.value)
            
            cb.checked && elem.value
            ? elem.style.border = 'solid 1px rgb(81, 168, 186)'
            : elem.style.border = 'solid 1px rgb(220, 38, 38)'

            !cb.checked && !elem.value
            ? elem.style.border = 'solid 0px white'
            : null

        })

        watch(error, () => {
            setTimeout(() => { 
                usePage().props.value.errors = {} 
                error.value = false 
            }, 2500)
        })

        watch(flashMessage, () => {
            setTimeout(() => {
                usePage().props.value.jetstream.flash.message = ''
            }, 2500 )
        })

        return {
            addRemoveAssociate,
            amOutside,
            amInside,
            checkIfRoleInputFieldsFilled,
            checkIfUserMaySubmit,
            clearInviteModals,
            clearModal,
            onCollectMemberRoles,
            edit,
            geogArea,
            greyButtonEnabled,
            groupAdmins,
            groupDescription,
            groupMembers,
            groupMembersEdit,
            groupId,
            groupName,
            groupMemberRoles,
            groupsNotInNetwork,
            makeAdmin,
            mode,
            networkName,
            networkDescription,
            networkGroups,
            networkGroupMember,
            networkId,
            nowInside, 
            nowOutside,
            onActivateEditGroupModal,
            onActivateEditNetworkModal,
            onActivateEditTeamModal,
            onActivateShowGroupModal,
            onActivateTeamModal,
            onActivateTransferGroupOwnership,
            onActivateTransferNetworkOwnership,
            onClickOutside,
            onUpdateModelValue,
            roleUserId,
            selectedAssoc,
            selectedGroupAssociates,
            selectedTeamAssociates,
            showBackdrop,
            showEditTeamModal,
            showGroupModal,
            showNetworkGroupMember,
            showNetworkModal,
            showEditNetworkModal,
            showTeamModal,
            showTransferGroupModal,
            showTransferNetworkModal,
            submitGroupData,
            submitNetworkData,
            submitTeamData,
            submitTransferGroupData,
            submitTransferNetworkData,
            teamAdmins,
            teamFunction,
            teamId,
            teamMembers,
            teamMembersEdit,
            teamMemberRoles,
            teamName,
            teamOwner,
            userMaySubmitForm,
        }
    }
}
</script>