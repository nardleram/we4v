<template>
    <div class="m-auto w-96 mt-24">
        <jet-application-mark class="block w-96" />

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <input type="hidden" name="_token" :value="csrf">

            <div>
                <jet-label for="name" value="Name" />
                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
            </div>

            <div>
                <jet-label for="surname" value="Surname" />
                <jet-input id="surname" type="text" class="mt-1 block w-full" v-model="form.surname" required autofocus autocomplete="surname" />
            </div>

            <div>
                <jet-label for="username" value="Username" />
                <jet-input id="username" type="text" class="mt-1 block w-full" v-model="form.username" required autofocus autocomplete="username" />
            </div>

            <div>
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
            </div>

            <div>
                <jet-label for="password" value="Password" />
                <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
            </div>

            <div>
                <jet-label for="password_confirmation" value="Confirm Password" />
                <jet-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                <jet-label for="terms">
                    <div class="flex items-center">
                        <jet-checkbox name="terms" id="terms" v-model:checked="form.terms" />

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 hover:text-gray-900">Privacy Policy</a>
                        </div>
                    </div>
                </jet-label>
            </div>

            <jet-button-blue class="w-full mt-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Sign up
            </jet-button-blue>

            <div class="font-semibold text-sm text-center text-we4vDarkBlue mt-6 hover:text-we4vBlue hover:border-we4vGrey-300 justify-center min-w-5/12 cursor-pointer rounded-lg border-b border-we4vGrey-200 pb-1 transition ease-in-out duration-150">
                <inertia-link :href="route('login')">Already registered?</inertia-link>
            </div>
        </form>
    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetButtonBlue from '@/Jetstream/ButtonBlue'
    import JetInput from '@/Jetstream/Input'
    import JetCheckbox from "@/Jetstream/Checkbox";
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            JetApplicationMark,
            JetButtonBlue,
            JetInput,
            JetCheckbox,
            JetLabel,
            JetValidationErrors
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    surname: '',
                    username: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    slug: '',
                    terms: false,
                    csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('register'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
            }
        }
    }
</script>
