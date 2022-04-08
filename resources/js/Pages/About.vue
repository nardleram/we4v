<template>
    <modal-backdrop :show="showBackdrop"></modal-backdrop>

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
                </Title>

                <Subtitle>
                    <template #title>
                        Everything you need to know
                    </template>
                </Subtitle>

                <div>
                    <h4 class="text-blue-600">1. Talkboard</h4>
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
import Input from './Components/Input'
import { Inertia } from '@inertiajs/inertia'
import { ref } from 'vue'

export default {
    name: 'About',

    components: {
        AppLayout,
        Title,
        Subtitle,
        JetApplicationMark,
        Input,
    },

    props: {
        errors: Object
    },

    setup(props) {

        const getGroup = async function () {
            let id = '5cdba9ce-f0af-47e5-bcc3-f5e52ed1f2fc'
            try {
                await axios.get('/about/showGroup/'+id)
                .then((response) => {
                    groupShow.value = response.data
                })
            } catch (err) {
                props.errors.value = err
            }
        }

        return {
        }
    }
    
}
</script>