<template>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight">
                <Title>
                    <template #title>
                        <div class="flex items-center justify-start">
                            <div class="pt-2">
                                Welcome to
                            </div>
                            <div class="ml-1">
                                <jet-application-mark class="block h-8 w-auto" />
                            </div>
                        </div>
                    </template>

                    <template #description>
                        <p class="text-we4vGrey-600">we4v is a social-governance and direct-democracy (micro-democracy) online software that constrains power to the local level, while enabling only information-sharing and administrative functionality to networked groups and teams spanning geographic regions larger than the local.</p>
                        <p class="text-we4vGrey-600">The process is to associate with other we4v users, create groups of associates as required, divide those groups into teams as required, and assign projects and tasks – tasks belong in projects – to those groups and teams. Groups can be affiliated into networks. Vote functionality is also available to facilitate micro-democratic decision making where necessary.</p>
                        <p class="text-we4vGrey-600">we4v also includes article composition. Articles can be tagged for easy searching and referencing. After completing a project, a team might, for example, write an article detailing their succcesses and failures.</p>
                        <p class="text-we4vGrey-700 italic text-xs">This software is currently in beta. When all bugs are ironed out and full functionality finished, there will be a £1/month charge to use we4v. we4v does not use cookies beyond the barest minimum: a session, a cross-site and a storage cookie.</p>
                    </template>
                </Title>

                <Subtitle>
                    <template #title>
                        Everything you need to know (almost)
                    </template>
                </Subtitle>

                <div>
                    <h4 class="text-blue-600">1. Talkboard</h4>
                    <p class="text-we4vGrey-600">Much like Facebook’s wall, the Talkboard is for sharing chat-sized posts, with optional images, which are visible to all a user’s associates. Coming soon is a group/team filter, such that a post can be made visible only to members of a group or team.</p>
                </div>

                <div>
                    <h4 class="text-blue-600">2. Groups</h4>
                    <p class="text-we4vGrey-600">we4v emphasises the local. The idea is to assist, <strong>not</strong> replace, social action, civic endeavour, democratic activism, etc. This means the software anticipates that groups are formed between associates who have come to know and trust each other sufficiently. This is not a requirement, of course, but the software has been designed around this core idea.</p>
                    <p class="text-we4vGrey-600">A group’s direct members do not represent, necessarily, all members of that group. If a group is large and needs to be subdivided into teams, that group’s members include all team members. Hence, a group’s direct members might consist of only an administrator and secretary, while the teams host all the other members in their various roles. Teams may also have administrators.</p>
                    <p class="text-we4vGrey-600">When a user creates a group and adds associates to it, those associates will see the grey bell by “Group/team invitations” turn orange. This indicates there are one or more invitations to join a group or team. In other words, users cannot simply join a group or team, they are invited. This means members of groups and teams will likely get to know each other first.</p>
                    <p class="text-we4vGrey-600">Groups can be affiliated into a network. This functionality will remain largely cosmetic until the PM (Pigeon Mail) system is set up. Coming soon...</p>
                </div>

                <div>
                    <h4 class="text-blue-600">3. Projects</h4>
                    <p class="text-we4vGrey-600">Projects are assigned to groups or teams. The tasks that a project is composed of are assigned to a group member, a whole team, or one or more members of a team. Both projects and tasks require start and end dates. Notes can be recorded in both projects and tasks, by way of logging project and task activity, success and lessons learned. Completed tasks can be viewed in report form, including all their notes, and reopened if required.</p>
                </div>

                <div>
                    <h4 class="text-blue-600">4. Articles</h4>
                    <p class="text-we4vGrey-600">Articles don’t really need any explanation, except perhaps that a list of each user’s articles is auto-generated for their “Self” page. <span class="italic">(This page is not complete; images cannot currently be added to articles. Coming soon...)</span></p>
                </div>

                <div>
                    <h4 class="text-blue-600">5. Votes</h4>
                    <p class="text-we4vGrey-600">Where micro-democracy happens. Votes are assigned to groups or teams. If a group is selected, all members of the group and its teams are invited to vote. When a vote is created, each relevant associate receives an invitation – indicated by the bell next to Pending Votes turning orange – to take part.</p>
                </div>

                <div>
                    <h4 class="text-blue-600">6. Self</h4>
                    <p class="text-we4vGrey-600">An auto-generated page that displays a user’s articles and posts.</p>
                </div>
            </div>

        </template>
    </app-layout>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import AppLayout from '@/Layouts/AppLayout'
import Title from '@/Jetstream/SectionTitle'
import Subtitle from '@/Jetstream/Subtitle'

export default {
    name: 'About',

    components: {
        AppLayout,
        Title,
        Subtitle,
        JetApplicationMark,
    }
    
}
</script>