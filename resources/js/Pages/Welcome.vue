<template>
    <div class="m-auto w-96 mt-24">
        <jet-application-mark class="block w-96" />

        <div class="w-full flex mt-20 text-center text-we4vDarkBlue font-semibold text-sm">
            <div class="hover:text-we4vBlue hover:border-we4vGrey-300 justify-center min-w-5/12 cursor-pointer rounded-lg border-b border-we4vGrey-200 pb-1 mr-2 transition ease-in-out duration-150">
                <inertia-link :href="route('login')">Login</inertia-link>
            </div>

            <div class="hover:text-we4vBlue hover:border-we4vGrey-300 justify-center min-w-5/12 cursor-pointer rounded-lg border-b border-we4vGrey-200 pb-1 transition ease-in-out duration-150">
                <inertia-link :href="route('register')">Register</inertia-link>
            </div>
        </div>
    </div>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'

export default {
    name: 'Welcome',

    components: {
        JetApplicationMark
    }
}
</script>