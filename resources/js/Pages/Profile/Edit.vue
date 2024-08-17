<script setup>
import { txt } from '@/Utils/helpers'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue'
</script>

<template>
	<AuthenticatedLayout :header="txt('Profile')" width="narrow">
		<UpdateProfileInformationForm :user="$page.props.auth.user" />
		<UpdatePasswordForm />
		<DeleteUserForm />
	</AuthenticatedLayout>
</template>
