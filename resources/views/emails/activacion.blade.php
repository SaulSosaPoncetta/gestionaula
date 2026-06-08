<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #198754, #157347); color: white; padding: 40px; text-align: center; }
        .body { padding: 32px; }
        .btn { display: inline-block; background: #198754; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; margin: 20px 0; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
        .check { font-size: 64px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="check">✅</div>
        <h1 style="margin:8px 0">Cuenta activada</h1>
        <p style="margin:0;opacity:0.9">Ya podes usar GestiónAula</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        <p>Tu cuenta en <strong>GestiónAula</strong> fue activada correctamente. Ya podés iniciar sesión y comenzar a usar el sistema.</p>
        <div style="text-align:center">
            <a href="{{ url('/login') }}" class="btn">Iniciar sesión</a>
        </div>
        <hr>
        <p><strong>Tus datos de acceso:</strong></p>
        <table style="width:100%">
            <tr><td style="color:#666;padding:6px">Email</td><td style="padding:6px">{{ $user->email }}</td></tr>
        </table>
        <p style="font-size:13px;color:#666;margin-top:16px">
            Si tenés alguna consulta, no dudes en contactarnos desde la sección Contacto de nuestra web.
        </p>
    </div>
    <div class="footer">
        GestiónAula &copy; {{ date('Y') }} — Sistema de gestión áulica para docentes
    </div>
</div>
</body>
</html>