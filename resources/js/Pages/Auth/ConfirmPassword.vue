<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Button from '@/Components/Elements/Button.vue'

const form = useForm({
    password: '',
})

const passwordInput = ref(null)

const submit = () => {
	form.clearErrors()
    form.post('/confirm-password', {
        onFinish: () => {
            form.reset()
            passwordInput.value.focus()
        },
    })
}
</script>

<template>
	<GuestLayout :header="txt('Secured area')">
		<p>{{ txt('secureNote', "This is a secure area of the application. Please confirm your password before continuing.") }}</p>
		<form @submit.prevent="submit" class="line">
			<TextInput
				name="password"
				ref="passwordInput"
				:label="txt('Password')"
				type="password"
				:placeholder="txt('yoursecretpassword')"
				v-model="form.password"
				required
				autofocus
				autocomplete="current-password"
				:error="form.errors.password"
			/>

			<div class="line margin-bigger">
				<Button full :loading="form.processing" type="submit">{{ txt('Confirm') }}</Button>
			</div>
		</form>
	</GuestLayout>
</template>
