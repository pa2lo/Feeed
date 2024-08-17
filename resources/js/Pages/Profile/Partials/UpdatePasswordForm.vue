<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'
import { txt } from '@/Utils/helpers'

import Button from '@/Components/Elements/Button.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Card from '@/Components/Elements/Card.vue'

const passwordInput = ref(null)
const currentPasswordInput = ref(null)

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updatePassword = () => {
	form.clearErrors()
    form.put('/password', {
		errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => {
			toast.success(txt('Password updated'))
			form.reset()
		},
        onError: () => {
			if (form.errors.current_password) {
				form.reset('current_password')
				currentPasswordInput.value.focus()
			} else if (form.errors.password) {
				form.reset('password', 'password_confirmation')
				passwordInput.value.focus()
			}
        },
    })
}
</script>

<template>
	<Card as="form" :header="txt('Update Password')" :header-note="txt('updatePasswordNote', 'Ensure your account is using a long, random password to stay secure.')" @submit.prevent="updatePassword">
		<div class="inputs-grid">
			<TextInput
				name="current_password"
				:label="txt('Current Password')"
				ref="currentPasswordInput"
				v-model="form.current_password"
				type="password"
				autocomplete="current-password"
				:placeholder="txt('yoursecretpassword')"
				:error="form.errors.current_password"
			/>
			<TextInput
				name="password"
				:label="txt('New Password')"
				class="input-col1"
				ref="passwordInput"
				v-model="form.password"
				type="password"
				autocomplete="new-password"
				:placeholder="txt('yournewpassword')"
				:error="form.errors.password"
			/>
			<TextInput
				name="password_confirmation"
				:label="txt('Confirm Password')"
				v-model="form.password_confirmation"
				type="password"
				autocomplete="new-password"
				:placeholder="txt('yournewpassword')"
				:error="form.errors.password_confirmation"
			/>
		</div>
		<template #buttons>
			<Button type="submit" :loading="form.processing">{{ txt('Save') }}</Button>
		</template>
	</Card>
</template>
