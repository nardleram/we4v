<template>
    <div class="bg-we4vGrey-700 absolute top-20 left-2 w-searchTabWidth rounded-md p-1">
        <div class="flex flex-wrap w-full p-0">
            <input type="radio" name="searchTabs" value="users" class="hidden" checked="checked">
            <label id="label-users" @click="handleClick('users')" for="users" class="px-4 py-3 rounded-t-md tracking-tight text-xs bg-we4vGrey-800 text-we4vGrey-300 cursor-pointer">Users</label>
            <div id="users" class="order-1 w-full bg-we4vBg pt-4 px-2 rounded-tl-none rounded-tr-md rounded-bl-md rounded-br-md hidden">
                <Input id="input-users" v-model="usersSearchString" @keyup.enter="fetchResults('users')" :name="name" :label="label" :placeholder="placeholder" />
            </div>

            <input type="radio" name="searchTabs" value="groups" class="hidden">
            <label id="label-groups" @click="handleClick('groups')" for="groups" class="px-4 py-3 rounded-t-md tracking-tight text-xs bg-we4vGrey-800 text-we4vGrey-300 cursor-pointer">Groups</label>
            <div id="groups" class="order-1 w-full bg-we4vBg pt-4 px-2 rounded-tl-none rounded-tr-md rounded-bl-md rounded-br-md hidden">
                <Input id="input-groups" v-model="groupsSearchString" @keyup.enter="fetchResults('groups')" :name="name" :label="label" :placeholder="placeholder" />
            </div>

            <input type="radio" name="searchTabs" value="articles" class="hidden">
            <label id="label-articles" @click="handleClick('articles')" for="articles" class="px-4 py-3 rounded-t-md tracking-tight text-xs bg-we4vGrey-800 text-we4vGrey-300 cursor-pointer">Articles</label>
            <div id="articles" class="order-1 w-full bg-we4vBg pt-4 px-2 rounded-tl-none rounded-tr-md rounded-bl-md rounded-br-md hidden">
                <Input id="input-articles" v-model="articlesSearchString" @keyup.enter="fetchResults('articles')" :name="name" :label="label" :placeholder="placeholder" />
            </div>
        </div>

        <div v-if="results.length > 0" class="mt-3 bg-we4vBg p-1 rounded-md text-xs text-we4vGrey-700 max-h-80 overflow-y-scroll">
            <h5 class="text-we4vBlue font-semibold text-sm tracking-tight mb-3">Search results</h5>
            <div v-for="(result, resultKey) in results" :key="resultKey">
                <div class="flex flex-row flex-nowrap w-full tracking-tight hover:bg-we4vGrey-200 py-2 items-center max-h-96 overflow-y-scroll rounded-sm">
                    <div v-if="result.description" class="w-1/3 font-medium">{{ result.name }}</div>
                    <div v-if="result.surname" class="text-xs w-1/3 font-medium pl-1">{{ result.name }} {{ result.surname }}</div>
                    <div v-if="result.username" class="w-1/3 italic">{{ result.username }}</div>
                    <div v-if="result.username" class="w-1/3">
                        <inertia-link :href="route('user-show', result.username)" as="button">
                            <img v-if="result.path" :src="'/'+result.path" alt="" class="rounded-full w-7 h-7 object-cover ml-6">
                            <img v-if="!result.path" :src="'/images/nobody.png'" alt="" class="rounded-full w-7 h-7 object-cover ml-6">
                        </inertia-link>
                    </div>
                    <div v-if="result.geog_area" class="w-3/5 pl-1 italic">{{ result.geog_area }}</div>
                </div>
            </div>
        </div>

        <div v-if="emptySetReturned" class="mt-3 bg-we4vBg p-1 rounded-md">
            <h5 class="text-red-700 font-semibold text-sm tracking-tight mb-3">Nothing found!</h5>
            <p class="text-xs text-we4vGrey-700">
                Sorry. No results returned from that search string.
            </p>
        </div>
    </div>
</template>

<script>
import Input from './Input'
import { ref, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

export default {
    name: 'SearchTabs',

    components: {
        Input
    },

    props: [
        'errors'
    ],

    setup(props) {
        const selectedTab = ref(null)
        const name = ref('users')
        const label = ref('search users')
        const placeholder = ref('enter search string')
        const usersSearchString = ref(null)
        const groupsSearchString = ref(null)
        const articlesSearchString = ref(null)
        const results = ref([])
        const emptySetReturned = ref(false)

        onMounted(() => {
            let searchTabId = document.querySelector("input[type=radio]:checked").value

            document.getElementById(searchTabId).classList.remove('hidden')
            document.getElementById('label-users').classList.remove('bg-we4vGrey-800')

            document.getElementById(searchTabId).classList.add('block')
            document.getElementById(searchTabId).classList.add('aktiv')
            document.getElementById('label-users').classList.add('aktiv-label')
            document.getElementById('label-users').classList.add('bg-we4vBg')
            document.getElementById('label-users').classList.add('text-we4vGrey-800')
            document.getElementById('label-users').classList.add('font-semibold')
        })

        const handleClick = (tab) => {
            selectedTab.value = tab
            emptySetReturned.value = false
            results.value = []
            name.value = tab
            label.value = 'search '+tab
            tab === 'articles'
            ? placeholder.value = 'enter article tag(s)'
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

        return {
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
        }
    },
}
</script>