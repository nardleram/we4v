<template>
    <div class="flex flex-col flex-1 bg-we4vBg min-h-screen overflow-y-hidden" >
        
        <nav class="bg-we4vGrey-900 border-b border-we4vGrey-700 shadow-md fixed top-0 left-0 w-screen z-50">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto md:px-6 sm:px-1 flex w-full justify-between h-16">
                <div class="flex w-full">
                    <!-- Logo -->
                    <div class="w-1/4 flex">
                        <div class="flex-shrink-0 flex items-center">
                            <inertia-link :href="route('talkboard')">
                                <jet-application-mark class="block h-9 w-auto" />
                            </inertia-link>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="w-1/2 flex justify-around items-center text-center uppercase font-light">
                        <div>
                            <jet-nav-link :href="route('talkboard')" :active="route().current('talkboard')">
                                Talkboard
                            </jet-nav-link>
                        </div>
                        <div>
                            <jet-nav-link :href="route('myarticles', $page.props.authUser.id)" :active="route().current('myarticles')">
                                Articles
                            </jet-nav-link>
                        </div>
                        <div>
                            <jet-nav-link :href="route('mygroups', $page.props.authUser.id)" :active="route().current('mygroups')">
                                Groups
                            </jet-nav-link>
                        </div>
                        <div>
                            <jet-nav-link :href="route('myprojects', $page.props.authUser.id)" :active="route().current('myprojects')">
                                Projects
                            </jet-nav-link>
                        </div>
                        <div>
                            <jet-nav-link href="#">
                                Votes
                            </jet-nav-link>
                        </div>
                        <div>
                            <jet-nav-link :href="route('user-posts', $page.props.authUser.username)" :active="route().current('user-posts')">
                                Self
                            </jet-nav-link>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="ml-3 mt-4 text-we4vGrey-800 relative w-1/4 flex justify-end">
                        <jet-dropdown align="right" width="48">
                            <template #mail>
                                <img class="h-8 rounded-none pt-1 mr-2 cursor-pointer" :src="'/images/pigeon.svg'" alt="pmail" />
                            </template>

                            <template #trigger>
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-we4vGrey-300 transition duration-150 ease-in-out">
                                    <img class="h-8 w-8 rounded-full object-cover" :src="'/'+$page.props.userProfileImages.profile" :alt="$page.props.user.name" />
                                </button>
                            </template>

                            <template #content>
                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-we4vGrey-400">
                                    Manage Account
                                </div>

                                <jet-dropdown-link :href="route('myprofile', $page.props.authUser.id)">
                                    Profile
                                </jet-dropdown-link>

                                <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                    API Tokens
                                </jet-dropdown-link>

                                <div class="border-t border-we4vGrey-100"></div>

                                <!-- Authentication -->
                                <form @submit.prevent="logout">
                                    <jet-dropdown-link as="button">
                                        Logout
                                    </jet-dropdown-link>
                                </form>
                            </template>
                        </jet-dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-we4vGrey-400 hover:text-we4vGrey-500 hover:bg-we4vGrey-100 focus:outline-none focus:bg-we4vGrey-100 focus:text-we4vGrey-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <jet-responsive-nav-link :href="route('talkboard')" :active="route().current('talkboard')">
                        Talkboard
                    </jet-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-we4vGrey-200">
                    <div class="flex items-center px-4">
                        <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex-shrink-0 mr-3" >
                            <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.userProfileImages.profile" :alt="$page.props.user.name" />
                        </div>

                        <div>
                            <div class="font-medium text-base text-we4vGrey-800">{{ $page.props.user.name }}</div>
                            <div class="font-medium text-sm text-we4vGrey-500">{{ $page.props.user.email }}</div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                            Profile
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                            API Tokens
                        </jet-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <jet-responsive-nav-link as="button">
                                Logout
                            </jet-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex flex-1 overflow-y-hidden pt-16">
            <sidebar-left />
            <slot name="centre"></slot>
            <sidebar-right />
        </main>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>
    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetBanner from '@/Jetstream/Banner'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
    import SidebarLeft from '@/Jetstream/SidebarLeft'
    import SidebarRight from '@/Jetstream/SidebarRight'

    export default {
        components: {
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            SidebarLeft,
            SidebarRight,
        },

        data() {
            return {
                showingNavigationDropdown: false,
            }
        },

        methods: {
            logout() {
                this.$inertia.post(route('logout'));
            },
        }
    }
</script>
