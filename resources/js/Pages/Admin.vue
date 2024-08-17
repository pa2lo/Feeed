<script setup>
import { ref } from 'vue'
import { toast } from '@/Utils/toaster'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Card from '@/Components/Elements/Card.vue'
import Button from '@/Components/Elements/Button.vue'

const processing = ref(null)

function action(url) {
	processing.value = url
	axios.get(`/admin/${url}`).then(data => {
		toast.success('Success')
		console.log(data)
		processing.value = null
	}).catch(err => {
		toast.error(err?.message || 'Error')
		console.log(err)
		processing.value = null
	})
}
</script>

<template>
	<AuthenticatedLayout header="Admin">
		<Card header="Cache">
			<div class="inputs-grid cols3">
				<div>
					<h5>Config cache</h5>
					<p class="buttons-row mt05">
						<Button class="grow" variant="outline" @click="action('configClear')" :loading="processing == 'configClear'" :disabled="processing && processing != 'configClear'">Clear</Button>
						<Button class="grow" @click="action('configCache')" :loading="processing == 'configCache'" :disabled="processing && processing != 'configCache'">Cache</Button>
					</p>
				</div>
				<div>
					<h5>Route cache</h5>
					<p class="buttons-row mt05">
						<Button class="grow" variant="outline" @click="action('routeClear')" :loading="processing == 'routeClear'" :disabled="processing && processing != 'routeClear'">Clear</Button>
						<Button class="grow" @click="action('routeCache')" :loading="processing == 'routeCache'" :disabled="processing && processing != 'routeCache'">Cache</Button>
					</p>
				</div>
				<div>
					<h5>Views cache</h5>
					<p class="buttons-row mt05">
						<Button class="grow" variant="outline" @click="action('viewClear')" :loading="processing == 'viewClear'" :disabled="processing && processing != 'viewClear'">Clear</Button>
						<Button class="grow" @click="action('viewCache')" :loading="processing == 'viewCache'" :disabled="processing && processing != 'viewCache'">Cache</Button>
					</p>
				</div>
			</div>
		</Card>
	</AuthenticatedLayout>
</template>