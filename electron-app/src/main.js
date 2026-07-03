const { app, BrowserWindow, Menu, Tray, ipcMain, dialog, shell, nativeImage } = require('electron');
const path   = require('path');
const os     = require('os');

// ── Configuración ─────────────────────────────────────────────────────
// Cambiá esta URL por la de tu servidor de producción
const APP_URL    = 'https://tu-dominio.com';
const OFFLINE_PAGE = path.join(__dirname, 'offline.html');

let mainWindow = null;
let tray       = null;
let isOnline   = true;

// ── Ventana principal ─────────────────────────────────────────────────
function createWindow() {
    mainWindow = new BrowserWindow({
        width:  1280,
        height: 800,
        minWidth:  900,
        minHeight: 600,
        title: 'GestiónAula',
        icon: path.join(__dirname, '../assets/icon.png'),
        backgroundColor: '#f0f2f5',
        webPreferences: {
            nodeIntegration:    false,
            contextIsolation:   true,
            preload:            path.join(__dirname, 'preload.js'),
            partition:          'persist:gestionaula',  // Mantiene sesión entre reinicios
        },
        show: false, // Mostrar solo cuando esté lista
    });

    // Mostrar ventana cuando esté lista (evita flash blanco)
    mainWindow.once('ready-to-show', () => {
        mainWindow.show();
        checkConnectivity();
    });

    // Cargar la app
    loadApp();

    // Título dinámico
    mainWindow.on('page-title-updated', (e, title) => {
        e.preventDefault();
        mainWindow.setTitle(title ? `${title} — GestiónAula` : 'GestiónAula');
    });

    // Abrir links externos en navegador
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        shell.openExternal(url);
        return { action: 'deny' };
    });

    mainWindow.on('closed', () => { mainWindow = null; });
}

function loadApp() {
    if (!mainWindow) return;
    if (isOnline) {
        mainWindow.loadURL(APP_URL + '/dashboard')
            .catch(() => loadOffline());
    } else {
        loadOffline();
    }
}

function loadOffline() {
    mainWindow.loadFile(OFFLINE_PAGE);
}

// ── Menú nativo ───────────────────────────────────────────────────────
function buildMenu() {
    const isMac = process.platform === 'darwin';
    const template = [
        ...(isMac ? [{ label: app.name, submenu: [
            { role: 'about', label: 'Acerca de GestiónAula' },
            { type: 'separator' },
            { role: 'services' },
            { role: 'hide' },
            { role: 'quit', label: 'Salir' }
        ]}] : []),
        {
            label: 'Navegación',
            submenu: [
                { label: '🏠 Inicio',          click: () => mainWindow?.loadURL(APP_URL + '/dashboard') },
                { label: '✓  Asistencia',       click: () => mainWindow?.loadURL(APP_URL + '/asistencia') },
                { label: '📝 Calificaciones',   click: () => mainWindow?.loadURL(APP_URL + '/calificaciones') },
                { label: '📚 Contenidos',       click: () => mainWindow?.loadURL(APP_URL + '/contenidos') },
                { label: '📋 Actividades',      click: () => mainWindow?.loadURL(APP_URL + '/actividades') },
                { type: 'separator' },
                { label: '🔄 Recargar',         role: 'reload' },
                { label: '← Atrás',            click: () => mainWindow?.webContents.goBack() },
                { label: '→ Adelante',          click: () => mainWindow?.webContents.goForward() },
            ]
        },
        {
            label: 'Reportes',
            submenu: [
                { label: '🖨 Imprimir PDF',      click: () => mainWindow?.loadURL(APP_URL + '/pdf') },
                { label: '📊 Exportar Excel',   click: () => mainWindow?.loadURL(APP_URL + '/excel') },
                { label: '📈 Cierre de notas',  click: () => mainWindow?.loadURL(APP_URL + '/cierre-cuatri') },
            ]
        },
        {
            label: 'Ver',
            submenu: [
                { role: 'togglefullscreen', label: 'Pantalla completa' },
                { role: 'zoomIn',  label: 'Ampliar',  accelerator: 'CmdOrCtrl+=' },
                { role: 'zoomOut', label: 'Reducir',  accelerator: 'CmdOrCtrl+-' },
                { role: 'resetZoom', label: 'Zoom normal', accelerator: 'CmdOrCtrl+0' },
                { type: 'separator' },
                { role: 'toggleDevTools', label: 'Herramientas de desarrollo' },
            ]
        },
        ...(!isMac ? [{
            label: 'Archivo',
            submenu: [
                { label: 'Salir', accelerator: 'Alt+F4', role: 'quit' }
            ]
        }] : []),
    ];

    Menu.setApplicationMenu(Menu.buildFromTemplate(template));
}

// ── Tray (bandeja del sistema) ────────────────────────────────────────
function createTray() {
    try {
        const iconPath = path.join(__dirname, '../assets/tray.png');
        tray = new Tray(iconPath);
    } catch {
        tray = new Tray(nativeImage.createEmpty());
    }

    tray.setToolTip('GestiónAula');
    const ctxMenu = Menu.buildFromTemplate([
        { label: 'Abrir GestiónAula',  click: () => { mainWindow?.show(); mainWindow?.focus(); } },
        { label: 'Dashboard',           click: () => { mainWindow?.show(); mainWindow?.loadURL(APP_URL + '/dashboard'); } },
        { label: 'Asistencia',          click: () => { mainWindow?.show(); mainWindow?.loadURL(APP_URL + '/asistencia'); } },
        { type: 'separator' },
        { label: 'Salir',               click: () => { tray.destroy(); app.quit(); } },
    ]);
    tray.setContextMenu(ctxMenu);
    tray.on('double-click', () => { mainWindow?.show(); mainWindow?.focus(); });
}

// ── Detección de conectividad ─────────────────────────────────────────
function checkConnectivity() {
    const wasOnline = isOnline;

    require('dns').lookup('google.com', (err) => {
        isOnline = !err;

        if (mainWindow) {
            // Notificar al renderer
            mainWindow.webContents.send('connectivity-change', { online: isOnline });

            // Si volvió la conexión, recargar desde servidor
            if (isOnline && !wasOnline) {
                console.log('[APP] Conexión recuperada — recargando desde servidor');
                loadApp();
            }

            // Si se perdió la conexión y estaba online, mostrar offline
            if (!isOnline && wasOnline) {
                console.log('[APP] Sin conexión — mostrando página offline');
                loadOffline();
            }
        }
    });
}

// Revisar conectividad cada 30 segundos
setInterval(checkConnectivity, 30000);

// ── IPC desde el renderer ────────────────────────────────────────────
ipcMain.handle('get-app-info', () => ({
    version:  app.getVersion(),
    platform: process.platform,
    online:   isOnline,
    appUrl:   APP_URL,
}));

ipcMain.handle('retry-connection', () => {
    checkConnectivity();
    return isOnline;
});

ipcMain.handle('open-external', (e, url) => {
    shell.openExternal(url);
});

// ── Lifecycle ────────────────────────────────────────────────────────
app.whenReady().then(() => {
    createWindow();
    buildMenu();
    createTray();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) createWindow();
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') app.quit();
});

// Prevenir múltiples instancias
const gotLock = app.requestSingleInstanceLock();
if (!gotLock) {
    app.quit();
} else {
    app.on('second-instance', () => {
        if (mainWindow) {
            if (mainWindow.isMinimized()) mainWindow.restore();
            mainWindow.focus();
        }
    });
}
