<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Button from '@/Components/Elements/Button.vue'
import Message from '@/Components/Elements/Message.vue'

defineProps({
    status: String,
})

const form = useForm({
    email: '',
})

const submit = () => {
	form.clearErrors()
    form.post('/forgot-password')
}
</script>

<template>
	<GuestLayout :header="txt('Forgot Password')">
		<p>{{ txt('forgottenPasswordNote', "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.") }}</p>
		<Message v-if="status" type="success">{{ status }}</Message>
		<form @submit.prevent="submit" class="line">
			<TextInput
				name="email"
				label="Email"
				type="email"
				:placeholder="txt('your@email')"
				v-model="form.email"
				required
				autofocus
				autocomplete="username"
				:error="form.errors.email"
			/>
			<div class="line margin-bigger">
				<Button full :loading="form.processing" type="submit">{{ txt('Email Password Reset Link') }}</Button>
			</div>
			<div class="line margin-bigger divided ta-c">
				<Link href="/login">{{ txt('Back to login') }}</Link>
			</div>
		</form>
	</GuestLayout>
</template>
