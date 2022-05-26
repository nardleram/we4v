<template>
    <app-layout>
        <template #centre>
            <div class="w-1/2 p-3 ml-1/4 tracking-tight">
                <Title>
                    <template #title>
                        {{ user.username }}’s profile page
                    </template>
                    <template #description>
                        View and change your profile
                    </template>
                </Title>
                <Subtitle>
                    <template #title>
                        Personal details
                    </template>
                </Subtitle>
                <Form>
                    <template #form>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="name">Name</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" v-model="form.name">
                            <div class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.name">{{ errors.name }}</div>
                        </div>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="surname">Surname</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" v-model="form.surname">
                            <div class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.surname">{{ errors.surname }}</div>
                        </div>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="username">Username</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="text" v-model="form.username">
                            <div class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.username">{{ errors.username }}</div>
                        </div>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="username">Email</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="email" v-model="form.email">
                            <div class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700" v-if="errors.email">{{ errors.email }}</div>
                        </div>
                    </template>
                </Form>
                <div @click.prevent="submitForm" class="w-11/12 m-auto bg-we4vGreen-500 text-we4vGrey-100 shadow hover:shadow-lg hover:bg-we4vGreen-600 hover:text-white my-4 font-bold uppercase text-xl p-4 rounded-lg text-center cursor-pointer">
                    Update profile
                </div>
                <Subtitle>
                    <template #title>
                        Change password
                    </template>
                </Subtitle>
                <Form>
                    <template #form>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="password">current password</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="password" v-model="passwordData.password">
                        </div>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="newPassword">new password</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="password" v-model="passwordData.newPassword">
                        </div>
                        <div class="w-full relative pb-4">
                            <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="newPassword_confirmation">re-enter password</label>
                            <input class="w-full pl-4 pt-9 pb-4 text-we4vGrey-600 bg-we4vGrey-100 h-8 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="password" v-model="passwordData.newPassword_confirmation">
                        </div>
                        <div @click="errors.newPassword = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.newPassword">{{ errors.newPassword }}</div>
                        <div @click="errors.password = false" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="errors.password">{{ errors.password }}</div>
                    </template>
                </Form>
                <div @click.prevent="updatePassword" class="w-11/12 m-auto bg-we4vGreen-500 text-we4vGrey-100 shadow hover:shadow-lg hover:bg-we4vGreen-600 hover:text-white my-4 font-bold uppercase text-xl p-4 rounded-lg text-center cursor-pointer">
                    Change password
                </div>
                <Subtitle>
                    <template #title>
                        User-profile images
                    </template>
                </Subtitle>
                <Form id="profileImage">
                    <template #form>
                        <div class="flex w-full relative pb-4 items-center">
                            <div class="w-1/6 border-2 border-transparent rounded-full focus:outline-none focus:border-we4vGrey-300 transition duration-150 ease-in-out mr-2">
                                <img :src="'/storage/'+$page.props.userProfileImages.profile" alt="Profile image">
                            </div>
                            <div class="w-5/6">
                                <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="profile">select new profile image</label>
                                <input multiple class="w-full pl-4 pt-9 pb-9 text-we4vGrey-600 bg-we4vGrey-100 h-12 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="file" id="profile">
                            </div>
                        </div>
                        <div @click="profileError.error = !profileError.error" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="profileError.error">Please select a new profile image.</div>
                        <div @click.prevent="submitProfile" class="w-11/12 m-auto bg-we4vGreen-500 text-we4vGrey-100 shadow hover:shadow-lg hover:bg-we4vGreen-600 hover:text-white my-4 font-bold uppercase text-lg p-4 rounded-lg text-center cursor-pointer">
                            Upload profile image
                        </div>
                    </template>
                </Form>
                <Form id="bkgrndImage">
                    <template #form>
                        <div class="flex w-full relative pb-4 items-center">
                            <div class="w-1/6 border-2 border-transparent rounded-full focus:outline-none focus:border-we4vGrey-300 transition duration-150 ease-in-out mr-2">
                                <img :src="'/storage/'+$page.props.userProfileImages.bkgrnd" alt="Background image">
                            </div>
                            <div class="w-5/6">
                                <label class="absolute pl-4 pt-2 text-we4vBlue text-xs lowercase font-medium tracking-tight" for="bkgrnd">select new background image</label>
                                <input multiple class="w-full pl-4 pt-9 pb-9 text-we4vGrey-600 bg-we4vGrey-100 h-12 rounded-full focus:outline-none focus:shadow-outline text-sm tracking-tight font-medium" type="file" id="bkgrnd">
                            </div>
                        </div>
                        <div @click="bkgrndError.error = !bkgrndError.error" class="w-9/12 text-sm font-bold mt-2 text-center m-auto rounded-lg py-1 shadow-md text-red-700 border-b-2 border-red-700 cursor-pointer" v-if="bkgrndError.error">Please select a new background image.</div>
                        <div @click.prevent="submitBkgrnd" class="w-11/12 m-auto bg-we4vGreen-500 text-we4vGrey-100 shadow hover:shadow-lg hover:bg-we4vGreen-600 hover:text-white my-4 font-bold uppercase text-lg p-4 rounded-lg text-center cursor-pointer">
                            Upload background image
                        </div>
                    </template>
                </Form>
            </div>
        </template>
    </app-layout>
</template>
 
<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Title from '@/Jetstream/SectionTitle'
    import Subtitle from '@/Jetstream/Subtitle'
    import Form from '@/Jetstream/FormSection'

    export default {
        name: 'MyProfile',

        components: {
            AppLayout,
            Title,
            Subtitle,
            Form
        },

        props: [
            'user',
            'errors',
        ],

        data() {
            return {
                form: {
                    'name': this.$page.props.authUser.name,
                    'surname': this.$page.props.authUser.surname,
                    'username': this.$page.props.authUser.username,
                    'email': this.$page.props.authUser.email
                },
                passwordData: {
                    'password': '',
                    'newPassword': '',
                    'newPassword_confirmation': ''
                },
                profileError: {
                    'error': false,
                },
                bkgrndError: {
                    'error': false,
                }
            }
        },

        methods: {
            async submitForm() {
                let res = await this.$inertia.patch('/users/'+this.$page.props.user.id+'/update', this.form)
            },

            async updatePassword() {
                if (this.passwordData.newPassword !== this.passwordData.newPassword_confirmation) {
                    this.errors.password = 'New-password entries do not match.'
                    this.passwordData.newPassword = ''
                    this.passwordData.newPassword_confirmation = ''
                    return
                }
                let res = await this.$inertia.patch('/users/'+this.$page.props.user.id+'/updatePassword', this.passwordData)
            },

            async submitProfile() {
                if (!(profile.files[0])) {
                    this.profileError.error = true;
                    return
                }

                let selectedImage = {}

                profile.files[0] ? selectedImage.profile = profile.files[0] : null

                let res = await this.$inertia.post('/images/profile/store', {
                    'image': selectedImage.profile
                })
                .then(() => this.$inertia.props.userProfileImages.profile = res.userProfileImages.profile)
                .catch((err) => {
                    console.log(err)
                })
            },

            async submitBkgrnd() {
                if (!(bkgrnd.files[0])) {
                    this.bkgrndError.error = true;
                    return
                }

                let selectedImage = {}

                bkgrnd.files[0] ? selectedImage.bkgrnd = bkgrnd.files[0] : null

                let res = await this.$inertia.post('/images/background/store', {
                    'image': selectedImage.bkgrnd
                })
                .then(() => this.$inertia.props.userProfileImages.bkgrnd = res.userProfileImages.bkgrnd)
                .catch((err) => {
                    console.log(err)
                })
            }
        }
    }
</script>