// GestiónAula Service Worker v1.0
const CACHE_NAME     = 'gestionaula-v1';
const OFFLINE_URL    = '/offline';

// Recursos a cachear inmediatamente (App Shell)
const ASSETS_CACHE = [
    '/',
    '/offline',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// ── Instalación ───────────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('[SW] Cacheando app shell');
            return cache.addAll(ASSETS_CACHE.map(url => new Request(url, { mode: 'no-cors' })));
        }).then(() => self.skipWaiting())
    );
});

// ── Activación ────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => {
                    console.log('[SW] Eliminando caché viejo:', k);
                    return caches.delete(k);
                })
            )
        ).then(() => self.clients.claim())
    );
});

// ── Interceptar peticiones ────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorar peticiones no-GET, extensiones externas no cacheadas, etc.
    if (request.method !== 'GET') return;
    if (url.origin !== location.origin && !ASSETS_CACHE.includes(request.url)) return;

    // Rutas de API y formularios: siempre red (no cachear)
    if (url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/logout') ||
        url.pathname.startsWith('/register') ||
        url.pathname.startsWith('/excel/') ||
        url.pathname.startsWith('/pdf/') && url.pathname !== '/pdf') {
        event.respondWith(fetchWithOfflineFallback(request));
        return;
    }

    // Estrategia: Network First (intenta red, si falla usa caché)
    event.respondWith(networkFirst(request));
});

// Red primero, caché como respaldo
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);

        // Cachear respuestas exitosas de páginas propias
        if (networkResponse.ok && new URL(request.url).origin === location.origin) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (err) {
        console.log('[SW] Sin red, buscando en caché:', request.url);
        const cached = await caches.match(request);
        if (cached) return cached;

        // Si es una navegación y no hay caché, mostrar página offline
        if (request.mode === 'navigate') {
            const offline = await caches.match(OFFLINE_URL);
            if (offline) return offline;
        }

        throw err;
    }
}

// Petición con fallback offline simple
async function fetchWithOfflineFallback(request) {
    try {
        return await fetch(request);
    } catch (err) {
        if (request.mode === 'navigate') {
            const offline = await caches.match(OFFLINE_URL);
            if (offline) return offline;
        }
        throw err;
    }
}

// ── Notificaciones push (para futuro) ────────────────────────────────
self.addEventListener('push', event => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'GestiónAula', {
            body: data.body || '',
            icon: '/icons/icon-192x192.png',
            badge: '/icons/icon-72x72.png',
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow('/dashboard'));
});
