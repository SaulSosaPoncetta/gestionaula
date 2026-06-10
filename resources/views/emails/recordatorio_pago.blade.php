<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: #ffc107; color: #1a1a2e; padding: 32px; text-align: center; }
        .body { padding: 32px; }
        .btn { display: inline-block; background: #0d6efd; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .info-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; border-radius: 4px; margin: 16px 0; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #f0f0f0; }
        td:first-child { color: #666; width: 40%; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:48px">⏰</div>
        <h2 style="margin:8px 0">Recordatorio de pago</h2>
        <p style="margin:0">Tu suscripcion vence en 7 dias</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $suscripcion->user->name }}</strong>,</p>
        <p>Te recordamos que tu suscripcion a <strong>GestionAula</strong> esta proxima a vencer.</p>

        <div class="info-box">
            <strong>Tu suscripcion vence el {{ $suscripcion->proximopago?->format('d/m/Y') }}</strong>
        </div>

        <table>
            <tr><td>Plan</td><td><strong>{{ $suscripcion->plan?->nombre ?? 'Personalizado' }}</strong></td></tr>
            <tr><td>Monto</td><td><strong>${{ number_format($suscripcion->montomensual, 2) }}</strong></td></tr>
            <tr><td>Periodicidad</td><td>{{ $suscripcion->plan?->periodicidad ?? 'mensual' }}</td></tr>
            <tr><td>Vencimiento</td><td><strong>{{ $suscripcion->proximopago?->format('d/m/Y') }}</strong></td></tr>
        </table>

        <p style="margin-top:24px">Para renovar tu suscripcion, ingresa al sistema y realizá el pago desde la seccion <strong>Mis pagos</strong>.</p>

        <div style="text-align:center;margin:24px 0">
            <a href="{{ url('/mis-pagos') }}" class="btn">
                Renovar suscripcion
            </a>
        </div>

        <p style="color:#666;font-size:13px">Si ya realizaste el pago, ignore este mensaje.</p>
    </div>
    <div class="footer">
        GestionAula &copy; {{ date('Y') }} — Sistema de gestion aulica para docentes
    </div>
</div>
</body>
</html>