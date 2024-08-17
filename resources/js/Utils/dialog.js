import { ref, h, render } from "vue"
import { getUUID, txt } from "./helpers"
import Dialogs from "@/Components/Modals/Dialogs.vue"

export const dialogStore = ref([])

let dialogMounted = false

function mountDialog() {
	render(h(Dialogs, {dialogStore: dialogStore.value}), document.body)
	dialogMounted = true
}

function showDialog(type, title, options = {}) {
	if (!dialogMounted) mountDialog()

	dialogStore.value.push({
		id: getUUID('dialog'),
		title,
		type,
		...options
	})
}

export const dialog = {
	// confirms
	confirm: (title, message, options) => showDialog('question', title || txt('Confirmation'), { message, closeable: false, showCancel: true, confirmText: txt('Yes'), ...options }),
	delete: (title, message, options) => showDialog('question', title || txt('Confirmation'), { message, closeable: false, showCancel: true, confirmColor: 'danger', confirmText: txt('Delete'), ...options }),

	// info
	info: (title, message, options) => showDialog('info', title || txt('Info'), { message, ...options }),
	success: (title, message, options) => showDialog('success', title || txt('Success'), { message, ...options }),
	warning: (title, message, options) => showDialog('warning', title || txt('Warning'), { message, ...options }),
	error: (title, message, options) => showDialog('error', title || txt('Error'), { message, ...options }),

	// image
	image: (image, title) => showDialog('image', title, { image })
}

export function destroyDialog(id) {
	let index = dialogStore.value.findIndex(item => item.id == id)
	if (index > -1) dialogStore.value.splice(index, 1)
}