<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'
import { dialog } from '@/Utils/dialog'
import { roleOptions } from '@/Utils/helpers'
import { txt } from '@/Utils/helpers'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import TableInfo from '@/Components/Table/TableInfo.vue'
import FilterTags from '@/Components/Table/FilterTags.vue'
import Tag from '@/Components/Elements/Tag.vue'
import DataTable from '@/Components/Table/DataTable.vue'
import Column from '@/Components/Table/Column.vue'
import Button from '@/Components/Elements/Button.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import RadioButtons from '@/Components/Inputs/RadioButtons.vue'
import Modal from '@/Components/Modals/Modal.vue'
import Icon from '@/Components/Elements/Icon.vue'

const props = defineProps({
	users: Array
})

const filter = ref('')
const filteredUsers = computed(() => {
	return props.users?.filter(user => [user.email, user.name].some(u => u.toLocaleLowerCase().includes(filter.value)))
})

const newUserModal = ref(false)
const newUserForm = useForm({
	name: '',
    email: '',
    password: '',
    password_confirmation: '',
	role: 'user'
})
function showNewUserModal() {
	newUserForm.clearErrors()
	newUserForm.reset()
	newUserModal.value = true
}
function submitNewUser() {
	newUserForm.clearErrors()
	newUserForm.post('/users', {
		preserveScroll: true,
		onSuccess: () => {
			toast.success(txt('Account created'), {
				onClick: () => {
					if (data?.props?.flash?.id) router.visit(`/users/${data.props.flash.id}`)
					else return
				}
			})
			newUserForm.reset()
			newUserModal.value = false
		}
	})
}

const removingId = ref(null)
const deleteForm = useForm({})

function deleteItem(user) {
	if (!user.id) return

	dialog.delete(txt('Delete Account'),  txt('deleteUserQuestion', 'Are you sure you want to delete the account <strong>#0#</strong>?', [user.email]), {
		onConfirm: () => {
			removingId.value = user.id
			deleteForm.delete(`/users/${user.id}`, {
				preserveScroll: true,
				onSuccess: () => toast.success(txt('Account deleted')),
				onFinish: () => removingId.value = null
			})
		}
	})
}
</script>

<template>
	<AuthenticatedLayout :header="txt('User accounts')">
		<Card>
			<TableInfo :count="filteredUsers?.length" :countWords="[txt('account'), txt('accounts2', 'accounts'), txt('accounts')]">
				<TextInput placeholder="Filter..." v-model="filter" icon="search" clearable />
				<template #buttons>
					<Button icon="plus" @click.prevent="showNewUserModal">{{ txt('New account') }}</Button>
				</template>
			</TableInfo>
			<FilterTags>
				<Tag v-if="filter" @click="filter = ''" clearable>Filter: {{ filter }}</Tag>
			</FilterTags>
			<DataTable :items="filteredUsers">
				<template v-if="filter != ''" #empty>
					<Button icon="x" variant="outline" @click="filter = ''">{{ txt('Reset filter') }}</Button>
				</template>
				<Column type="icon">
					<template #default="{ data }">
						<Icon :name="data.role == 'admin' ? 'user-edit' : 'eye'" v-tooltip="data.role == 'admin' ? txt('Admin') : txt('User')" />
					</template>
				</Column>
				<Column :header="txt('Name')" field="name" link="/users" linkParam="id" minWidth="6rem" />
				<Column header="Email" field="email" />
				<Column :header="txt('Added')" field="created_at" type="date" />
				<Column type="buttons">
					<template #default="{ data }">
						<template v-if="data.id != 1">
							<IcoButton v-if="$page.props.auth.user.id == data.id" link="/profile" icon="edit" v-tooltip="txt('Edit')" />
							<IcoButton v-else link="/users" :linkParam="data.id" icon="edit" v-tooltip="txt('Edit')" />
							<IcoButton v-if="$page.props.auth.user.id != data.id" :loading="removingId == data.id" icon="trash" color="danger" v-tooltip="txt('Delete')" @click.stop="deleteItem(data)" />
						</template>
					</template>
				</Column>
			</DataTable>
		</Card>
	</AuthenticatedLayout>
	<Modal v-model:open="newUserModal" :header="txt('New account')" width="narrow" :closeable="!newUserForm.processing" as="form" @submit.prevent="submitNewUser">
		<TextInput
			name="name"
			:label="txt('Name')"
			type="text"
			placeholder="Peter"
			v-model="newUserForm.name"
			required
			autofocus
			:error="newUserForm.errors.name"
		/>
		<TextInput
			name="email"
			label="Email"
			type="email"
			:placeholder="txt('your@email')"
			v-model="newUserForm.email"
			required
			:error="newUserForm.errors.email"
		/>
		<TextInput
			name="password"
			:label="txt('Password')"
			type="password"
			:placeholder="txt('yoursecretpassword')"
			v-model="newUserForm.password"
			required
			:error="newUserForm.errors.password"
		/>
		<TextInput
			name="password_confirmation"
			:label="txt('Confirm Password')"
			type="password"
			:placeholder="txt('yoursecretpassword')"
			v-model="newUserForm.password_confirmation"
			required
			:error="newUserForm.errors.password_confirmation"
		/>
		<RadioButtons
			:label="txt('Role')"
			:options="roleOptions"
			v-model="newUserForm.role"
			required
			:error="newUserForm.errors.role"
			:tooltip="txt('roleTooltip', '<strong>User</strong> can only edit the warehouse<br><strong>Admin</strong> can also edit user accounts')"
		/>
		<p>
			<Button type="submit" full :loading="newUserForm.processing">{{ txt('Create an account') }}</Button>
		</p>
	</Modal>
</template>