/**
 * GestiónAula — Offline Manager
 * =============================================
 * Maneja:
 *  1. IndexedDB local (base de datos del navegador)
 *  2. Cola de operaciones pendientes
 *  3. Sincronización automática al reconectarse
 *  4. UUID para idempotencia
 * =============================================
 */

const OfflineManager = (() => {

    const DB_NAME    = 'gestionaula_offline';
    const DB_VERSION = 1;
    const SYNC_URL   = '/api/sync';
    const BATCH_SIZE = 10; // operaciones por lote

    let db = null;

    // ── Inicializar IndexedDB ────────────────────────────────────────
    async function init() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);

            req.onupgradeneeded = (e) => {
                const idb = e.target.result;

                // Cola de operaciones pendientes
                if (!idb.objectStoreNames.contains('cola')) {
                    const cola = idb.createObjectStore('cola', { keyPath: 'uuid' });
                    cola.createIndex('estado',  'estado',  { unique: false });
                    cola.createIndex('tabla',   'tabla',   { unique: false });
                    cola.createIndex('creado',  'creado',  { unique: false });
                }

                // Cache local de datos
                if (!idb.objectStoreNames.contains('asistencias')) {
                    const as = idb.createObjectStore('asistencias', { keyPath: 'uuid' });
                    as.createIndex('fecha_materia', ['fecha','materia_id'], { unique: false });
                }
                if (!idb.objectStoreNames.contains('calificaciones')) {
                    idb.createObjectStore('calificaciones', { keyPath: 'uuid' });
                }
                if (!idb.objectStoreNames.contains('librotemas')) {
                    idb.createObjectStore('librotemas', { keyPath: 'uuid' });
                }
            };

            req.onsuccess  = (e) => { db = e.target.result; resolve(db); };
            req.onerror    = (e) => reject(e.target.error);
        });
    }

    // ── Generar UUID v4 ──────────────────────────────────────────────
    function uuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
            const r = crypto.getRandomValues(new Uint8Array(1))[0] % 16;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    // ── Guardar operación en la cola ─────────────────────────────────
    async function encolar(tabla, accion, datos) {
        const op = {
            uuid:   datos.uuid || uuid(), // usar el UUID del dato si ya tiene uno
            tabla,
            accion,
            datos,
            estado:  'pendiente',
            creado:  new Date().toISOString(),
            intentos: 0,
        };
        datos.uuid = op.uuid; // asegurar que el dato tenga UUID

        return new Promise((resolve, reject) => {
            const tx    = db.transaction(['cola', tabla], 'readwrite');
            const cola  = tx.objectStore('cola');
            const store = tx.objectStore(tabla);

            cola.put(op);
            if (accion !== 'delete') {
                store.put({ ...datos, _pendiente: true });
            } else {
                store.delete(datos.uuid);
            }

            tx.oncomplete = () => resolve(op);
            tx.onerror    = (e) => reject(e.target.error);
        });
    }

    // ── Guardar (decide si va al server o a la cola) ─────────────────
    async function guardar(tabla, accion, datos) {
        // Asegurar UUID en el dato
        if (!datos.uuid) datos.uuid = uuid();

        if (navigator.onLine) {
            try {
                // Intentar enviar directo al server
                const resp = await fetch(SYNC_URL, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({ operaciones: [{ uuid: datos.uuid, tabla, accion, datos }] }),
                });
                if (resp.ok) {
                    const json = await resp.json();
                    if (json.errores === 0) {
                        // También guardar en IndexedDB como cache
                        await guardarEnCache(tabla, datos);
                        return { origen: 'server', uuid: datos.uuid, ok: true };
                    }
                }
                throw new Error('Server respondió con error');
            } catch {
                // Si falla el server, ir a cola local
                await encolar(tabla, accion, datos);
                return { origen: 'local', uuid: datos.uuid, ok: true, pendiente: true };
            }
        } else {
            // Sin internet: guardar en cola
            await encolar(tabla, accion, datos);
            return { origen: 'local', uuid: datos.uuid, ok: true, pendiente: true };
        }
    }

    async function guardarEnCache(tabla, datos) {
        return new Promise((resolve) => {
            const tx    = db.transaction([tabla], 'readwrite');
            const store = tx.objectStore(tabla);
            store.put({ ...datos, _pendiente: false });
            tx.oncomplete = resolve;
            tx.onerror    = resolve; // no falla si el cache da error
        });
    }

    // ── Obtener pendientes ────────────────────────────────────────────
    async function obtenerPendientes() {
        return new Promise((resolve, reject) => {
            const tx    = db.transaction('cola', 'readonly');
            const idx   = tx.objectStore('cola').index('estado');
            const req   = idx.getAll('pendiente');
            req.onsuccess = (e) => resolve(e.target.result);
            req.onerror   = (e) => reject(e.target.error);
        });
    }

    // ── Marcar como sincronizado ──────────────────────────────────────
    async function marcarSincronizado(uuids) {
        return new Promise((resolve, reject) => {
            const tx   = db.transaction('cola', 'readwrite');
            const cola = tx.objectStore('cola');
            uuids.forEach(u => cola.delete(u));
            tx.oncomplete = resolve;
            tx.onerror    = (e) => reject(e.target.error);
        });
    }

    // ── Sincronizar con el server ─────────────────────────────────────
    async function sincronizar() {
        const pendientes = await obtenerPendientes();
        if (pendientes.length === 0) {
            dispararEvento('sync:nada');
            return;
        }

        console.log(`[Offline] Sincronizando ${pendientes.length} operaciones...`);
        dispararEvento('sync:inicio', { total: pendientes.length });

        let sincronizados = 0;
        let errores       = 0;

        // Enviar en lotes de BATCH_SIZE
        for (let i = 0; i < pendientes.length; i += BATCH_SIZE) {
            const lote = pendientes.slice(i, i + BATCH_SIZE);
            try {
                const resp = await fetch(SYNC_URL, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({
                        operaciones: lote.map(op => ({
                            uuid:   op.uuid,
                            tabla:  op.tabla,
                            accion: op.accion,
                            datos:  op.datos,
                        }))
                    }),
                });

                if (resp.ok) {
                    const json    = await resp.json();
                    const okUuids = json.detalle
                        .filter(d => d.estado === 'ok' || d.estado === 'ya_sincronizado')
                        .map(d => d.uuid);

                    await marcarSincronizado(okUuids);
                    sincronizados += okUuids.length;
                    errores       += json.errores;
                } else {
                    errores += lote.length;
                }
            } catch {
                errores += lote.length;
                break; // sin internet, dejar para después
            }

            // Pequeña pausa entre lotes
            if (i + BATCH_SIZE < pendientes.length) {
                await new Promise(r => setTimeout(r, 300));
            }
        }

        const pendientesRestantes = (await obtenerPendientes()).length;

        dispararEvento('sync:fin', { sincronizados, errores, pendientes: pendientesRestantes });
        console.log(`[Offline] Sync OK: ${sincronizados} sync | ${errores} errores | ${pendientesRestantes} pendientes`);

        return { sincronizados, errores, pendientes: pendientesRestantes };
    }

    // ── Contar pendientes ─────────────────────────────────────────────
    async function contarPendientes() {
        return new Promise((resolve) => {
            const tx  = db.transaction('cola', 'readonly');
            const idx = tx.objectStore('cola').index('estado');
            const req = idx.count('pendiente');
            req.onsuccess = (e) => resolve(e.target.result);
            req.onerror   = () => resolve(0);
        });
    }

    // ── Eventos custom ────────────────────────────────────────────────
    function dispararEvento(nombre, detalle = {}) {
        window.dispatchEvent(new CustomEvent('gestionaula:' + nombre, { detail: detalle }));
    }

    // ── Auto-sync al reconectarse ─────────────────────────────────────
    function iniciarListeners() {
        window.addEventListener('online', async () => {
            console.log('[Offline] Conexión recuperada — sincronizando...');
            const resultado = await sincronizar();
            if (resultado?.sincronizados > 0) {
                dispararEvento('sync:exito', resultado);
            }
        });

        // Actualizar badge de pendientes cada 5 segundos
        setInterval(async () => {
            const n = await contarPendientes();
            actualizarBadge(n);
        }, 5000);
    }

    // ── Badge visual de pendientes ────────────────────────────────────
    function actualizarBadge(cantidad) {
        let badge = document.getElementById('_offline_badge');
        if (!badge) {
            badge = document.createElement('div');
            badge.id = '_offline_badge';
            badge.style.cssText = [
                'position:fixed', 'bottom:70px', 'left:16px', 'z-index:9998',
                'background:#ff9800', 'color:white', 'border-radius:20px',
                'padding:6px 14px', 'font-size:12px', 'font-weight:bold',
                'box-shadow:0 2px 8px rgba(0,0,0,.3)', 'cursor:pointer',
                'display:none', 'align-items:center', 'gap:8px',
            ].join(';');
            badge.innerHTML = '<span>⏳</span><span id="_badge_count"></span>';
            badge.title = 'Datos guardados localmente — click para sincronizar ahora';
            badge.addEventListener('click', () => sincronizar());
            document.body.appendChild(badge);
        }
        const countEl = document.getElementById('_badge_count');
        if (cantidad > 0) {
            badge.style.display     = 'flex';
            countEl.textContent     = `${cantidad} pendiente${cantidad > 1 ? 's' : ''} de sincronizar`;
        } else {
            badge.style.display     = 'none';
        }
    }

    // ── API pública ───────────────────────────────────────────────────
    return {
        init,
        guardar,
        sincronizar,
        contarPendientes,
        uuid,
        obtenerPendientes,
    };

})();

// ── Arrancar al cargar la página ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    try {
        await OfflineManager.init();
        console.log('[Offline] IndexedDB lista');

        // Si hay pendientes al cargar, mostrar badge
        const n = await OfflineManager.contarPendientes();
        if (n > 0) console.log(`[Offline] ${n} operaciones pendientes de sincronizar`);

        // Escuchar eventos de sync
        window.addEventListener('gestionaula:sync:fin', (e) => {
            const { sincronizados, pendientes } = e.detail;
            if (sincronizados > 0 && typeof pwaToast === 'function') {
                pwaToast(
                    `✅ ${sincronizados} registro${sincronizados > 1 ? 's' : ''} sincronizado${sincronizados > 1 ? 's' : ''} con el servidor`,
                    'bg-success'
                );
            }
        });

        // Auto-sync al reconectarse y listeners
        // (initListeners definido abajo, después de que el DOM esté listo)
        window.addEventListener('online', async () => {
            await OfflineManager.sincronizar();
        });

    } catch (e) {
        console.warn('[Offline] No se pudo inicializar IndexedDB:', e);
    }
});
