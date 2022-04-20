<template>
    <search-modal-backdrop :show="showGroup"></search-modal-backdrop>

    <teleport to="#pendingNetworkModals">
        <Modal :show="showGroup">
            <div v-if="showGroup" @mouseleave="nowOutside()" @mouseenter="nowInside()" class="z-50 fixed bg-white opacity-100 text-we4vGrey-700 top-32 left-1/4 w-1/2 m-auto rounded-md p-6">
                <div class="flex justify-end">
                    <div class="w-8 h-8 relative -top-2 -mr-2 rounded-full cursor-pointer">
                        <div @click="showGroup = false; clearModal()">
                            <i class="fas fa-skull-crossbones animate-pulse z-50 cursor-pointer text-lg text-we4vDarkBlue"></i>
                        </div>   
                    </div>
                </div>

                <h1 class="text-we4vBlue tracking-tight font-light text-3xl mb-2 -mt-8 pr-10">{{ groupName }}</h1>

                <p class="text-we4vGrey-500 text-sm tracking-tight">Description: <span class="text-we4vGrey-700">{{ groupDescription }}</span></p>
                <p class="text-we4vGrey-500 text-sm tracking-tight">Geographical area of operations: <span v-if="geogArea" class="text-we4vGrey-700">{{ geogArea }}</span><span v-else class="italic text-we4vGrey-300">No area specified for this group</span></p>
                <p class="text-we4vGrey-500 text-sm tracking-tight">Owner: <span class="text-we4vGrey-700">{{ groupOwner }}</span></p>

                <h5 class="font-semibold text-we4vGrey-600 mt-4 mb-2 tracking-tight">Invite <span class="text-we4vBlue">{{ groupName }}</span> to join one of your networks</h5>
                <div class="flex flex-wrap max-w-full justify-between mb-2">
                    <div v-for="(network, networkKey) in $page.props.myNetworks" :key="networkKey" class="min-w-1/3">
                        <input :value="network.network_id" name="network" id="network" class="rounded-sm border-indigo-100 shadow-sm text-indigo-600 focus:outline-none" type="radio">
                        <label class="text-we4vGrey-600 text-xs ml-2 w-full text-center" for="{{ network.network_id }}">{{ network.network_name }}</label>
                    </div>
                </div>
                
                <button-grey @click="submitNetworkInvite()">
                    Send request
                </button-grey>
            </div>
        </Modal>
    </teleport>

    <div class="bg-we4vGrey-700 fixed top-20 left-2 w-searchTabWidth rounded-md p-1">
        <div class="flex flex-wrap w-full p-0">
            <input type="radio" name="searchTabs" value="users" class="hidden" checked="checked">
            <label id="label-users" @click="handleClick('users')" for="users" class="px-4 py-3 rounded-t-md tracking-tight text-xs bg-we4vGrey-800 text-we4vGrey-300 cursor-pointer">Users</label>
            <div id="users" class="order-1 w-full bg-we4vBg pt-4 px-2 rounded-tl-none rounded-tr-md rounded-bl-md rounded-br-md hidden">
                <Input id="input-users" :modelValue="usersSearchString" @keyup.enter="fetchResults('users')" :name="name" :label="label" :placeholder="placeholder" @update-model-value="usersSearchString = $event"/>
            </div>

            <input type="radio" name="searchTabs" value="groups" class="hidden">
            <label id="label-groups" @click="handleClick('groups')" for="groups" class="px-4 py-3 rounded-t-md tracking-tight text-xs bg-we4vGrey-800 text-we4vGrey-300 cursor-pointer">Groups</label>
            <div id="groups" class="order-1 w-full bg-we4vBg pt-4 px-2 rounded-tl-none rounded-tr-md rounded-bl-md rounded-br-md hidden">
                <Input id="input-groups" :modelValue="groupsSearchString" @keyup.enter="fetchResults('groups')" :name="name" :label="label" :placeholder="placeholder" @update-model-value="groupsSearchString = $event"/>
            </div>

            <input type="radio" name="searchTabs" value="articles" class="hidden">
            <label id="label-articles" @click="handleClick('articles')" for="articles" class="px-4 py-3 rounded-t-md tracking-tight text-xs bg-we4vGrey-800 text-we4vGrey-300 cursor-pointer">Articles</label>
            <div id="articles" class="order-1 w-full bg-we4vBg pt-4 px-2 rounded-tl-none rounded-tr-md rounded-bl-md rounded-br-md hidden">
                <Input id="input-articles" :modelValue="articlesSearchString" @keyup.enter="fetchResults('articles')" :name="name" :label="label" :placeholder="placeholder" @update-model-value="articlesSearchString = $event"/>
            </div>
        </div>

        <div v-if="results.length > 0" class="mt-3 bg-we4vBg p-1 rounded-md text-xs text-we4vGrey-700 max-h-80 overflow-y-scroll">
            <div class="flex justify-between mt-1">
                <h5 class="text-we4vBlue font-semibold text-sm tracking-tight pl-1">Search results</h5>
                <div class="w-8 h-8 cursor-pointer">
                    <div @click="results = []">
                        <i class="fas fa-skull-crossbones animate-pulse z-50 text-sm text-right pl-3 text-we4vDarkBlue"></i>
                    </div>   
                </div>
            </div>
            <div v-for="(result, resultKey) in results" :key="resultKey">
                <div class="flex flex-row flex-nowrap w-full tracking-tight hover:bg-we4vGrey-200 py-2 px-1 items-center max-h-96 overflow-y-scroll rounded-sm">
                    <!-- Groups and users -->
                    <div @click="showGroup = true; activateShowGroupModal(result)" v-if="result.description" class="w-1/3 font-medium cursor-pointer hover:text-we4vBlue">{{ result.name }}</div>
                    <div v-if="result.surname" class="text-xs w-5/12 font-medium pl-1">{{ result.name }} {{ result.surname }}</div>
                    <div v-if="result.username" class="w-5/12 italic">{{ result.username }}</div>
                    <div v-if="result.username" class="w-1/6">
                        <inertia-link :href="route('user-show', result.slug)" as="button">
                            <img v-if="result.path" :src="'/storage/'+result.path" alt="" class="rounded-full w-7 h-7 object-cover ml-4">
                            <img v-if="!result.path" :src="'/stoage/images/nobody.png'" alt="" class="rounded-full w-7 h-7 object-cover ml-4">
                        </inertia-link>
                    </div>
                    <div v-if="result.geog_area" class="w-3/5 pl-1 italic">{{ result.geog_area }}</div>

                    <!-- Articles -->
                    <div v-if="result.title" class="w-1/3">
                        <inertia-link :href="route('article-show', result.slug)" as="button" class="text-left  font-medium text-xs hover:text-we4vBlue">
                            {{ result.title }}
                        </inertia-link>
                    </div>
                    <div v-if="result.synopsis" class="w-1/3 font-medium text-xs italic">{{ result.synopsis }}</div>
                    <div v-if="result.tags > 0" class="w-1/3 text-xs text-we4vGrey-500">
                        <div v-for="(tag, tagKey) in result.tags" :key="tagKey">
                            #{{ tag.tag }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="emptySetReturned" class="mt-3 bg-we4vBg p-1 rounded-md">
            <h5 class="text-red-700 font-semibold text-sm tracking-tight mb-3">Nothing found!</h5>
            <p class="text-xs text-we4vGrey-600">
                No results returned from that search string.
            </p>
        </div>
    </div>
</template>

<script>
import Input from './Input'
import ButtonGrey from '../../Jetstream/ButtonGrey'
import Modal from './Modal'
import SearchModalBackdrop from './SearchModalBackdrop'
import manageModals from '../Composables/manageModals'
import { ref, watch, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'SearchTabs',

    components: {
        Input,
        ButtonGrey,
        Modal,
        SearchModalBackdrop,
    },

    props: [
        'errors'
    ],

    setup(props) {
        const {
            activateShowGroupModal,
            amOutside, 
            amInside,
            clearModal,
            edit,
            groupId,
            groupName,
            groupDescription,
            groupOwner,
            geogArea,
            mode,
            nowInside, 
            nowOutside,
            onClickOutside,
            showBackdrop,
            showGroup
        } = manageModals()

        const selectedTab = ref(null)
        const name = ref('users')
        const label = ref('search users')
        const placeholder = ref('enter search string')
        const usersSearchString = ref(null)
        const groupsSearchString = ref(null)
        const articlesSearchString = ref(null)
        const results = ref([])
        const emptySetReturned = ref(false)
        const error = ref(false)
        const flashMessage = ref(false)

        onMounted(() => {
            setTimeout(() => {
                let searchTabId = document.querySelector("input[type=radio]:checked").value

                document.getElementById(searchTabId).classList.remove('hidden')
                document.getElementById('label-users').classList.remove('bg-we4vGrey-800')

                document.getElementById(searchTabId).classList.add('block')
                document.getElementById(searchTabId).classList.add('aktiv')
                document.getElementById('label-users').classList.add('aktiv-label')
                document.getElementById('label-users').classList.add('bg-we4vBg')
                document.getElementById('label-users').classList.add('text-we4vGrey-800')
                document.getElementById('label-users').classList.add('font-semibold')
            }, 100)
        })

        const handleClick = (tab) => {
            selectedTab.value = tab
            emptySetReturned.value = false
            results.value = []
            name.value = tab
            label.value = 'search '+tab
            tab === 'articles'
            ? placeholder.value = 'enter article tag'
            : placeholder.value = 'enter search string'

            document.getElementsByClassName('aktiv')[0].classList.remove('block')
            document.getElementsByClassName('aktiv')[0].classList.add('hidden')
            document.getElementsByClassName('aktiv')[0].classList.remove('aktiv')

            document.getElementsByClassName('aktiv-label')[0].classList.remove('bg-we4vBg')
            document.getElementsByClassName('aktiv-label')[0].classList.remove('text-we4vGrey-800')
            document.getElementsByClassName('aktiv-label')[0].classList.add('text-we4vGrey-300')
            document.getElementsByClassName('aktiv-label')[0].classList.add('bg-we4vGrey-800')
            document.getElementsByClassName('aktiv-label')[0].classList.remove('aktiv-label')
            
            document.getElementById(tab).classList.remove('hidden')
            document.getElementById('label-'+tab).classList.remove('bg-we4vGrey-800')

            document.getElementById(tab).classList.add('block')
            document.getElementById(tab).classList.add('aktiv')
            document.getElementById('label-'+tab).classList.add('aktiv-label')
            document.getElementById('label-'+tab).classList.add('bg-we4vBg')
            document.getElementById('label-'+tab).classList.add('text-we4vGrey-800')
            document.getElementById('label-'+tab).classList.add('font-semibold')
        }

        const submitNetworkInvite = async function () {
            let selectedNetwork

            if (document.querySelector('input[id="network"]:checked')) {
                selectedNetwork = document.querySelector('input[id="network"]:checked').value
            }

            let payload = {
                'membershipable_id': selectedNetwork,
                'membershipable_type': 'App\\Models\\Network',
                'group_id': groupId.value,
                'user_id': null,
                'admin': false,
                'role': null
            }
            
            try {
                await Inertia.post('/memberships/store', payload)
                flashMessage.value = true
                props.errors = null
            } catch (err) {
                error.value = true
                props.errors = err
            }

            clearModal()
        }

        const fetchResults = async function (db) {
            if (db === 'users') {
                try {
                    await Inertia.post('/users/search', 
                        { 'searchString': usersSearchString.value },
                    )
                    
                    results.value = usePage().props.value.searchResults.searchData
                    results.value.length === 0
                    ? emptySetReturned.value = true
                    : emptySetReturned.value = false

                    props.errors = null
                } catch (err) {
                    props.errors = err
                }
            }

            if (db === 'groups') {
                try {
                    await Inertia.post('/groups/search', 
                        { 'searchString': groupsSearchString.value }
                    )

                    results.value = usePage().props.value.searchResults.searchData
                    results.value.length === 0
                    ? emptySetReturned.value = true
                    : emptySetReturned.value = false

                    props.errors = null
                } catch (err) {
                    props.errors = err
                }
            }

            if (db === 'articles') {
                try {
                    await Inertia.post('/articles/search', 
                        { 'searchString': articlesSearchString.value }
                    )

                    results.value = usePage().props.value.searchResults.searchData
                    results.value.length === 0
                    ? emptySetReturned.value = true
                    : emptySetReturned.value = false

                    props.errors = null
                } catch (err) {
                    props.errors = err
                }
            }
        }

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
            activateShowGroupModal,
            amOutside, 
            amInside,
            clearModal,
            edit,
            groupId,
            groupName,
            groupDescription,
            groupOwner,
            geogArea,
            mode,
            nowInside, 
            nowOutside,
            onClickOutside,
            showBackdrop,
            selectedTab,
            name,
            label,
            placeholder,
            handleClick,
            fetchResults,
            usersSearchString,
            groupsSearchString,
            articlesSearchString,
            results,
            emptySetReturned,
            showGroup,
            submitNetworkInvite,
            error,
            flashMessage,
        }
    },
}
</script>