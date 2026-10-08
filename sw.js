// Service Worker for Control Center v3 (PRJ-2026-0001)
const CACHE_NAME = 'ccv3-cache-v3.0.2';
const STATIC_ASSETS = [
    './index.php',
    './index.php?view=dashboard',
    './index.php?view=print_hub',
    './index.php?view=non_print_hub',
    './index.php?view=commercial',
    './index.php?view=agents',
    './index.php?view=telemetry',
    './00_brand_dna/cyber_design_system.css',
    './manifest.json'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                STATIC_ASSETS.map(url => cache.add(url).catch(err => console.warn('CCv3 Cache asset skip:', url, err)))
            );
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME && key.startsWith('ccv3-cache-')) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Network First with Cache Fallback for dynamic fresh SPA views
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    if (event.request.url.includes('/api/')) return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (response && response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) return cachedResponse;
                    return caches.match('./index.php?view=dashboard');
                });
            })
    );
});
