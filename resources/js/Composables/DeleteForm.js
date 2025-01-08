import { ref } from "vue"
import { dialog } from "@/Utils/dialog"
import { toast } from "@/Utils/toaster"
import { txt } from "@/Utils/helpers"

const defaultOptions = {
	dialogTitle: txt('Delete item'),
	dialogText: txt('deleteItemQuestion', 'Are you sure you want to delete item?'),
	toastSuccess: txt('Item deleted'),
	toastError: txt('Operation failed'),
	onSuccess: null,
	onError: null
}

export function useDeleteForm() {
	const deletingIDs = ref([])

	function onDeleteError(id, data, options) {
		toast.error(options.toastError)
		console.log(data)
		deletingIDs.value = deletingIDs.value.filter(i => i != id)
	}

	function deleteItem(id, url, deleteOptions) {
		if (!id || !url) return

		const options = Object.assign({}, defaultOptions, deleteOptions)

		dialog.delete(options.dialogTitle, options.dialogText, {
			onConfirm: () => {
				deletingIDs.value.push(id)
				axios.delete(url).then(res => {
					if (res?.data?.success) {
						toast.success(options.toastSuccess)
						options.onSuccess?.(res)
					} else onDeleteError(id, res, options)
				}).catch(error => onDeleteError(id, error, options))
			}
		})
	}

	return { deletingIDs, deleteItem }
}