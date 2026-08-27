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
	}, {
		title: 'Apify Instagram Scraper',
		value: 'apify'
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
		count: '',
		reset: '',
		resetDay: 1,
		defaultCount: '',
	})
}
function removeScraperKeyRow(i) {
	form.scraper_keys.splice(i, 1)
}

const resetOptions = [ { title: 'Monthly', value: 'monthly' }, { title: 'Weekly', value: 'weekly' } ]
const monthDaysOptions = Array.from({length: 31}, (k, v) => v+1).map(i => ({ title: i.toString(), value: i }))
const weekDaysOptions = [
	{ title: 'Monday', value: 1 },
	{ title: 'Tuesday', value: 2 },
	{ title: 'Wednesday', value: 3 },
	{ title: 'Thursday', value: 4 },
	{ title: 'Friday', value: 5 },
	{ title: 'Saturday', value: 6 },
	{ title: 'Sunday', value: 7 }
]
function onResetOptionChange(v, scraperKey) {
	if (v == 'weekly' && scraperKey.resetDay > 7) scraperKey.resetDay = 7
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
			<template v-if="['webscrapingapi', 'proxiesapi', 'scrapedo', 'apify'].includes(form.ig_strategy)">
				<div v-for="(scraperKey, i) in form.scraper_keys" class="line divided">
					<InputsRow horizontal :label="`Scraper API key${form.scraper_keys.length > 1 ? ` ${i+1}` : ''}`" wrap>
						<TextInput :required="i == 0" v-model="scraperKey.key" class="grow" :chars="34" placeholder="key_xxxxxxxxxxxxxxx" />
						<InputsRow class="grow">
							<NumberInput placeholder="Count" v-model="scraperKey.count" class="grow" :min="0" :chars="6" :required="scraperKey.key ? true : false" />
							<Button icon="x" v-tooltip="'Delete'" bigIcon color="danger" variant="outline" @click="removeScraperKeyRow(i)" v-if="form.scraper_keys.length > 1" />
						</InputsRow>
					</InputsRow>
					<InputsRow horizontal :label="`API key${form.scraper_keys.length > 1 ? ` ${i+1}` : ''} reset`" wrap>
						<SelectInput class="grow" :options="resetOptions" placeholder="Don't reset" allowEmpty v-model="scraperKey.reset" @change="(v) => onResetOptionChange(v, scraperKey)" />
						<InputsRow v-if="scraperKey.reset" class="grow">
							<SelectInput class="grow" placeholder="Select day" v-model="scraperKey.resetDay" :options="scraperKey.reset == 'monthly' ? monthDaysOptions : weekDaysOptions" required />
							<NumberInput placeholder="Count" v-model="scraperKey.defaultCount" :min="0" :required="scraperKey.reset ? true : false" :chars="6" />
						</InputsRow>
					</InputsRow>
				</div>
				<InputsRow horizontal class="divided">
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