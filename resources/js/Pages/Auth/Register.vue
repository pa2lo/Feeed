<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { txt } from '@/Utils/helpers'

import GuestLayout from '@/Layouts/GuestLayout.vue'
import TextInput from '@/Components/Inputs/TextInput.vue'
import Button from '@/Components/Elements/Button.vue'
import SimpleCheckbox from '@/Components/Inputs/SimpleCheckbox.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
})

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
	<GuestLayout :header="txt('Register')">
		<form @submit.prevent="submit" class="line">
			<TextInput
				name="name"
				:label="txt('Name')"
				type="text"
				placeholder="Peter"
				v-model="form.name"
				required
				autofocus
				autocomplete="name"
				:error="form.errors.name"
			/>

			<TextInput
				name="email"
				label="Email"
				type="email"
				:placeholder="txt('your@email')"
				v-model="form.email"
				required
				autocomplete="username"
				:error="form.errors.email"
			/>

			<TextInput
				name="password"
				:label="txt('Password')"
				type="password"
				:placeholder="txt('yoursecretpassword')"
				v-model="form.password"
				required
				autocomplete="new-password"
				:error="form.errors.password"
			/>

			<TextInput
				name="password_confirmation"
				:label="txt('Confirm Password')"
				type="password"
				:placeholder="txt('yoursecretpassword')"
				v-model="form.password_confirmation"
				required
				autocomplete="new-password"
				:error="form.errors.password_confirmation"
			/>

			<SimpleCheckbox class="margin-bigger" name="consent" v-model="form.terms" required>
				{{ txt('I agree to the') }} <a target="_blank" href="/terms">{{ txt('Terms of use') }}</a> {{ txt('and') }} <a target="_blank" href="/policy">{{ txt('Privacy policy') }}</a>
			</SimpleCheckbox>
			<p v-if="form.errors.terms" class="input-note color-error">{{ form.errors.terms }}</p>

			<div class="line margin-bigger">
				<Button full :loading="form.processing" type="submit">{{ txt('Create an account') }}</Button>
			</div>
			<div class="line margin-bigger divided ta-c">
				<Link href="/login">{{ txt('Back to login') }}</Link>
			</div>
		</form>
	</GuestLayout>
</template>
