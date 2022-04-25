<template>
        <div class="m-auto w-96 mt-24 mb-4">
            <jet-application-mark class="block w-96" />

            <div class="my-4 text-sm text-we4vGrey-600 text-center">
                Thanks for joining us! Before you get going, could you verify your email address by clicking the link we just emailed you? If you didn’t receive the email, click the resend button below and we’ll dispatch another.
            </div>

            <div class="mb-4 font-medium text-sm text-we4vGreen-600" v-if="verificationLinkSent" >
                A new verification link has been sent to the email address you provided during registration.
            </div>

            <form @submit.prevent="submit">
                <div class="mt-4 flex flex-wrap items-center justify-between">
                    <!-- <jet-button-blue :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Resend verification email
                    </jet-button-blue> -->
                    <button-blue :type="'submit'" class="w-full text-white">
                        Resend verification email
                    </button-blue>

                    <inertia-link :href="route('logout')" method="post" as="button" class="w-full text-center tracking-tighter text-sm font-bold text-we4vBlue hover:text-we4vDarkBlue">Log out</inertia-link>
                </div>
            </form>
        </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetButtonBlue from '@/Jetstream/ButtonBlue'
    import ButtonBlue from '@/Jetstream/ButtonBlue'

    export default {
        components: {
            JetApplicationMark,
            JetButtonBlue,
            ButtonBlue
        },

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form()
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('verification.send'))
            },
        },

        computed: {
            verificationLinkSent() {
                return this.status === 'verification-link-sent';
            }
        }
    }
</script>
