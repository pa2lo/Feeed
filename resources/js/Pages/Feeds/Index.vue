<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'
import { dialog } from '@/Utils/dialog'
import { useAppForms } from '@/Composables/AppForms'

import { formatDate } from '@/Utils/helpers'

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
import Icon from '@/Components/Elements/Icon.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
import InputsRow from '@/Components/Inputs/InputsRow.vue'
import RadioButtons from '@/Components/Inputs/RadioButtons.vue'
import SimpleToggle from '@/Components/Inputs/SimpleToggle.vue'
import Dropdown from '@/Components/Floating/Dropdown.vue'
import DropdownLink from '@/Components/Floating/DropdownLink.vue'
import SlideToggle from '@/Components/Elements/SlideToggle.vue'

const props = defineProps({
	items: Array,
	categories: Array
})

const networkOptions = [
	{
		value: '',
		title: 'All networks'
	},
	{
		value: 'facebook',
		title: 'Facebook'
	},
	{
		value: 'instagram',
		title: 'Instagram'
	}
]
const categoryMap = computed(() => {
	return props.categories.reduce((acc, item) => {
		acc.push({
			title: item.name,
			value: item.id
		})
		return acc
	}, [])
})

const showFilter = ref(false)
const filterTitle = ref('')
const filterSort = ref('')
const filterState = ref('')
const filterCategory = ref([])
const filterNetwork = ref('')
const filteredItems = computed(() => {
	let res = props.items.filter(item => item.name.toLocaleLowerCase()?.normalize('NFD')?.replace(/[\u0300-\u036f]/g, '')?.includes(filterTitle.value.toLocaleLowerCase().normalize('NFD')?.replace(/[\u0300-\u036f]/g, '')))
	if (filterSort.value) res = res.sort((a, b) => {
		if (filterSort.value == 'new') return b.id - a.id
		if (filterSort.value == 'titleaz') return a.name.localeCompare(b.name)
		if (filterSort.value == 'titleza') return b.name.localeCompare(a.name)
		if (filterSort.value == 'postsdesc') return b.posts_count - a.posts_count
		if (filterSort.value == 'postsasc') return a.posts_count - b.posts_count
	})
	if (filterState.value) res = res.filter(item => {
		if (filterState.value == 'active') return item.active
		else return !item.active
	})
	if (filterCategory.value.length) res = res.filter(item => item.categories.some(c => filterCategory.value.includes(c)))
	if (filterNetwork.value.length) res = res.filter(item => item.network == filterNetwork.value)
	return res
})

const sortOptions = [
	{
		title: 'Oldest',
		value: ''
	}, {
		title: 'Newest',
		value: 'new'
	}, {
		title: 'Title A-Z',
		value: 'titleaz'
	}, {
		title: 'Title Z-A',
		value: 'titleza'
	}, {
		title: 'Most posts',
		value: 'postsdesc'
	}, {
		title: 'Least posts',
		value: 'postsasc'
	}
]
const stateOptions = [
	{
		title: 'All states',
		value: ''
	}, {
		title: 'Active only',
		value: 'active'
	}, {
		title: 'Inactive only',
		value: 'inactive'
	}
]

function resetFilter() {
	filterTitle.value = ''
	filterState.value = ''
	filterCategory.value = []
	filterNetwork.value = ''
}

const { showNewForm, showEditForm, activeForm, showModal } = useAppForms({
	id: null,
	name: '',
	url: '',
	network: '',
	categories: [],
	active: false
})

const updateOptions = [
	{
		title: 'Inactive',
		value: false,
		color: 'danger'
	}, {
		title: 'Active',
		value: true,
		color: 'success'
	}
]

function submit() {
	activeForm.value.form.clearErrors()
	activeForm.value.type == 'newForm' ? submitNewForm() : submitEditForm()
}
function submitNewForm() {
	activeForm.value.form.post('/feeds', {
		preserveScroll: true,
		onSuccess: () => onSubmitSuccess('Feed created')
	})
}
function submitEditForm() {
	activeForm.value.form.patch(`/feeds/${activeForm.value.form.id}`, {
		preserveScroll: true,
		onSuccess: () => onSubmitSuccess('Feed updated')
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

	dialog.delete('Delete feed', `Are you sure you want to delete the <strong>${item.name}</strong> feed?`, {
		onConfirm: () => {
			removingId.value = item.id
			deleteForm.delete(`/feeds/${item.id}`, {
				preserveScroll: true,
				onSuccess: () => toast.success('Feed deleted'),
				onFinish: () => removingId.value = null
			})
		}
	})
}
function deleteMultiple() {
	if (!filteredTableModel.value.length) return

	dialog.delete('Delete feeds', `Are you sure you want to delete the <strong>${filteredTableModel.value.length}</strong> feeds?`, {
		onConfirm: () => {
			axios.post(`/feeds/deleteMultiple`, {
				feeds: filteredTableModel.value
			}).then((response) => {
				if (response?.data?.success) {
					toast.success(`${filteredTableModel.value.length} feeds deleted`)
					router.reload({
						preserveScroll: true,
						only: ['items']
					})
				} else {
					toast.error('Operation failed!')
					console.log(response)
				}
			}).catch((err) => {
				toast.error('Operation failed!')
				console.log(err)
			})
		}
	})
}

function setNetwork() {
	activeForm.value.form.url = activeForm.value.form.url.split('?')[0].replace(/\/$/, "")
	if (activeForm.value.form.url.startsWith('https://www.facebook.com/')) activeForm.value.form.network = 'facebook'
	else if (activeForm.value.form.url.startsWith('https://www.instagram.com/')) activeForm.value.form.network = 'instagram'
}

const refreshingIds = ref([])
const refreshingAll = ref(false)
function refreshFeed(id) {
	refreshingIds.value.push(id)

	axios.get(`/updateFeed/${id}`).then((response) => {
		if (response?.data?.success == true) {
			toast.success('Feed updated', {
				message: response?.data?.processedPosts ? `${response.data.processedPosts} posts processed<br>${response?.data?.newPosts} new posts` : null
			})
			router.reload({
				preserveScroll: true,
				only: ['items'],
				onSuccess: () => {
					if (showModal.value) showEditForm(props.items.find(i => i.id == id))
				}
			})
		} else {
			toast.error('Update failed!')
			console.log(response)
		}
	}).catch((err) => {
		toast.error('Update failed!')
		console.log(err)
	}).finally(() => {
		refreshingIds.value = refreshingIds.value.filter(i => i != id)
	})
}
function refreshMultipleFeeds(ids = null, action) {
	refreshingIds.value.push(...ids)
	refreshingAll.value = true

	axios.get('/updateFeeds', {
		params: {
			feeds: ids,
			action: action ?? null
		}
	}).then((response) => {
		if (response?.data?.id) {
			toast[response?.data?.has_errors ? 'warning' : 'success'](`Feeds updated${response?.data?.has_errors ? ' with errors' : ''}`, {
				message: response?.data?.data?.successFeeds?.length ? `${response.data.data.successFeeds.length} feeds reloaded<br>${response.data?.data?.newPosts} new posts` : null
			})
			router.reload({
				preserveScroll: true,
				only: ['items']
			})
		} else {
			toast.error('Update failed!')
			console.log(response)
		}
	}).catch((err) => {
		toast.error('Update failed!')
		console.log(err)
	}).finally(() => {
		refreshingIds.value = refreshingIds.value.filter(i => !ids.includes(i))
		refreshingAll.value = false
	})
}
function refreshAllFeeds() {
	let activeIDs = props.items.reduce((acc, item) => {
		if (item.active) acc.push(item.id)
		return acc
	}, [])
	refreshMultipleFeeds(activeIDs)
}
function refreshSelectedFeeds() {
	if (!filteredTableModel.value.length) return
	refreshMultipleFeeds(filteredTableModel.value, 'processMultipleFeeds')
}

function switchActive(e, id) {
	axios.post(`/feeds/${id}/switch`, {
		active: e.target.checked
	}).then((response) => {
		if (response?.data?.success) {
			toast.success('Feeds updated')
			let found = props.items?.find(i => i.id == id)
			if (found) found.active = e.target.checked
		} else {
			toast.error('Update failed!')
			console.log(response)
		}
	}).catch((err) => {
		toast.error('Update failed!')
		console.log(err)
	})
}
function switchSelectedActive(state) {
	if (!filteredTableModel.value.length) return

	axios.post(`/feeds/switchMultiple`, {
		feeds: filteredTableModel.value,
		active: state
	}).then((response) => {
		if (response?.data?.success) {
			toast.success(`${filteredTableModel.value.length} feeds updated`)
			filteredTableModel.value.forEach(id => {
				let found = props.items?.find(i => i.id == id)
				if (found) found.active = state
			})
		} else {
			toast.error('Update failed!')
			console.log(response)
		}
	}).catch((err) => {
		toast.error('Update failed!')
		console.log(err)
	})
}

const tableModel = ref([])
const filteredTableModel = computed(() => tableModel.value.filter(i => filteredItems.value.some(f => f.id == i)))
</script>

<template>
	<AuthenticatedLayout header="Feeds">
		<Card>
			<TableInfo v-if="items?.length" :count="filteredItems?.length" :countWords="['feed', 'feeds', 'feeds']">
				<Button icon="filter" variant="outline" color="link" :disabled="[filterCategory, filterTitle, filterState, filterNetwork].some(f => f.length)" @click="showFilter = !showFilter">Filter</Button>
				<template #buttons>
					<Dropdown :disabled="!filteredTableModel.length || refreshingAll || refreshingIds.length > 0" variant="outline" color="link" :label="`Action${filteredTableModel.length ? ` (${filteredTableModel.length})` : ''}`">
						<DropdownLink icon="eye-off" label="Disable selected" @click="switchSelectedActive(false)" :closeable="true" />
						<DropdownLink icon="eye" label="Enable selected" @click="switchSelectedActive(true)" :closeable="true" />
						<DropdownLink icon="refresh" label="Update selected" :disabled="!filteredTableModel.length || refreshingAll" @click="refreshSelectedFeeds" />
						<DropdownLink icon="trash" label="Delete selected" @click="deleteMultiple" color="error" />
					</Dropdown>
					<Button :loading="refreshingAll" icon="refresh" color="link" variant="outline" @click.prevent="refreshAllFeeds" v-tooltip="'Active feeds only'">Update all</Button>
					<Button icon="plus" @click.prevent="showNewForm">Add feed</Button>
				</template>
			</TableInfo>
			<SlideToggle class="line" :show="showFilter">
				<InputsRow wrap>
					<TextInput class="grow" placeholder="Filter..." v-model="filterTitle" icon="search" clearable :chars="14" />
					<SelectInput class="grow" placeholder="Categories" v-model="filterCategory" :options="categoryMap" showCount />
					<SelectInput placeholder="All networks" class="grow" v-model="filterNetwork" :options="networkOptions" />
					<SelectInput class="grow" v-model="filterState" :options="stateOptions" />
					<SelectInput class="grow" v-model="filterSort" :options="sortOptions" />
				</InputsRow>
				<FilterTags>
					<Tag v-if="filterTitle" @click="filterTitle = ''" clearable>Filter: {{ filterTitle }}</Tag>
					<Tag v-if="filterState" @click="filterState = ''" clearable>{{ stateOptions.find(o => o.value == filterState).title }}</Tag>
					<Tag v-if="filterCategory.length" v-for="cat in filterCategory" @click="filterCategory = filterCategory.filter(c => c != cat)" clearable>Category: {{ categoryMap.find(c => c.value == cat).title }}</Tag>
					<Tag v-if="filterNetwork" @click="filterNetwork = ''" clearable>Network: {{ filterNetwork }}</Tag>
				</FilterTags>
			</SlideToggle>
			<DataTable :items="filteredItems" itemWord="feeds" v-model="tableModel" modelField="id" :modelDisabled="refreshingAll">
				<template #empty>
					<Button v-if="filterTitle || filterState || filterCategory.length || filterNetwork" icon="x" variant="outline" @click="resetFilter">Reset filter</Button>
					<Button v-else icon="plus" size="bigger" @click.prevent="showNewForm">Add feed</Button>
				</template>
				<Column type="icon">
					<template #default="{ data }">
						<Icon :name="data.network" :class="data.active ? 'color-success' : 'color-error'" v-tooltip="data.active ? 'Active' : 'Inactive'" />
					</template>
				</Column>
				<Column header="Title" field="name" minWidth="7rem" :colClick="(data) => removingId != data.id && !refreshingIds.includes(data.id) && showEditForm(data)" />
				<Column v-if="filteredItems.some(i => i.posts_count > 0)" field="posts_count" align="center" header="Posts" />
				<Column header="Updated" field="downloaded_at" type="date" />
				<Column header="Active" align="center">
					<template #default="{ data }">
						<SimpleToggle :disabled="removingId == data.id || refreshingIds.includes(data.id)" :modelValue="data.active" @change="switchActive($event, data.id)" />
					</template>
				</Column>
				<Column type="buttons">
					<template #default="{ data }">
						<IcoButton v-if="data?.posts_count > 0" :disabled="removingId == data.id || refreshingIds.includes(data.id)" :link="`posts?feeds[]=${data.id}`" icon="article" v-tooltip="'Posts'" />
						<IcoButton :link="data.url" icon="external-link" target="_blank" rel="noopener noreferrer" v-tooltip="'Open link'" />
						<IcoButton :disabled="removingId == data.id || refreshingIds.includes(data.id)" icon="edit" v-tooltip="'Edit'" @click="showEditForm(data)" />
						<IcoButton :disabled="removingId == data.id" :loading="refreshingIds.includes(data.id)" icon="refresh" v-tooltip="'Update'" @click="refreshFeed(data.id)" />
						<IcoButton :disabled="refreshingIds.includes(data.id)" :loading="removingId == data.id" icon="trash" color="danger" v-tooltip="'Delete'" @click.stop="deleteItem(data)" />
					</template>
				</Column>
			</DataTable>
		</Card>
		<Modal v-model:open="showModal" :header="activeForm?.type == 'newForm' ? 'New feed' : 'Edit feed'" :closeable="!activeForm?.form?.processing && !refreshingIds.includes(activeForm?.form?.id)" :headerNote="activeForm?.form?.id ? `ID: ${activeForm.form.id}` : ''" as="form" @submit.prevent="submit">
			<div class="inputs-grid">
				<TextInput
					label="Title"
					placeholder="Some site"
					:autofocus="activeForm?.type == 'newForm'"
					required
					v-model="activeForm.form.name"
					:error="activeForm.form.errors.name"
				/>
				<RadioButtons
					label="Automatic updates"
					solid
					:options="updateOptions"
					v-model="activeForm.form.active"
					:error="activeForm.form.errors.active"
				/>
				<TextInput
					label="URL"
					placeholder="https://www.facebook.com/somesite"
					required
					v-model="activeForm.form.url"
					@change="setNetwork"
					:error="activeForm.form.errors.url"
				/>
				<SelectInput
					label="Network"
					required
					:options="networkOptions"
					v-model="activeForm.form.network"
					:error="activeForm.form.errors.network"
				/>
				<SelectInput
					label="Categories"
					full
					:options="categoryMap"
					v-model="activeForm.form.categories"
					:error="activeForm.form.errors.categories"
				/>
				<template v-if="activeForm?.type == 'editForm' && activeForm.form.downloaded_at">
					<TextInput class="input-col1" readOnly label="Updated at" :modelValue="formatDate(activeForm.form.downloaded_at)" />
					<TextInput readOnly label="Network id" copyable :modelValue="activeForm.form.network_id" />
					<InputsRow label="Thumbnail" full>
						<TextInput class="grow" v-model="activeForm.form.thumbnail" />
						<img v-if="activeForm.form.thumbnail" :src="activeForm.form.thumbnail" class="networkThumbnail" />
					</InputsRow>
				</template>
			</div>
			<template v-if="activeForm?.form?.created_at" #footer>
				Added: <strong class="nowrap">{{ formatDate(activeForm.form.created_at) }}</strong>
			</template>
			<template #buttons>
				<Button v-if="activeForm?.type == 'editForm'" icon="refresh" variant="outline" color="link" :loading="refreshingIds.includes(activeForm?.form?.id)" @click.prevent="refreshFeed(activeForm?.form?.id)">Update</Button>
				<Button v-if="activeForm?.form?.posts_count > 0" icon="article" :link="`posts?feeds[]=${activeForm?.form?.id}`" variant="outline" color="link" :disabled="refreshingIds.includes(activeForm?.form?.id)">{{ activeForm.form.posts_count }} posts</Button>
				<Button type="submit" :loading="activeForm.form.processing" :disabled="refreshingIds.includes(activeForm?.form?.id)">Save feed</Button>
			</template>
		</Modal>
	</AuthenticatedLayout>
</template>

<style>
.networkThumbnail {
	width: 2.75rem;
	height: 2.75rem;
	padding: 1px;
	border: 1px solid var(--input-border);
	border-radius: 0.25em;
}
</style>