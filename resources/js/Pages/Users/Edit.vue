<script setup>
import { useForm } from '@inertiajs/vue3'
import { formatDate } from '@/Utils/helpers'
import { toast } from '@/Utils/toaster'
import { dialog } from '@/Utils/dialog'
import { roleOptions } from '@/Utils/helpers'
import { txt } from '@/Utils/helpers'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import Button from '@/Components/Elements/Button.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import RadioButtons from '@/Components/Inputs/RadioButtons.vue'

const props = defineProps({
	user: Object
})

const form = useForm({
	name: props.user.name,
	email: props.user.email,
	password: '',
	password_confirmation: '',
	role: props.user.role
})

function submit() {
	if (deleteForm.processing) return

	form.patch(`/users/${props.user.id}`, {
		preserveScroll: true,
		onSuccess: () => {
			form.password = ''
			form.password_confirmation = ''
			toast.success(txt('Account edited'))
		},
		onError: (err) => {
			if (err.password) form.password_confirmation = ''
		}
	})
}

const deleteForm = useForm({})
function deleteUser() {
	if (form.processing) return

	dialog.delete(txt('Delete Account'),  txt('deleteUserQuestion', 'Are you sure you want to delete the account <strong>#0#</strong>?', [props.user.email]), {
		onConfirm: () => {
			deleteForm.delete(`/users/${props.user.id}`, {
				preserveScroll: true,
				onSuccess: () => toast.success(txt('Account deleted'))
			})
		}
	})
}
</script>

<template>
	<AuthenticatedLayout :header="`${user.name}`" width="narrow" backLink="/users">
		<Card :header="txt('Edit account')" :headerNote="`${txt('Added')}: ${formatDate(user.created_at)}`" as="form" @submit.prevent="submit">
			<div class="inputs-grid">
				<TextInput
					name="name"
					:label="txt('Name')"
					type="text"
					placeholder="Peter"
					v-model="form.name"
					required
					:error="form.errors.name"
				/>
				<TextInput
					name="email"
					label="Email"
					type="email"
					:placeholder="txt('your@email')"
					v-model="form.email"
					required
					:error="form.errors.email"
				/>
				<TextInput
					name="password"
					:label="txt('New Password')"
					type="password"
					:placeholder="txt('yoursecretpassword')"
					v-model="form.password"
					:error="form.errors.password"
				>
					<p v-if="form.password.length">{{ txt('passwotdChangeNote', 'Fill in only if you want to change the password') }}</p>
				</TextInput>
				<TextInput
					name="password_confirmation"
					:label="txt('Confirm Password')"
					type="password"
					:placeholder="txt('yoursecretpassword')"
					v-model="form.password_confirmation"
					:required="form.password.length ? true : false"
					:error="form.errors.password_confirmation"
				/>
				<RadioButtons
					:label="txt('Role')"
					:options="roleOptions"
					v-model="form.role"
					required
					:error="form.errors.role"
					:tooltip="txt('roleTooltip', '<strong>User</strong> can only edit the warehouse<br><strong>Admin</strong> can also edit user accounts')"
				/>
			</div>
			<template #buttons>
				<Button icon="trash" color="danger" variant="outline" :loading="deleteForm.processing" :disabled="form.processing" @click.prevent="deleteUser">{{ txt('Delete') }}</Button>
				<Button icon="save" type="submit" :loading="form.processing" :disabled="deleteForm.processing">{{ txt('Save') }}</Button>
			</template>
		</Card>
	</AuthenticatedLayout>
</template>