<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import Button from '@/Components/Elements/Button.vue'
import Message from '@/Components/Elements/Message.vue'

const props = defineProps({
    status: String,
})

const form = useForm({})

const submit = () => {
    form.post('/email/verification-notification')
}

const verificationLinkSent = computed(() => props.status === 'verification-link-sent')
</script>

<template>
	<GuestLayout header="Potvrdenie emailovej adresy">
		<p>{{ txt('verifyEmailNote', "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}</p>
		<Message type="success" v-if="verificationLinkSent">
			{{ txt('verificationSentNote', "A new verification link has been sent to the email address you provided during registration.") }}
		</Message>
		<form @submit.prevent="submit" class="line">
			<div>
				<Button full :loading="form.processing" type="submit">{{ txt('Resend Verification Email') }}</Button>
			</div>
			<div class="line margin-bigger divided ta-c">
				<Button	color="link" size="compact" variant="outline" link="logout" icon="logout" method="post" as="button">{{ txt('Log Out') }}</Button>
			</div>
		</form>
	</GuestLayout>
</template>
