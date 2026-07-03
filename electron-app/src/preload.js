const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronApp', {
    getInfo:        ()    => ipcRenderer.invoke('get-app-info'),
    retryConnection:()    => ipcRenderer.invoke('retry-connection'),
    openExternal:   (url) => ipcRenderer.invoke('open-external', url),
    onConnectivity: (fn)  => ipcRenderer.on('connectivity-change', (e, data) => fn(data)),
});
