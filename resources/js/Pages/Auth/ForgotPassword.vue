<template>
    <div class="m-auto w-96 mt-24">
        <jet-application-mark class="block w-96" />

        <jet-validation-errors class="mb-4" />

        <div class="my-4 text-sm text-gray-600 text-center">
            Forgot your password? No problem. Enter your email below and we’ll send you a password-reset link.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <input type="hidden" name="_token" :value="csrf">
            
            <div>
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <jet-button-blue class="ml-4 text-we4vBg bg-we4vBlue" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Email password-reset link
                </jet-button-blue>
            </div>
        </form>
    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetButtonBlue from '@/Jetstream/ButtonBlue'
    import JetInput from '@/Jetstream/Input'
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            JetApplicationMark,
            JetButtonBlue,
            JetInput,
            JetLabel,
            JetValidationErrors
        },

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.email'))
            }
        }
    }
</script>
