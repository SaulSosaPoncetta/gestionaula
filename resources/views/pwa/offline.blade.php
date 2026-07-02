<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sin conexión — GestiónAula</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d6efd">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #e3f2fd 0%, #f5f5f5 100%);
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
    }
    .card {
        background: white; border-radius: 16px; padding: 40px;
        text-align: center; max-width: 420px; width: 90%;
        box-shadow: 0 8px 32px rgba(0,0,0,.12);
    }
    .icono { font-size: 64px; margin-bottom: 16px; }
    h1 { font-size: 22px; color: #1565c0; margin-bottom: 8px; }
    p  { color: #666; font-size: 14px; margin-bottom: 24px; line-height: 1.6; }
    .btn {
        display: inline-block; padding: 12px 32px; border-radius: 8px;
        background: #0d6efd; color: white; font-size: 15px;
        text-decoration: none; border: none; cursor: pointer;
        font-weight: bold;
    }
    .btn:hover { background: #0b5ed7; }
    .tip {
        margin-top: 20px; padding: 12px; background: #f0f7ff;
        border-radius: 8px; font-size: 12px; color: #555; text-align: left;
    }
    .tip strong { color: #1565c0; }
</style>
</head>
<body>
<div class="card">
    <div class="icono">📡</div>
    <h1>Sin conexión a internet</h1>
    <p>
        Parece que no tenés conexión en este momento.<br>
        Revisá tu red Wi-Fi o datos móviles y volvé a intentarlo.
    </p>
    <button class="btn" onclick="window.location.reload()">
        🔄 Reintentar
    </button>

    <div class="tip">
        <strong>💡 Consejo:</strong> GestiónAula guarda automáticamente algunas páginas
        para que puedas consultarlas sin internet. Volvé a la app cuando
        tengas conexión para sincronizar tus datos.
    </div>
</div>

<script>
// Cuando vuelva la conexión, redirigir automáticamente
window.addEventListener('online', () => {
    window.location.href = '/dashboard';
});
</script>
</body>
</html>
