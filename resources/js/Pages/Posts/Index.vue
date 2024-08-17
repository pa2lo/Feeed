<script setup>
import { ref } from 'vue'
import { useForm, router, Link } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'
import { dialog } from '@/Utils/dialog'
import { formatDate } from '@/Utils/helpers'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import TableInfo from '@/Components/Table/TableInfo.vue'
import DataTable from '@/Components/Table/DataTable.vue'
import Column from '@/Components/Table/Column.vue'
import Icon from '@/Components/Elements/Icon.vue'
import Pagination from '@/Components/Elements/Pagination.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
import Loader from '@/Components/Elements/Loader.vue'
import Button from '@/Components/Elements/Button.vue'
import IcoButton from '@/Components/Elements/IcoButton.vue'
import Modal from '@/Components/Modals/Modal.vue'
import Accordion from '@/Components/Elements/Accordion.vue'
import SlideToggle from '@/Components/Elements/SlideToggle.vue'
import InputsRow from '@/Components/Inputs/InputsRow.vue'
import FilterTags from '@/Components/Table/FilterTags.vue'
import Tag from '@/Components/Elements/Tag.vue'

const props = defineProps({
	filters: Object,
	posts: Object,
	feeds: Array,
	categories: Array
})

const hasActiveFilter = Object.values(props.filters).some(v => v && v.length) ? true : false
const showFilter = ref(hasActiveFilter)

const filterValues = ref({
	feeds: props.filters?.feeds || [],
	categories: props.filters?.categories || [],
	type: props.filters?.type || '',
	active: props.filters?.active || ''
})

const feedsMap = props.feeds.reduce((acc, feed) => {
	acc.push({
		title: feed.name,
		value: feed.id
	})
	return acc
}, [])

const categoriesMap = props.categories.reduce((acc, feed) => {
	acc.push({
		title: feed.name,
		value: feed.id
	})
	return acc
}, [])

const feedsIDMap = props.feeds.reduce((acc, feed) => {
	acc[feed.id] = {
		name: feed.name,
		url: feed.url
	}
	return acc
}, {})

const postTypes = [
	{
		title: 'All types',
		value: ''
	}, {
		title: 'Text',
		value: 'text'
	}, {
		title: 'Image',
		value: 'image'
	}, {
		title: 'Video',
		value: 'video'
	}, {
		title: 'Link',
		value: 'link'
	}, {
		title: 'Gallery',
		value: 'gallery'
	}
]

const activeOptions = [
	{
		title: 'All feed states',
		value: ''
	}, {
		title: 'Active only',
		value: '1'
	}, {
		title: 'Inactive only',
		value: '0'
	}
]

const isLoading = ref(false)

const typeIconMap = {
	'text': 'type-text',
	'image': 'type-image',
	'gallery': 'type-gallery',
	'video': 'type-video',
	'link': 'type-link'
}

function setFilter() {
	let params = Object.entries(filterValues.value).reduce((acc, [key, val]) => {
		if (val.length) acc[key] = val
		return acc
	}, {})
	router.get(props.posts.path, params, {
		preserveState: true,
		preserveScroll: true,
		onStart: () => isLoading.value = true,
		onFinish: () => isLoading.value = false
	})
}

function clearFilter() {
	Object.keys(filterValues.value).forEach(key => {
		filterValues.value[key] = []
	})
	setFilter()
}

const removingId = ref(null)
const deleteForm = useForm({})
function deletePost(id) {
	if (!id) return

	dialog.delete('Delete post', `Are you sure you want to delete the this post?`, {
		onConfirm: () => {
			removingId.value = id
			deleteForm.delete(`/posts/${id}`, {
				preserveScroll: true,
				onSuccess: () => toast.success('Post deleted'),
				onFinish: () => removingId.value = null
			})
		}
	})
}

const infoModalOpen = ref(false)
const infoData = ref(null)
const filteredInfoContent = ref(null)
function showInfo(data) {
	infoData.value = data
	if (data.content && Object.keys(data.content).length) {
		filteredInfoContent.value = Object.entries(data.content).reduce((acc, [key, val]) => {
			if (['id', 'network_link', 'likes', 'comments'].includes(key)) return acc
			acc[key] = val
			return acc
		}, {})
	} else filteredInfoContent.value = null
	infoModalOpen.value = true
}

function openLinkInNewTab(link) {
	if (!link) return
	window.open(link, '_blank', 'noopener,noreferrer')
}

function resetFilterValue(filter, value) {
	filterValues.value[filter] = value
	setFilter()
}
</script>

<template>
	<AuthenticatedLayout header="Posts">
		<Card>
			<TableInfo v-if="hasActiveFilter || posts.total" :count="posts.total" :countWords="['post', 'posts', 'posts']">
				<Button icon="filter" variant="outline" color="link" :disabled="Object.values(filterValues).some(f => f.length)" @click.prevent="showFilter = !showFilter">Filter</Button>
			</TableInfo>
			<SlideToggle class="line" :show="showFilter">
				<InputsRow wrap>
					<SelectInput class="grow" placeholder="Feeds" searchable :options="feedsMap" v-model="filterValues.feeds" :readOnly="isLoading" @change="setFilter" showCount />
					<SelectInput class="grow" placeholder="Categories" :options="categoriesMap" v-model="filterValues.categories" :readOnly="isLoading" @change="setFilter" showCount />
					<SelectInput class="grow" :options="postTypes" v-model="filterValues.type" :readOnly="isLoading" @change="setFilter" />
					<SelectInput class="grow" :options="activeOptions" v-model="filterValues.active" :readOnly="isLoading" @change="setFilter" />
				</InputsRow>
				<FilterTags>
					<Tag v-if="filterValues.feeds.length" v-for="feed in filterValues.feeds" @click="resetFilterValue('feeds', filterValues.feeds.filter(i => i != feed))" clearable>Feed: {{ feedsIDMap[feed].name }}</Tag>
					<Tag v-if="filterValues.categories.length" v-for="cat in filterValues.categories" @click="resetFilterValue('categories', filterValues.categories.filter(c => c != cat))" clearable>Feed: {{ categoriesMap.find(c => c.value == cat).title }}</Tag>
					<Tag v-if="filterValues.type" @click="resetFilterValue('type', '')" clearable>Type: {{ postTypes.find(t => t.value == filterValues.type).title }}</Tag>
					<Tag v-if="filterValues.active" @click="resetFilterValue('active', '')" clearable>State: {{ activeOptions.find(a => a.value == filterValues.active).title }}</Tag>
				</FilterTags>
			</SlideToggle>
			<Loader class="line" :loading="isLoading">
				<DataTable :items="posts.data" itemWord="posts">
					<template v-if="!feeds.length || (!hasActiveFilter && !posts?.total)" #emptyCont>
						<h3>No posts</h3>
						<p v-if="!feeds.length" class="mt07">Posts will be fetched from feeds. You have to add feeds first. <Link href="/feeds">Go to Feeds page</Link>.</p>
						<p v-else-if="!hasActiveFilter && !posts?.total" class="mt07">You have no fetched posts yet. <Link href="/feeds">Go to Feeds page</Link> and fetch posts from feeds first.</p>
					</template>
					<template v-if="Object.values(filterValues).some(f => f.length)" #empty>
						<Button icon="x" variant="outline" @click="clearFilter">Reset filter</Button>
					</template>
					<Column type="icon">
						<template #default="{ data }">
							<Icon v-if="data?.content?.image" class="clickable color-link" name="type-image" v-tooltip="{
									text: `<img src='${data.content.image}' loading='lazy' />`
								}" @click="dialog.image(data.content.image)" />
							<Icon v-else-if="data.type == 'video' && data?.content?.thumbnail" name="type-video" class="clickable color-link" v-tooltip="{
									text: `<img src='${data.content.thumbnail}' loading='lazy' />`
								}"  @click="openLinkInNewTab(data.content?.video)" />
							<Icon v-else-if="data?.type == 'link' && data?.content?.meta?.['twitter:image']" class="clickable color-link" name="type-link" v-tooltip="{
									text: `<img src='${data?.content?.meta?.['twitter:image']}' loading='lazy' />`
								}" @click="dialog.image(data?.content?.meta?.['twitter:image'])" />
							<Icon v-else-if="data?.type == 'gallery' && data?.content?.gallery?.[0]?.image" name="type-gallery" v-tooltip="{
									text: `<span class='ttip-gallery'>${data.content.gallery.map(g => `<img src='${g.image}' />`).join('')}</span>`
								}" />
							<Icon v-else :name="typeIconMap[data.type]" v-tooltip="data.type" />
						</template>
					</Column>
					<Column header="Text">
						<template #default="{ data }">
							<span v-if="data?.content?.text || data?.content?.meta?.description" v-html="data?.content?.text || data?.content?.meta?.description"></span>
						</template>
					</Column>
					<Column header="Feed" align="center">
						<template #default="{ data }">
							<a v-if="data?.feed_id" rel="noopener noreferrer" target="_blank" :href="feedsIDMap[data.feed_id].url">{{ feedsIDMap[data.feed_id].name }}</a>
						</template>
					</Column>
					<Column header="Date" field="time" type="date" />
					<Column type="buttons">
						<template #default="{ data }">
							<IcoButton icon="info2" rel="noopener noreferrer" target="_blank" @click="showInfo(data)" v-tooltip="'Info'" />
							<IcoButton v-if="data?.content?.network_link" icon="external-link" rel="noopener noreferrer" target="_blank" :link="data?.content?.network_link" v-tooltip="'Link'" />
							<IcoButton :loading="removingId == data.id" icon="trash" color="danger" v-tooltip="'Delete'" @click.stop="deletePost(data.id)" />
						</template>
					</Column>
				</DataTable>
				<Pagination
					v-if="posts.links"
					:currentPage="posts.current_page"
					:links="posts.links"
					:prevPage="posts.prev_page_url"
					:nextPage="posts.next_page_url"
					:firstPage="posts.first_page_url"
					:lastPage="posts.last_page_url"
					:pages="posts.last_page"
					:from="posts.from"
					:to="posts.to"
					:total="posts.total"
				/>
			</Loader>
			<Modal v-model:open="infoModalOpen" header="Post info">
				<template v-if="infoData">
					<div>Internal ID - <strong>{{ infoData.id }}</strong></div>
					<div>Type - <strong>{{ infoData.type }}</strong></div>
					<div>Feed - <strong>{{ feedsIDMap[infoData.feed_id].name }}</strong> ({{ infoData.feed_id }})</div>
					<div>Network ID - <strong>{{ infoData.network_id }}</strong></div>
					<div>Time - <strong>{{ formatDate(infoData.time) }}</strong></div>
					<div class="line divided">
						<div v-if="infoData.content?.id">ID - <strong>{{ infoData.content.id }}</strong></div>
						<div v-if="infoData.content?.network_link">Network link - <strong><a :href="infoData.content.network_link" rel="noopener noreferrer" target="_blank">{{ infoData.content.network_link }}</a></strong></div>
						<div v-if="infoData.content?.likes">Likes - <strong>{{ infoData.content.likes }}</strong></div>
						<div v-if="infoData.content?.comments">Comments - <strong>{{ infoData.content.comments }}</strong></div>
					</div>
					<div v-if="filteredInfoContent && Object.keys(filteredInfoContent).length" class="line">
						<Accordion title="Content" open pre>
							{{ filteredInfoContent }}
						</Accordion>
					</div>
				</template>
			</Modal>
		</Card>
	</AuthenticatedLayout>
</template>

<style>
	.ttip-gallery {
		display: flex;
		gap: 1px;
		flex-wrap: wrap;
	}
	.ttip-gallery img {
		display: block;
		height: 100%;
		aspect-ratio: 1;
		object-fit: cover;
		width: calc(50% - 0.5px);
	}
	.ttip-gallery img:first-child:nth-last-child(3), .ttip-gallery img:first-child:is(:nth-last-child(5), :nth-last-child(3), :nth-last-child(7)) ~ :nth-last-child(-n+3) {
		width: calc(100% / 3 - 2px / 3);
	}
</style>