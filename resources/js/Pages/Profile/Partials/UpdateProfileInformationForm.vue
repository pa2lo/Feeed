<script setup>
import { useForm } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'
import { txt } from '@/Utils/helpers'

import TextInput from '@/Components/Inputs/TextInput.vue'
import Message from '@/Components/Elements/Message.vue'
import Button from '@/Components/Elements/Button.vue'
import Card from '@/Components/Elements/Card.vue'

const props = defineProps({
    user: Object,
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
})

function sendForm() {
	if (form.processing) return

	form.patch('/profile', {
		errorBag: 'updateProfileInformation',
		preserveScroll: true,
		onSuccess: () => {
			toast.success(txt('Profile information updated'))
		}
	})
}

function sendVerificationEmail() {
	useForm({}).post('/email/verification-notification', {
		preserveScroll: true,
		onSuccess: () => {
			toast.success(txt('verificationSentNote2', 'A new verification link has been sent to your email address'))
		}
	})
}
</script>

<template>
	<Card as="form" @submit.prevent="sendForm" :header="txt('Profile Information')" :header-note="txt('profileInfoNote', 'Update your account\'s profile information and email address.')">
		<div class="inputs-grid">
			<TextInput
				name="name"
				:label="txt('Name')"
				v-model="form.name"
				required
				autocomplete="name"
				:error="form.errors.name"
			/>
			<TextInput
				name="email"
				label="Email"
				type="email"
				v-model="form.email"
				required
				autocomplete="username"
				:error="form.errors.email"
			/>
		</div>
		<Message v-if="$page.props.mustVerifyEmail && user.email_verified_at === null" type="warning">
			<p>{{ txt('emailNotVerified', 'Your email address is unverified.') }} <a @click.prevent="sendVerificationEmail" href="#">{{ txt('Resend Verification Email') }}</a>.</p>
		</Message>
		<template #buttons>
			<Button type="submit" :loading="form.processing">{{ txt('Save') }}</Button>
		</template>
	</Card>
</template>
