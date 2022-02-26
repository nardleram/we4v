<template>
    <flash-message></flash-message>
    <error-message></error-message>
    <modal-backdrop :show="showBackdrop">
    </modal-backdrop>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 max-h-screen overflow-x-hidden tracking-tight">
                <Title>
                    <template #title>
                        My articles
                    </template>
                    <template #description>
                        This is where you share your professional acumen, be it academic, or artistic/literary, or your experise in a trade, etc. More importantly, this is where you share <span class="italic">what you are</span>, which you seek to offer the world in the interests of the world.
                    </template>
                </Title>
                
            </div>
        </template>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Title from '@/Jetstream/SectionTitle'
import ButtonBlue from '../Jetstream/ButtonBlue'
import ModalBackdrop from './Components/ModalBackdrop'
import manageModals from '../Pages/Composables/manageModals'
import { watch, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import FlashMessage from '../Pages/Components/FlashMessage'
import ErrorMessage from '../Pages/Components/ErrorMessage'

export default {
    name: 'MyArticles',

    components: {
        AppLayout,
        Title,
        ModalBackdrop,
        ButtonBlue,
        FlashMessage,
        ErrorMessage
    },

    setup() {
        const {
            amInside,
            amOutside, 
            clearModal,
            nowInside, 
            nowOutside,
            onClickOutside,
            showBackdrop,
        } = manageModals()

        const error = ref(false)
        const flashMessage = ref(false)

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
            amInside, 
            amOutside, 
            clearModal,
            editor,
            nowInside, 
            nowOutside, 
            onClickOutside, 
            showBackdrop }
    }
    
}
</script>