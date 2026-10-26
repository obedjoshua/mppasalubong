// Service Worker for Masaganang Pagpapala Pasalubong
// Enables offline capability for the application

const CACHE_NAME = 'mpp-cache-v5'; // Bumped cache version to force cache invalidation
const OFFLINE_URL = '/offline.html';

// Assets to cache for offline use (Only existing local files)
const STATIC_ASSETS = [
    '/',
    '/shop',
    '/login',
    '/register',
    '/community',
    '/css/welcome.css',
    '/css/loader.css',
    '/css/dashboard.css',
    '/css/orders-index.css',
    '/css/tickets-show.css',
    '/css/login.css',
    '/css/staff/layout.css',
    '/css/staff/dashboard.css',
    '/js/welcome.js',
    '/images/logo.png',
    '/images/default-avatar.jpeg',
    'https://fonts.googleapis.com/css?family=Manrope:wght@500;700;800&display=swap',
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS.map(url => new Request(url, { credentials: 'same-origin' })))
                    .catch(err => console.log('[SW] Some assets failed to cache:', err));
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches (e.g. mpp-cache-v1, v2, v3, v4)
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Skip third-party CDNs (Leaflet, OpenStreetMap, Unpkg) to allow live network fetching
    if (url.origin !== location.origin && !url.hostname.includes('fonts.googleapis.com') && !url.hostname.includes('fonts.gstatic.com')) {
        return;
    }

    // Network-First strategy for HTML page requests and orders-create assets
    if (event.request.mode === 'navigate' || url.pathname.includes('orders-create')) {
        event.respondWith(networkFirst(event.request));
        return;
    }

    if (url.pathname.includes('/images/carousel/')) {
        event.respondWith(networkFirst(event.request));
        return;
    }

    const isStaticAsset = STATIC_ASSETS.some(asset => event.request.url.includes(asset.replace('/', '')));
    const isAPI = url.pathname.startsWith('/api/') || url.pathname.includes('.json');

    if (isStaticAsset) {
        event.respondWith(cacheFirst(event.request));
    } else if (isAPI) {
        event.respondWith(networkFirst(event.request));
    } else {
        event.respondWith(staleWhileRevalidate(event.request));
    }
});

async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse && cachedResponse.ok) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        if (networkResponse.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        if (request.mode === 'navigate') {
            return caches.match(OFFLINE_URL);
        }
        throw error;
    }
}

async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse && cachedResponse.ok) {
            return cachedResponse;
        }
        if (request.mode === 'navigate') {
            return caches.match(OFFLINE_URL);
        }
        throw error;
    }
}

async function staleWhileRevalidate(request) {
    const cachedResponse = await caches.match(request);

    const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse.status === 200) {
            const cache = caches.open(CACHE_NAME);
            cache.then(c => c.put(request, networkResponse.clone()));
        }
        return networkResponse;
    }).catch(() => cachedResponse);

    return (cachedResponse && cachedResponse.ok) ? cachedResponse : fetchPromise;
}

self.addEventListener('message', (event) => {
    if (event.data === 'skipWaiting') self.skipWaiting();
});