<script setup>
defineProps({
	header: String,
	type: {
		type: String,
		default: 'default'
	},
	field: String,
	date: String,
	link: String,
	linkParam: String,
	align: String,
	cellClass: String,
	minWidth: String,
	colClick: Function
})
</script>