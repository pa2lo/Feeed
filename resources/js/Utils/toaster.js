import { ref, h, render } from "vue"
import { getUUID } from "./helpers"
import Toaster from "@/Components/Toaster/Toaster.vue"

export const toastStore = ref([])

let toasterMounted = false

function mountToaster() {
	const toasterCont = Object.assign(document.createElement('div'), { className: 'toaster' })

	render(h(Toaster, {toastStore: toastStore.value}), document.body.appendChild(toasterCont))
	toasterMounted = true
}

function showToast(title, type, options = {}) {
	if (!toasterMounted) mountToaster()

	toastStore.value.push({
		id: getUUID('toast'),
		type,
		title,
		...options
	})
}

export const toast = {
	success: (title, options) => showToast(title, 'success', options),
	info: (title, options) => showToast(title, 'info', options),
	error: (title, options) => showToast(title, 'error', options),
	warning: (title, options) => showToast(title, 'warning', options)
}

export function destroyToast(id) {
	let index = toastStore.value.findIndex(item => item.id == id)
	if (index > -1) toastStore.value.splice(index, 1)
}