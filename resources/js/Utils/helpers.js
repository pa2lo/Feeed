export function getUUID(prefix) {
	return `${prefix}-${crypto.randomUUID().split('-').slice(0, -1).join('-')}`
}

export function slugify(string) {
	return string.toLowerCase().trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9\s-]/g, ' ').trim().replace(/[\s-]+/g, '-')
}

export function formatDate(date, seconds = false) {
	if (typeof date == 'number' && date.toString().length == 10) date = date*1000

	let options = {	day: 'numeric',	month: 'numeric', year: 'numeric', hour: '2-digit',	minute: 'numeric' }
	if (seconds) options.second = 'numeric'

	return new Date(date).toLocaleString( 'sk-SK', options)
}

export const roleOptions = [
	{
		title: 'Admin',
		value: 'admin'
	}, {
		title: 'User',
		value: 'user'
	}
]

export function txt(label, def, params) {
	let txt = window?.textLabels?.[label] || (def || label)
	return txt.replace(/\#(\d+)\#/g, (match, index) => params[parseInt(index)] ?? match)
}

export function getHref(link, params) {
	return !params ? link : `${link}/${params}`
}