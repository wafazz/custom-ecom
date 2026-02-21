const CACHE_VERSION = 'shaniena-v1';
const PRECACHE = CACHE_VERSION + '-precache';
const RUNTIME = CACHE_VERSION + '-runtime';

const PRECACHE_URLS = [
    '/offline.html',
    '/assets/ecom/css/bootstrap.min.css',
    '/assets/ecom/css/style.css',
    '/assets/ecom/css/elegant-icons.css',
    '/assets/ecom/css/slicknav.min.css',
    '/assets/ecom/css/magnific-popup.css',
    '/assets/ecom/css/owl.carousel.min.css',
    '/assets/ecom/css/jquery-ui.min.css',
    '/assets/ecom/js/jquery-3.3.1.min.js',
    '/assets/ecom/js/bootstrap.min.js',
    '/assets/ecom/js/main.js',
    '/assets/images/r-web-logo.png',
    '/assets/images/LOGO-ROZYANA-06-2.png'
];

// URLs that should never be cached
const NETWORK_ONLY_PATTERNS = [
    '/live_visitors.json',
    'senangpay.com',
    'bayarcash.com',
    'stripe.com/v3',
    'js.stripe.com'
];

// Static asset paths — cache-first
const STATIC_ASSET_PATTERNS = [
    '/assets/ecom/css/',
    '/assets/ecom/js/',
    '/assets/ecom/img/',
    '/assets/ecom/fonts/',
    '/assets/images/products/'
];

// CDN domains — cache-first
const CDN_DOMAINS = [
    'fonts.googleapis.com',
    'fonts.gstatic.com',
    'cdnjs.cloudflare.com',
    'cdn.jsdelivr.net',
    'code.jquery.com'
];

// Install: pre-cache core assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(PRECACHE)
            .then(cache => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// Activate: clean up old caches
self.addEventListener('activate', event => {
    const currentCaches = [PRECACHE, RUNTIME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => !currentCaches.includes(name))
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: routing strategies
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // Network-only: POST requests
    if (event.request.method !== 'GET') return;

    // Network-only: payment gateways, live data
    if (NETWORK_ONLY_PATTERNS.some(p => event.request.url.includes(p))) return;

    // Cache-first: static assets (local)
    if (STATIC_ASSET_PATTERNS.some(p => url.pathname.includes(p))) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // Cache-first: CDN resources
    if (CDN_DOMAINS.some(d => url.hostname.includes(d))) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // Network-first: navigation requests (HTML pages)
    if (event.request.mode === 'navigate') {
        event.respondWith(networkFirstNav(event.request));
        return;
    }

    // Default: network-first for everything else
    event.respondWith(networkFirst(event.request));
});

// Cache-first strategy
function cacheFirst(request) {
    return caches.match(request).then(cached => {
        if (cached) return cached;
        return fetch(request).then(response => {
            if (response.ok) {
                const clone = response.clone();
                caches.open(RUNTIME).then(cache => cache.put(request, clone));
            }
            return response;
        });
    });
}

// Network-first for navigation (with offline fallback)
function networkFirstNav(request) {
    return fetch(request)
        .then(response => {
            if (response.ok) {
                const clone = response.clone();
                caches.open(RUNTIME).then(cache => cache.put(request, clone));
            }
            return response;
        })
        .catch(() => {
            return caches.match(request).then(cached => {
                return cached || caches.match('/offline.html');
            });
        });
}

// Network-first for other GET requests
function networkFirst(request) {
    return fetch(request)
        .then(response => {
            if (response.ok) {
                const clone = response.clone();
                caches.open(RUNTIME).then(cache => cache.put(request, clone));
            }
            return response;
        })
        .catch(() => caches.match(request));
}
