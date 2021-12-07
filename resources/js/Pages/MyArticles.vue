<template>
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
                        This is where you share your professional acumen, be it academic, or artistic/literary, or your experise in a trade, etc. More importantly, this is where you share <span class="italic">what you are</span>, which you seek to offer to your community for the healthy furtherance of your community.
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
import FlashMessage from './Components/FlashMessage'
import ModalBackdrop from './Components/ModalBackdrop'
import manageModals from '../Pages/Composables/manageModals'
import { Inertia } from '@inertiajs/inertia'

export default {
    name: 'MyArticles',

    components: {
        AppLayout,
        Title,
        ModalBackdrop,
        ButtonBlue,
        FlashMessage
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

        return { amInside, amOutside, clearModal, nowInside, nowOutside, onClickOutside, showBackdrop }
    }
    
}
</script>