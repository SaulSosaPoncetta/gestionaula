// ============================================================
// GestiónAula — Service Worker
// Estrategia: Network First con caché de respaldo
// Sincronización en background cuando vuelve la conexión
// ============================================================

const VERSION    = 'gestionaula-v1';
const OFFLINE    = '/offline';

// Recursos que se cachean al instalar (app shell)
const SHELL = [
    '/offline',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
];

// ── INSTALAR ─────────────────────────────────────────────────
self.addEventListener('install', event => {
    console.log('[SW] Instalando versión:', VERSION);
    event.waitUntil(
        caches.open(VERSION)
            .then(cache => cache.addAll(SHELL.map(u => new Request(u, { mode: 'no-cors' }))))
            .then(() => self.skipWaiting())
    );
});

// ── ACTIVAR ───────────────────────────────────────────────────
self.addEventListener('activate', event => {
    console.log('[SW] Activando versión:', VERSION);
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== VERSION).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

// ── INTERCEPTAR PETICIONES ────────────────────────────────────
self.addEventListener('fetch', event => {
    const req = event.request;
    const url = new URL(req.url);

    // Solo GET
    if (req.method !== 'GET') return;

    // No interceptar descargas, exports Excel/PDF ni auth
    const excluir = ['/excel/descargar', '/descarga/', '/storage/exports/'];
    if (excluir.some(p => url.pathname.startsWith(p))) return;

    // No cachear login/logout/registro
    if (['/login','/logout','/register'].includes(url.pathname)) return;

    event.respondWith(networkFirst(req));
});

async function networkFirst(req) {
    const cache = await caches.open(VERSION);
    try {
        // Intentar red
        const resp = await fetch(req);
        if (resp && resp.ok && req.url.startsWith(self.location.origin)) {
            cache.put(req, resp.clone());
        }
        return resp;
    } catch {
        // Sin red: buscar en caché
        const cached = await cache.match(req);
        if (cached) return cached;

        // Si es navegación, mostrar offline
        if (req.mode === 'navigate') {
            return cache.match(OFFLINE) || new Response('Sin conexión', { status: 503 });
        }
        throw new Error('Sin red y sin caché');
    }
}

// ── SINCRONIZACIÓN EN BACKGROUND ─────────────────────────────
// Se dispara cuando el navegador detecta que volvió la conexión
self.addEventListener('sync', event => {
    if (event.tag === 'sync-pendientes') {
        event.waitUntil(sincronizarPendientes());
    }
});

async function sincronizarPendientes() {
    console.log('[SW] Sincronizando datos pendientes...');
    // Notificar a todas las pestañas abiertas que hay conexión
    const clientes = await self.clients.matchAll({ type: 'window' });
    clientes.forEach(cliente => {
        cliente.postMessage({ tipo: 'CONEXION_RECUPERADA' });
    });
}

// Escuchar mensajes desde la app
self.addEventListener('message', event => {
    if (event.data === 'SKIP_WAITING') self.skipWaiting();
});
