import { ref, toRaw } from "vue"
import { useForm } from "@inertiajs/vue3"

export function useAppForms(defaultData) {
	const newForm = useForm(defaultData)
	const editForm = useForm(defaultData)

	const activeForm = ref({
		type: 'newForm',
		form: {}
	})
	const showModal = ref(false)

	function showNewForm() {
		newForm.reset()
		newForm.clearErrors()
		Object.assign(newForm, structuredClone(defaultData))
		activeForm.value = {
			type: 'newForm',
			form: newForm
		}
		showModal.value = true
	}

	function showEditForm(data) {
		editForm.reset()
		editForm.clearErrors()
		Object.assign(editForm, structuredClone(toRaw(data)))
		activeForm.value = {
			type: 'editForm',
			form: editForm
		}
		showModal.value = true
	}

	return {
		newForm, editForm, activeForm, showModal, showNewForm, showEditForm
	}
}