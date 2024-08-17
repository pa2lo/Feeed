<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { toast } from '@/Utils/toaster'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import NumberInput from '@/Components/Inputs/NumberInput.vue'
import SelectInput from '@/Components/Inputs/SelectInput.vue'
import Button from '@/Components/Elements/Button.vue'
import Message from '@/Components/Elements/Message.vue'
import InputsRow from '@/Components/Inputs/InputsRow.vue'

const props = defineProps({
	settings: Object
})

const form = useForm({
	ig_strategy: props?.settings?.ig_strategy || 'default',
	ig_login: props?.settings?.ig_login || null,
	ig_pass: props?.settings?.ig_pass || null,
	scraper_keys: props?.settings?.scraper_keys ? JSON.parse(props?.settings?.scraper_keys) : [ { key: "", count: null } ]
})

const igStrategyOptions = [
	{
		title: 'default',
		value: 'default'
	}, {
		title: 'Instagram Account',
		value: 'account'
	}, {
		title: 'WebScrapingAPI',
		value: 'webscrapingapi'
	}, {
		title: 'ProxiesAPI',
		value: 'proxiesapi'
	}, {
		title: 'scrape.do',
		value: 'scrapedo'
	}
]

function saveSettings() {
	form.clearErrors()

	form.scraper_keys = form.scraper_keys.filter(k => k.key)

    form.post('/settings', {
		onSuccess: () => toast.success('Settings saved'),
		onError: (e) => {
			toast.error('Update failed')
			console.log(e)
		},
        onFinish: () => form.reset()
    })
}

function addScraperKeyRow() {
	form.scraper_keys.push({
		key: '',
		count: ''
	})
}
function removeScraperKeyRow(i) {
	form.scraper_keys.splice(i, 1)
}
</script>

<template>
	<AuthenticatedLayout header="Settings">
		<Card header="Instagram scraper settings" as="form" @submit.prevent="saveSettings">
			<SelectInput required label="Scraping strategy" horizontal v-model="form.ig_strategy" :options="igStrategyOptions" :error="form.errors.ig_strategy" :tooltip="{
				text: '<strong>default</strong> - uses devices IP to fetch data<br><strong>Instagram Account</strong> - uses devices IP and logs into IG account. May overcome some limitations<br><strong>WebScrapingAPI / ProxiesAPI</strong> - uses scraping API to fetch data',
				width: 'wide'
			}" />
			<div class="line input-note-horizontal" v-if="form.ig_strategy == 'default'">
				<Message type="warning"><strong>WARNING</strong> This strategy uses device IP and may result in your IP being blacklisted on Instagram.</Message>
			</div>
			<div class="line input-note-horizontal" v-if="form.ig_strategy == 'account'">
				<Message type="warning"><strong>WARNING</strong> This strategy may result in your account being banned.</Message>
			</div>
			<template v-if="['webscrapingapi', 'proxiesapi', 'scrapedo'].includes(form.ig_strategy)">
				<InputsRow v-for="(scraperKey, i) in form.scraper_keys" horizontal :label="`Scraper API key${form.scraper_keys.length > 1 ? ` ${i+1}` : ''}`" wrap>
					<TextInput :required="i == 0" v-model="scraperKey.key" class="grow" :chars="34" />
					<InputsRow class="grow">
						<NumberInput v-model="scraperKey.count" class="grow" :min="0" :chars="6" />
						<Button icon="x" v-tooltip="'Delete'" bigIcon color="danger" variant="outline" @click="removeScraperKeyRow(i)" v-if="form.scraper_keys.length > 1" />
					</InputsRow>
				</InputsRow>
				<InputsRow horizontal>
					<Button class="grow" variant="outline" color="link" icon="plus" @click="addScraperKeyRow" full>Add key</Button>
				</InputsRow>
			</template>
			<template v-else-if="form.ig_strategy == 'account'">
				<TextInput required label="Instagram login" horizontal v-model="form.ig_login" :error="form.errors.ig_login" />
				<TextInput required label="Instagram password" type="password" horizontal v-model="form.ig_pass" :error="form.errors.ig_pass" />
			</template>
			<template #buttons>
				<Button :loading="form.processing" type="submit">Save settings</Button>
			</template>
		</Card>
	</AuthenticatedLayout>
</template>