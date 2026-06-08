<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; padding: 40px; text-align: center; }
        .body { padding: 32px; }
        .btn { display: inline-block; background: #0d6efd; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; margin: 20px 0; }
        .plan-box { background: #f0f7ff; border: 2px solid #0d6efd; border-radius: 12px; padding: 20px; margin: 20px 0; text-align: center; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; border-radius: 4px; margin: 16px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:48px">🎓</div>
        <h1 style="margin:8px 0">Bienvenido a GestiónAula</h1>
        <p style="margin:0;opacity:0.9">Ya falta poco para empezar</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        <p>Gracias por registrarte en <strong>GestiónAula</strong>. Para completar tu registro y activar tu cuenta, hacé clic en el botón de abajo.</p>

        @if($plan)
        <div class="plan-box">
            <div style="font-size:14px;color:#666;margin-bottom:4px">Plan seleccionado</div>
            <div style="font-size:24px;font-weight:bold;color:#0d6efd">{{ $plan->nombre }}</div>
            <div style="font-size:20px;margin-top:8px">${{ number_format($plan->precio, 2) }} / {{ $plan->periodicidad }}</div>
            @if($plan->descripcion)
            <div style="font-size:13px;color:#666;margin-top:8px">{{ $plan->descripcion }}</div>
            @endif
        </div>
        @endif

        <div style="text-align:center">
            <a href="{{ $activationUrl }}" class="btn">
                Activar mi cuenta
            </a>
        </div>

        <div class="warning">
            <strong>Importante:</strong> Este link es de uso único y expira en 48 horas. Si no creaste esta cuenta, ignorá este mensaje.
        </div>

        <hr>
        <p style="font-size:13px;color:#666">Si el botón no funciona, copiá y pegá este link en tu navegador:</p>
        <p style="font-size:12px;word-break:break-all;color:#0d6efd">{{ $activationUrl }}</p>
    </div>
    <div class="footer">
        GestiónAula &copy; {{ date('Y') }} — Sistema de gestión áulica para docentes
    </div>
</div>
</body>
</html>