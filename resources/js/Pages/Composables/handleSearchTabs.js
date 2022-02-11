import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const errors = ref(null)
const searchLabel = ref('search users')
const searchName = ref('users')
const searchPlaceholder = ref('enter search string')
const articlesSearchString = ref(null)
const groupsSearchString = ref(null)
const usersSearchString = ref(null)

const manageSearchTabsCSSOnMounted = () => {
    let searchTabId = document.querySelector("input[type=radio]:checked").value

    document.getElementById(searchTabId).classList.remove('hidden')
    document.getElementById('label-users').classList.remove('bg-we4vGrey-800')

    document.getElementById(searchTabId).classList.add('block')
    document.getElementById(searchTabId).classList.add('aktiv')
    document.getElementById('label-users').classList.add('aktiv-label')
    document.getElementById('label-users').classList.add('bg-we4vBg')
    document.getElementById('label-users').classList.add('text-we4vGrey-800')
    document.getElementById('label-users').classList.add('font-semibold')
}

const handleSearchTabsClick = (tab) => {
    searchName.value = tab
    searchLabel.value = 'search '+tab
    tab === 'articles'
    ? searchPlaceholder.value = 'enter article tag(s)'
    : searchPlaceholder.value = 'enter search string'

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

const fetchSearchResults = function (db) {
    if (db === 'users') {
        console.log('fetchSearchResults fired for db '+db)
        try {
            Inertia.post('/users/search', 
                { 'searchString': usersSearchString.value },
            )
            errors.value = null
        } catch (err) {
            errors.value = err
        }
    }

    if (db === 'groups') {
        try {
            Inertia.post('/groups/search', groupsSearchString.value)
            errors.value = null
        } catch (err) {
            errors.value = err
        }
    }

    if (db === 'articles') {
        try {
            Inertia.post('/articles/search', articlesSearchString.value)
            errors.value = null
        } catch (err) {
            errors.value = err
        }
    }
}

const handleSearchTabs = () => {
    return {
        articlesSearchString,
        errors,
        fetchSearchResults,
        groupsSearchString,
        handleSearchTabsClick,
        manageSearchTabsCSSOnMounted,
        searchLabel,
        searchName,
        searchPlaceholder,
        usersSearchString,
    }
}

export default handleSearchTabs