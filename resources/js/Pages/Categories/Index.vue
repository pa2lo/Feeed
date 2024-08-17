<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'
import { dialog } from '@/Utils/dialog'
import { useAppForms } from '@/Composables/AppForms'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import Button from '@/Components/Elements/Button.vue'
import TableInfo from '@/Components/Table/TableInfo.vue'
import FilterTags from '@/Components/Table/FilterTags.vue'
import Tag from '@/Components/Elements/Tag.vue'
import DataTable from '@/Components/Table/DataTable.vue'
import Column from '@/Components/Table/Column.vue'
import Modal from '@/Components/Modals/Modal.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'

const props = defineProps({
	items: Array
})

const filter = ref('')
const filteredItems = computed(() => {
	return props.items.filter(item => [item.name, item.code].some(i => i?.toLocaleLowerCase()?.includes(filter.value.toLocaleLowerCase())))
})

const { showNewForm, showEditForm, activeForm, showModal } = useAppForms({
	id: null,
	name: ''
})

function submit() {
	activeForm.value.form.clearErrors()
	activeForm.value.type == 'newForm' ? submitNewForm() : submitEditForm()
}
function submitNewForm() {
	activeForm.value.form.post('/categories', {
		preserveScroll: true,
		onSuccess: () => onSubmitSuccess('Category created')
	})
}
function submitEditForm() {
	activeForm.value.form.patch(`/categories/${activeForm.value.form.id}`, {
		preserveScroll: true,
		onSuccess: () => onSubmitSuccess('Category updated')
	})
}
function onSubmitSuccess(text) {
	toast.success(text)
	showModal.value = false
}

const removingId = ref(null)
const deleteForm = useForm({})
function deleteItem(item) {
	if (!item.id) return

	dialog.delete('Delete category', `Are you sure you want to delete the <strong>${item.name}</strong> category?`, {
		onConfirm: () => {
			removingId.value = item.id
			deleteForm.delete(`/categories/${item.id}`, {
				preserveScroll: true,
				onSuccess: () => toast.success('Category deleted'),
				onFinish: () => removingId.value = null
			})
		}
	})
}
</script>

<template>
	<AuthenticatedLayout header="Categories">
		<Card>
			<TableInfo v-if="items?.length" :count="filteredItems?.length" :countWords="['category', 'categories', 'categories']">
				<TextInput placeholder="Filter..." v-model="filter" icon="search" clearable />
				<template #buttons>
					<Button icon="plus" @click.prevent="showNewForm">Add category</Button>
				</template>
			</TableInfo>
			<FilterTags>
				<Tag v-if="filter" @click="filter = ''" clearable>Filter: {{ filter }}</Tag>
			</FilterTags>
			<DataTable :items="filteredItems" itemWord="categories">
				<template #empty>
					<Button v-if="filter" icon="x" variant="outline" @click="filter = ''">Reset filter</Button>
					<Button v-else icon="plus" size="bigger" @click.prevent="showNewForm">Add category</Button>
				</template>
				<Column header="Title" field="name" minWidth="7rem" :colClick="(data) => showEditForm(data)" />
				<Column header="Added" field="created_at" type="date" />
				<Column type="buttons">
					<template #default="{ data }">
						<IcoButton icon="edit" v-tooltip="'Edit'" @click="showEditForm(data)" />
						<IcoButton :loading="removingId == data.id" icon="trash" color="danger" v-tooltip="'Delete'" @click.stop="deleteItem(data)" />
					</template>
				</Column>
			</DataTable>
		</Card>
		<Modal v-model:open="showModal" :header="activeForm?.type == 'newForm' ? 'New category' : 'Edit category'" width="narrow" :closeable="!activeForm?.form?.processing" as="form" @submit.prevent="submit">
			<TextInput
				label="Title"
				placeholder="Some site"
				autofocus
				required
				v-model="activeForm.form.name"
				:error="activeForm.form.errors.name"
			/>
			<p class="divided">
				<Button type="submit" full :loading="activeForm.form.processing">Save category</Button>
			</p>
		</Modal>
	</AuthenticatedLayout>
</template>