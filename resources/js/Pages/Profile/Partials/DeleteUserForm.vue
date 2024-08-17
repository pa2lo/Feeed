<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import Button from '@/Components/Elements/Button.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Card from '@/Components/Elements/Card.vue'
import Modal from '@/Components/Modals/Modal.vue'

const passwordInput = ref(null)
const confirmingUserDeletion = ref(false)

const form = useForm({
    password: '',
})

const deleteUser = () => {
    form.delete('/profile', {
        preserveScroll: true,
        onSuccess: () => confirmingUserDeletion.value = false,
        onError: () => passwordInput.value.focus()
    })
}

function resetPasswordForm() {
	form.clearErrors()
	form.reset()
}

const deleteAccountNote = txt('deleteAccountNote', 'Once your account is deleted, all of its resources and data will be permanently deleted.')
</script>

<template>
	<Card :header="txt('Delete Account')">
		<p>
			{{ deleteAccountNote }} {{ txt('deleteAccountSentence1', 'Before deleting your account, please download any data or information that you wish to retain.') }}
		</p>
		<template #buttons>
			<Button icon="trash" variant="outline" color="danger" @click="confirmingUserDeletion = true">{{ txt('Delete Account') }}</Button>
		</template>
	</Card>
	<Modal v-model:open="confirmingUserDeletion" closeable width="narrow" :header="txt('deleteAccountQuestion', 'Are you sure you want to delete your account?')" @modalClosed="resetPasswordForm">
		<p>
			{{ deleteAccountNote }} {{ txt('deleteAccountSentence2', 'Please enter your password to confirm you would like to permanently delete your account.') }}
		</p>
		<TextInput
			ref="passwordInput"
			v-model="form.password"
			type="password"
			autofocus
			autocomplete="password"
			:placeholder="txt('Password')"
			@keydown.enter="deleteUser"
			:error="form.errors.password"
		/>
		<template #buttons>
			<Button color="link" variant="outline" @click.prevent="confirmingUserDeletion = false">{{ txt('Cancel') }}</Button>
			<Button color="danger" full :loading="form.processing" @click.prevent="deleteUser">{{ txt('Delete Account') }}</Button>
		</template>
	</Modal>
</template>
