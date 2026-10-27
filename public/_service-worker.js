// E-Barokah P2S3 Service Worker
var APP_NAME = 'EBarokah';
var APP_VER = '2.0';
var CACHE_NAME = APP_NAME + '-' + APP_VER;

// Files required to make static assets load fast and reliably
var REQUIRED_FILES = [
	'/assets/styles/style.css',
	'/assets/styles/bootstrap.css',
	'/assets/scripts/custom.js',
	'/assets/scripts/bootstrap.min.js',
	'/assets/fonts/css/fontawesome-all.min.css',
	'/assets/fonts/webfonts/fa-solid-900.woff2',
	'/assets/fonts/webfonts/fa-regular-400.woff2',
	'/assets/fonts/webfonts/fa-brands-400.woff2',
	'/assets/p2s2_fav.png'
];

var APP_DIAG = false;

self.addEventListener('install', function(event) {
	event.waitUntil(
		caches.open(CACHE_NAME)
		.then(function(cache) {
			return cache.addAll(REQUIRED_FILES);
		}).catch(function(error) {
			if(APP_DIAG){console.log('Service Worker Cache: ' + error);}
		})
		.then(function() {
			return self.skipWaiting();
		})
	);
});

self.addEventListener('fetch', function(event) {
	// Bypass cache for dynamic API endpoints & non-GET requests so financial data is always real-time
	if (event.request.method !== 'GET' || event.request.url.includes('/Report/') || event.request.url.includes('/report/')) {
		event.respondWith(fetch(event.request));
		return;
	}

	event.respondWith(
		caches.match(event.request)
			.then(function(response) {
				if (response) { return response; }
				return fetch(event.request);
			})
	);
});

self.addEventListener('activate', function(event) {
	event.waitUntil(self.clients.claim());
	event.waitUntil(
		caches.keys().then(cacheNames => {
			return Promise.all(
				cacheNames
					.filter(cacheName => (cacheName.startsWith(APP_NAME + "-")))
					.filter(cacheName => (cacheName !== CACHE_NAME))
					.map(cacheName => caches.delete(cacheName))
			);
		})
	);
});