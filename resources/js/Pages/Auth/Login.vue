<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Button from '@/Components/Elements/Button.vue'
import Message from '@/Components/Elements/Message.vue'
import SimpleCheckbox from '@/Components/Inputs/SimpleCheckbox.vue'

defineProps({
    canResetPassword: Boolean,
	canRegister: Boolean,
    status: String,
	message: String
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post('/login', {
        onFinish: () => form.reset('password')
    })
}

function getStatusMessage(action) {
	if (action == 'loggedOut') return 'Logged out successfully'
	else if (action == 'accountRemoved') return 'Account deleted successfully'
	else return action
}
</script>

<template>
	<GuestLayout :header="txt('Log in')">
		<Message v-if="status" type="success">{{ txt(getStatusMessage(status)) }}</Message>
		<Message v-if="message" type="info">{{ message }}</Message>
		<form @submit.prevent="submit" class="line">
			<TextInput
				name="email"
				label="Email"
				:placeholder="txt('your@email')"
				type="email"
				v-model="form.email"
				required
				autofocus
				autocomplete="username"
				:error="form.errors.email"
			/>

			<TextInput
				name="password"
				:label="txt('Password')"
				type="password"
				:placeholder="txt('yoursecretpassword')"
				v-model="form.password"
				required
				autocomplete="current-password"
				:error="form.errors.password"
				class="login-pass"
			>
				<Link class="nu-link" v-if="canResetPassword" href="/forgot-password">{{ txt('Forgot your password?') }}</Link>
			</TextInput>

			<SimpleCheckbox class="margin-bigger" name="remember" v-model="form.remember">{{ txt('Remember me') }}</SimpleCheckbox>

			<div class="line margin-bigger">
				<Button full :loading="form.processing" type="submit">{{ txt('Log me in') }}</Button>
			</div>
			<div v-if="canRegister" class="line margin-bigger divided ta-c">
				{{ txt('Don\'t have an account?') }} <Link href="/register">{{ txt('Sign up') }}</Link>
			</div>
		</form>
	</GuestLayout>
</template>