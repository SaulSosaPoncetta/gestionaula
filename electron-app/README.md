# GestiónAula Desktop — App de Escritorio

Aplicación de escritorio para Windows basada en Electron que envuelve
el sistema web GestiónAula con soporte offline y sincronización automática.

---

## ⚡ Requisitos previos

- **Node.js 18+** → https://nodejs.org
- **npm** (viene con Node.js)
- Acceso a internet para la primera instalación de dependencias

---

## 🔧 Configuración antes de compilar

Editá el archivo `src/main.js` y cambiá la URL del servidor:

```js
// Línea 7 — cambiá por tu dominio real:
const APP_URL = 'https://tu-dominio.com';
```

---

## 📦 Pasos para compilar el instalador (.exe)

Abrí PowerShell en la carpeta del proyecto y ejecutá:

```powershell
# 1. Instalar dependencias
npm install

# 2. Compilar instalador para Windows 64-bit
npm run build:win
```

El instalador se genera en la carpeta `dist/`:
```
dist/
  GestiónAula Setup 1.0.0.exe   ← Este es el instalador
```

---

## 🖼 Íconos (opcional pero recomendado)

Colocá tus íconos en la carpeta `assets/`:

| Archivo        | Tamaño   | Uso                    |
|----------------|----------|------------------------|
| `icon.ico`     | 256x256  | Windows (instalador)   |
| `icon.png`     | 512x512  | Taskbar / menú         |
| `tray.png`     | 16x16    | Bandeja del sistema    |

Si no tenés íconos propios, podés generar uno en:
https://www.favicon-generator.org o https://cloudconvert.com/png-to-ico

---

## 🚀 Probar sin compilar

```powershell
npm start
```

---

## 📋 Funcionalidades

- ✅ Carga el sistema web GestiónAula en ventana nativa
- ✅ Mantiene la sesión iniciada entre reinicios
- ✅ Detecta pérdida de conexión y muestra página offline
- ✅ Se reconecta automáticamente cuando vuelve internet
- ✅ Menú nativo con accesos directos a cada módulo
- ✅ Ícono en bandeja del sistema (tray)
- ✅ Una sola instancia a la vez
- ✅ Atajos de teclado nativos

---

## 📁 Estructura

```
electron-app/
├── src/
│   ├── main.js        ← Proceso principal
│   ├── preload.js     ← Bridge seguro main ↔ renderer
│   └── offline.html   ← Página sin conexión
├── assets/
│   ├── icon.ico       ← Ícono Windows
│   ├── icon.png       ← Ícono general
│   └── tray.png       ← Ícono bandeja
├── package.json
└── README.md
```

---

## 🔄 Distribución

Una vez generado el `.exe`, colocalo en tu servidor en:
```
public/descarga/GestionAula-Setup.exe
```

La landing page ya tiene el botón de descarga apuntando a esa URL.






Para generar el instalador .exe:

Editá src/main.js línea 7: cambiá https://tu-dominio.com por tu URL real
Abrí PowerShell en la carpeta electron-app/:

powershellnpm install
npm run build:win

El instalador queda en dist/GestiónAula Setup 1.0.0.exe
Copiá ese .exe a public/descarga/GestionAula-Setup.exe en tu servidor Laravel
El botón "Descargar para Windows" de la landing ya apunta a esa URL


Editá src/main.js línea 7: cambiá https://tu-dominio.com por tu URL real
Abrí PowerShell en la carpeta electron-app/:

ejecutar
npm install
npm run build:win