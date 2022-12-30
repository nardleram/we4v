<template>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight bg-we4vGrey-900">
                <teleport to="#pmailModal">
                    <div v-if="showPmailModal" class="bg-we4vGrey-900 border border-we4vBlue z-50 fixed top-48 left-1/4 ml-1/20 w-pmailWidth rounded-md py-3 px-6 max-h-600 overflow-y-scroll">
                        <div class="flex items-start justify-between">
                            <h5 class="font-semibold tracking-tight text-we4vBlue text-sm mb-3">New message</h5>
                            <h5 @click="showPmailModal = false" class="font-bold tracking-tight text-we4vGrey-600 text-sm mb-3 cursor-pointer hover:text-we4vGrey-700">X</h5>
                        </div>
                        <Form>
                            <template #form>
                                <div id="messageDetails" class="flex flex-col items-start">
                                    <div class="flex items-start justify-between w-full mb-2">
                                        <p class="text-we4vGrey-500 font-semibold tracking-tight">From:</p>
                                        <div class="w-10/12 h-7 text-we4vGrey-300 bg-we4vGrey-800 text-xs font-semibold tracking-tight rounded-md pl-2 pt-1">{{ $page.props.authUser.slug }}@we4v</div>
                                    </div>

                                    <div class="flex items-start justify-between w-full mb-2">
                                        <p class="text-we4vGrey-500 font-semibold tracking-tight">To:</p>
                                        <div>
                                            <input @keydown.enter.prevent="addAddressee" v-model="addressee" class="w-10/12 pl-2 text-we4vGrey-300 bg-we4vGrey-800 h-7 rounded-md text-xs tracking-tight font-semibold" type="text">
                                            <div v-if="showAddresseeList">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start justify-between w-full mb-2">
                                        <p class="text-we4vGrey-500 font-semibold tracking-tight">Subject:</p>
                                        <input v-model="subject" class="w-10/12 pl-2 text-we4vGrey-300 bg-we4vGrey-800 h-7 rounded-md text-xs tracking-tight font-semibold" type="text">
                                    </div>

                                </div>
                                
                            </template>
                        </Form>
                    </div>
                </teleport>
                <Title>
                    <template #title>
                        <div class="flex flex-col w-full items-start justify-around">
                            <div class="pt-2 flex">
                                <img class="h-8 rounded-none mr-3 cursor-pointer" :src="'/storage/images/pigeon.svg'" alt="pmail" /> <span class="text-we4vBg">Pidgen Mail</span>
                            </div>
                        </div>
                    </template>
                </Title>

                <div id="header" class="flex items-center justify-between px-4">
                    <i @click="showPmailModal = !showPmailModal" class="fas fa-edit  text-we4vOrange hover:translate-x-1 cursor-pointer"></i>
                    <i class="fas fa-inbox text-we4vBlue hover:text-we4vDarkBlue cursor-pointer"></i>
                    <i class="fas fa-paper-plane text-we4vBlue hover:text-we4vDarkBlue cursor-pointer"></i>
                    <i class="fas fa-trash text-we4vBlue hover:text-we4vDarkBlue cursor-pointer"></i>
                    <input type="text" name="search" id="search" class="w-1/2 pl-4 shadow-sm bg-we4vBg h-8 rounded-lg border-none focus:border-we4vBlue focus:ring-1 focus:ring-we4vBlue focus:shadow-md text-sm tracking-tight font-medium" placeholder="Search folders">
                </div>
            </div>
        </template>
    </app-layout>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import AppLayout from '@/Layouts/AppLayout'
import Title from '@/Jetstream/SectionTitle'
import Form from '@/Jetstream/FormSection'
import { ref } from 'vue'

export default {
    name: 'PidgenMail',

    components: {
        AppLayout,
        Title,
        JetApplicationMark,
        Form,
    },

    setup() {
        const showPmailModal = ref(false)
        const addresseeList = ref([])
        const addressee = ref('')
        const showAddresseeList = ref(false)

        const addAddressee = () => {
            if (!addresseeList.value.includes(addressee.value)) {
                addresseeList.value.push(addressee.value)
            }
            addressee.value = ''
        }

        return {
            showPmailModal,
            addresseeList,
            showAddresseeList,
            addressee,
            addAddressee
        }
    }
    
}
</script>