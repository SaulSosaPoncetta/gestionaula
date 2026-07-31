// GestiónAula Service Worker v1.0
const CACHE = 'gestionaula-v1';
const OFFLINE = '/offline';

const SHELL = [
    '/offline',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
];

// ── Instalar ─────────────────────────────────────────────────────────
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE)
            .then(c => c.addAll(SHELL.map(u => new Request(u, { mode: 'no-cors' }))))
            .then(() => self.skipWaiting())
    );
});

// ── Activar ──────────────────────────────────────────────────────────
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

// ── Interceptar peticiones ────────────────────────────────────────────
self.addEventListener('fetch', e => {
    const req = e.request;
    const url = new URL(req.url);

    // Solo GET
    if (req.method !== 'GET') return;

    // No interceptar descargas ni auth
    const skip = ['/excel/descargar', '/descarga/', '/login', '/logout', '/register'];
    if (skip.some(p => url.pathname.startsWith(p))) return;

    e.respondWith(networkFirst(req));
});

async function networkFirst(req) {
    const cache = await caches.open(CACHE);
    try {
        const resp = await fetch(req);
        if (resp && resp.ok && req.url.startsWith(self.location.origin)) {
            cache.put(req, resp.clone());
        }
        return resp;
    } catch {
        const cached = await cache.match(req);
        if (cached) return cached;
        if (req.mode === 'navigate') {
            return cache.match(OFFLINE) || new Response('Sin conexión', { status: 503 });
        }
        throw new Error('Sin red y sin caché');
    }
}

// ── Sincronización cuando vuelve la conexión ─────────────────────────
self.addEventListener('sync', e => {
    if (e.tag === 'sync-pendientes') {
        e.waitUntil(
            self.clients.matchAll({ type: 'window' }).then(clients => {
                clients.forEach(c => c.postMessage({ tipo: 'CONEXION_RECUPERADA' }));
            })
        );
    }
});

self.addEventListener('message', e => {
    if (e.data === 'SKIP_WAITING') self.skipWaiting();
});
