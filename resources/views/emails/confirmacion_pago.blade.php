<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #198754, #157347); color: white; padding: 32px; text-align: center; }
        .body { padding: 32px; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
        .success-box { background: #d1e7dd; border-left: 4px solid #198754; padding: 16px; border-radius: 4px; margin: 16px 0; }
        .next-box { background: #cff4fc; border-left: 4px solid #0dcaf0; padding: 16px; border-radius: 4px; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #f0f0f0; }
        td:first-child { color: #666; width: 45%; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:48px">✅</div>
        <h2 style="margin:8px 0">Pago confirmado</h2>
        <p style="margin:0">Tu suscripcion esta activa</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $pago->user->name }}</strong>,</p>
        <p>Tu pago fue procesado correctamente. Gracias por confiar en <strong>GestionAula</strong>.</p>

        <div class="success-box">
            <strong>Pago acreditado el {{ $pago->fechapago->format('d/m/Y') }}</strong>
        </div>

        <p><strong>Detalles del pago:</strong></p>
        <table>
            <tr><td>Comprobante N°</td><td><strong>#{{ $pago->id }}</strong></td></tr>
            <tr><td>Fecha de pago</td><td>{{ $pago->fechapago->format('d/m/Y') }}</td></tr>
            <tr><td>Monto abonado</td><td><strong>${{ number_format($pago->monto, 2) }}</strong></td></tr>
            <tr><td>Periodo cubierto</td><td>{{ $pago->periododesde?->format('d/m/Y') }} — {{ $pago->periodohasta?->format('d/m/Y') }}</td></tr>
            <tr><td>Metodo de pago</td><td>{{ \App\Models\Pago::METODOS[$pago->metodopago] ?? $pago->metodopago ?? 'Online' }}</td></tr>
            <tr><td>Estado</td><td><strong>Pagado ✓</strong></td></tr>
        </table>

        @if($pago->suscripcion)
        <div class="next-box" style="margin-top:20px">
            <strong>Proximo vencimiento:</strong>
            {{ $pago->suscripcion->proximopago?->format('d/m/Y') }}
            — ${{ number_format($pago->suscripcion->montomensual, 2) }}
        </div>
        @endif

        <p style="color:#666;font-size:13px;margin-top:16px">
            Guarda este correo como comprobante de tu pago.
        </p>
    </div>
    <div class="footer">
        GestionAula &copy; {{ date('Y') }} — Sistema de gestion aulica para docentes
    </div>
</div>
</body>
</html>