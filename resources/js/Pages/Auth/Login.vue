<template>
    <login-register-flash></login-register-flash>

    <div class="m-auto w-96 mt-24">
        <jet-application-mark class="block w-96" />

        <jet-validation-errors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <input type="hidden" name="_token" :value="csrf">

            <div class="mt-2 pb-4">
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="mt-2 pb-4">
                <jet-label for="password" value="Password" />
                <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
            </div>

            <div class="block mt-2">
                <label class="flex items-center">
                    <jet-checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-xs tracking-tight text-we4vGrey-600">Remember me</span>
                </label>
            </div>

            <div class="mt-6">
                <jet-button-blue class="w-full" :class="{ 'opacity-25': form.processing }" :type="submit" :disabled="form.processing">Log in</jet-button-blue>
            </div>

            <div class="flex text-center text-we4vDarkBlue font-semibold text-sm mt-10">
                <div class="hover:text-we4vBlue hover:border-we4vGrey-300 justify-center min-w-5/12 cursor-pointer rounded-lg border-b border-we4vGrey-200 pb-1 mr-2 transition ease-in-out duration-150">
                    <inertia-link v-if="canResetPassword" :href="route('password.request')">Forgot password?</inertia-link>
                </div>

                <div class="hover:text-we4vBlue hover:border-we4vGrey-300 justify-center min-w-5/12 cursor-pointer rounded-lg border-b border-we4vGrey-200 pb-1 transition ease-in-out duration-150">
                    <inertia-link :href="route('register')">Register</inertia-link>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import JetApplicationMark from '@/Jetstream/ApplicationMark'
import JetButtonBlue from '@/Jetstream/ButtonBlue'
import JetInput from '@/Jetstream/Input'
import JetCheckbox from '@/Jetstream/Checkbox'
import JetLabel from '@/Jetstream/Label'
import JetValidationErrors from '@/Jetstream/ValidationErrors'
import LoginRegisterFlash from '../Components/LoginRegisterFlash'

export default {
    components: {
        JetApplicationMark,
        JetButtonBlue,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors,
        LoginRegisterFlash
    },

    props: {
        canResetPassword: Boolean,
        status: String
    },

    data() {
        return {
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false,
                _token: this.$page.props.csrf_token,
            })
        }
    },

    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ... data,
                    remember: this.form.remember ? 'on' : ''
                }))
                .post(this.route('login'), {
                    onFinish: () => {
                        this.form.reset('password')
                    },
                })
        }
    }
}
</script>
