<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sin conexión — GestiónAula</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d6efd">
<style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',Arial,sans-serif;background:linear-gradient(135deg,#e3f2fd,#f5f5f5);min-height:100vh;display:flex;align-items:center;justify-content:center}
    .card{background:white;border-radius:20px;padding:48px 40px;text-align:center;max-width:440px;width:92%;box-shadow:0 12px 48px rgba(0,0,0,.12)}
    .emoji{font-size:72px;margin-bottom:20px}
    h1{font-size:22px;color:#1565c0;margin-bottom:10px;font-weight:700}
    p{color:#666;font-size:14px;margin-bottom:24px;line-height:1.7}
    .btn{padding:12px 28px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;background:#0d6efd;color:white}
    .btn:hover{background:#0b5ed7}
    .status{display:inline-flex;align-items:center;gap:8px;margin-top:20px;padding:10px 18px;background:#fff3cd;border-radius:8px;font-size:13px;color:#856404}
    .dot{width:10px;height:10px;border-radius:50%;background:#dc3545;animation:pulse 1.4s infinite}
    .dot.verde{background:#198754;animation:none}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.2}}
</style>
</head>
<body>
<div class="card">
    <div class="emoji">📡</div>
    <h1>Sin conexión a internet</h1>
    <p>GestiónAula necesita conexión para funcionar.<br>
    Revisá tu red y volvé a intentarlo.<br><br>
    La app se <strong>reconecta automáticamente</strong> cuando detecta internet.</p>
    <button class="btn" onclick="reintentar()">🔄 Reintentar</button>
    <div class="status">
        <span class="dot" id="dot"></span>
        <span id="msg">Sin conexión — verificando...</span>
    </div>
</div>
<script>
function reintentar() {
    document.getElementById('msg').textContent = 'Verificando...';
    fetch('/ping?' + Date.now())
        .then(() => {
            document.getElementById('dot').classList.add('verde');
            document.getElementById('msg').textContent = '✅ Conexión recuperada. Cargando...';
            setTimeout(() => { window.location.href = '/dashboard'; }, 1200);
        })
        .catch(() => {
            document.getElementById('msg').textContent = 'Seguís sin conexión. Revisá tu red.';
        });
}
window.addEventListener('online', () => {
    document.getElementById('dot').classList.add('verde');
    document.getElementById('msg').textContent = '✅ Conexión recuperada. Cargando...';
    setTimeout(() => { window.location.href = '/dashboard'; }, 1200);
});
setInterval(reintentar, 30000);
</script>
</body>
</html>
