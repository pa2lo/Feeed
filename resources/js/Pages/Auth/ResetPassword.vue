<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Button from '@/Components/Elements/Button.vue'

const props = defineProps({
    email: String,
    token: String,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
	form.clearErrors()
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
	<GuestLayout :header="txt('Reset Password')">
		<form @submit.prevent="submit" class="line">
			<TextInput
				name="email"
				type="email"
				label="Email"
				:placeholder="txt('your@email')"
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
				:placeholder="txt('yournewpassword')"
				v-model="form.password"
				required
				autocomplete="new-password"
				:error="form.errors.password"
			/>

			<TextInput
				name="password_confirmation"
				:label="txt('Confirm Password')"
				type="password"
				:placeholder="txt('yournewpassword')"
				v-model="form.password_confirmation"
				required
				autocomplete="new-password"
				:error="form.errors.password_confirmation"
			/>

			<div class="line margin-bigger">
				<Button full :loading="form.processing" type="submit">{{ txt('Reset Password') }}</Button>
			</div>
			<div class="line margin-bigger divided ta-c">
				<Link href="/login">{{ txt('Back to login') }}</Link>
			</div>
		</form>
	</GuestLayout>
</template>
