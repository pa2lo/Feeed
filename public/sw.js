// self.addEventListener('fetch', function() {
//     return;
// });
self.addEventListener('install', function(event) {
	event.waitUntil(self.skipWaiting());
});
self.addEventListener('activate', () => {
	console.log('sw activate');
	clients.claim();
});