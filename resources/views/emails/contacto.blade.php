<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: #0d6efd; color: white; padding: 32px; text-align: center; }
        .body { padding: 32px; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
        .badge { background: #e9f5ff; color: #0d6efd; padding: 4px 12px; border-radius: 20px; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>🎓 GestiónAula</h2>
        <p style="margin:0;opacity:0.9">Confirmación de contacto</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $nombre }}</strong>,</p>
        <p>Recibimos tu mensaje correctamente. Nos pondremos en contacto contigo a la brevedad.</p>
        <hr>
        <p><strong>Resumen de tu consulta:</strong></p>
        <table style="width:100%;border-collapse:collapse">
            <tr><td style="padding:8px;color:#666">Nombre</td><td style="padding:8px">{{ $nombre }}</td></tr>
            <tr style="background:#f8f9fa"><td style="padding:8px;color:#666">Email</td><td style="padding:8px">{{ $email }}</td></tr>
            <tr><td style="padding:8px;color:#666">Teléfono</td><td style="padding:8px">{{ $telefono ?: 'No informado' }}</td></tr>
            <tr style="background:#f8f9fa"><td style="padding:8px;color:#666">Mensaje</td><td style="padding:8px">{{ $mensaje }}</td></tr>
        </table>
    </div>
    <div class="footer">
        GestiónAula &copy; {{ date('Y') }} — Sistema de gestión áulica para docentes
    </div>
</div>
</body>
</html>