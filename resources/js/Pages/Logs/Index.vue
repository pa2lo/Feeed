<script setup>
import { ref } from 'vue'

import { formatDate } from '@/Utils/helpers'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import DataTable from '@/Components/Table/DataTable.vue'
import Column from '@/Components/Table/Column.vue'
import Pagination from '@/Components/Elements/Pagination.vue'
import Icon from '@/Components/Elements/Icon.vue'
import Modal from '@/Components/Modals/Modal.vue'
import Accordion from '@/Components/Elements/Accordion.vue'

const props = defineProps({
	logs: Object
})

const serviceMap = {
	'processAllFeeds': 'Process feeds',
	'processMultipleFeeds': 'Process feeds manually'
}

const showPayloadModal = ref(false)
const payloadData = ref(null)
function showPayload(data) {
	if (!data) return
	payloadData.value = data
	showPayloadModal.value = true
}
</script>

<template>
	<AuthenticatedLayout header="Logs">
		<Card>
			<h5>{{ logs.total }} records</h5>
			<DataTable :items="logs.data" :rowClick="showPayload">
				<Column type="icon">
					<template #default="{ data }">
						<Icon v-if="!data.has_errors" class="color-success" name="circle-check" />
						<Icon v-else class="color-warn" name="circle-alert" />
					</template>
				</Column>
				<Column header="Description" field="message" cellClass="isClickable" minWidth="10rem" />
				<Column header="New posts" align="center">
					<template #default="{ data }">
						<template v-if="data?.data?.newPosts">{{ data.data.newPosts }}</template>
						<template v-else>-</template>
					</template>
				</Column>
				<Column header="Date" field="created_at" type="date" />
			</DataTable>
			<Pagination
				v-if="logs.links"
				:currentPage="logs.current_page"
				:links="logs.links"
				:prevPage="logs.prev_page_url"
				:nextPage="logs.next_page_url"
				:firstPage="logs.first_page_url"
				:lastPage="logs.last_page_url"
				:pages="logs.last_page"
				:from="logs.from"
				:to="logs.to"
				:total="logs.total"
			/>
		</Card>
		<Modal v-model:open="showPayloadModal" :header="serviceMap[payloadData?.service]" :headerNote="formatDate(payloadData?.created_at)">
			<div v-if="payloadData">
				<div>Message: <strong>{{ payloadData.message }}</strong></div>
				<div>Has errors: <strong>{{ payloadData.has_errors ? 'true' : 'false' }}</strong></div>
				<div v-if="payloadData?.data?.newPosts">New posts: <strong>{{ payloadData.data.newPosts }}</strong></div>
			</div>
			<div v-if="payloadData.data" class="line divided">
				<Accordion v-if="payloadData.data.successFeeds?.length" :title="`Successfully updated feeds - ${payloadData.data.successFeeds?.length}`" pre open>
					{{ payloadData.data.successFeeds.join('\n') }}
				</Accordion>
				<Accordion v-if="Object.keys(payloadData.data.fetchErrors).length" title="Fetch errors" pre>
					{{ payloadData.data.fetchErrors }}
				</Accordion>
				<Accordion v-if="payloadData.data.log?.length" title="Log" pre>
					{{ payloadData.data.log.join('\n') }}
				</Accordion>
			</div>
		</Modal>
	</AuthenticatedLayout>
</template>