<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sin conexión — GestiónAula</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d6efd">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(135deg, #e3f2fd 0%, #f5f5f5 100%);
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
    }
    .card {
        background: white; border-radius: 20px; padding: 48px 40px;
        text-align: center; max-width: 440px; width: 92%;
        box-shadow: 0 12px 48px rgba(0,0,0,.12);
    }
    .emoji { font-size: 72px; margin-bottom: 20px; }
    h1 { font-size: 22px; color: #1565c0; margin-bottom: 10px; font-weight: 700; }
    p  { color: #666; font-size: 14px; margin-bottom: 24px; line-height: 1.7; }
    .btn {
        padding: 12px 28px; border-radius: 8px; font-size: 14px;
        font-weight: 600; border: none; cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary  { background: #0d6efd; color: white; }
    .btn-primary:hover { background: #0b5ed7; }
    .badge-conexion {
        display: inline-flex; align-items: center; gap: 8px;
        margin-top: 20px; padding: 10px 18px;
        background: #fff3cd; border-radius: 8px;
        font-size: 13px; color: #856404;
    }
    .punto {
        width: 10px; height: 10px; border-radius: 50%;
        background: #dc3545; animation: pulso 1.4s infinite;
    }
    @keyframes pulso { 0%,100%{opacity:1} 50%{opacity:.2} }
    .punto.verde { background: #198754; animation: none; }
</style>
</head>
<body>
<div class="card">
    <div class="emoji">📡</div>
    <h1>Sin conexión a internet</h1>
    <p>
        GestiónAula necesita conexión para funcionar.<br>
        Revisá tu red y volvé a intentarlo.<br><br>
        La app se <strong>reconecta automáticamente</strong><br>
        cuando detecta internet.
    </p>
    <button class="btn btn-primary" onclick="reintentar()" id="btn-reintentar">
        🔄 Reintentar
    </button>
    <div class="badge-conexion">
        <span class="punto" id="punto"></span>
        <span id="msg">Sin conexión — verificando...</span>
    </div>
</div>

<script>
function reintentar() {
    document.getElementById('msg').textContent = 'Verificando conexión...';
    fetch('/ping?' + Date.now())
        .then(() => {
            document.getElementById('punto').classList.add('verde');
            document.getElementById('msg').textContent = '✅ Conexión recuperada. Cargando...';
            setTimeout(() => { window.location.href = '/dashboard'; }, 1200);
        })
        .catch(() => {
            document.getElementById('msg').textContent = 'Seguís sin conexión. Revisá tu red.';
        });
}

// Automático al volver la conexión
window.addEventListener('online', () => {
    document.getElementById('punto').classList.add('verde');
    document.getElementById('msg').textContent = '✅ Conexión recuperada. Cargando...';
    setTimeout(() => { window.location.href = '/dashboard'; }, 1200);
});

// Reintentar automático cada 30s
setInterval(reintentar, 30000);
</script>
</body>
</html>
