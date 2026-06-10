<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: white; padding: 32px; text-align: center; }
        .body { padding: 32px; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #6c757d; }
        .info-box { background: #cfe2ff; border-left: 4px solid #0d6efd; padding: 16px; border-radius: 4px; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px; border-bottom: 1px solid #f0f0f0; }
        td:first-child { color: #666; width: 45%; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:48px">🔄</div>
        <h2 style="margin:8px 0">Renovacion automatica</h2>
        <p style="margin:0">Tu suscripcion se renovo exitosamente</p>
    </div>
    <div class="body">
        <p>Hola <strong>{{ $pagoOnline->user->name }}</strong>,</p>
        <p>Tu suscripcion a <strong>GestionAula</strong> se renovo automaticamente via
            <strong>{{ $pagoOnline->plataforma === 'mercadopago' ? 'MercadoPago' : 'PayPal' }}</strong>.
        </p>

        <div class="info-box">
            <strong>Renovacion procesada automaticamente el {{ now()->format('d/m/Y') }}</strong>
        </div>

        <p><strong>Detalles de la transaccion:</strong></p>
        <table>
            <tr><td>Plataforma</td><td>{{ $pagoOnline->plataforma === 'mercadopago' ? 'MercadoPago' : 'PayPal' }}</td></tr>
            <tr><td>ID transaccion</td><td><strong>{{ $pagoOnline->external_id ?? 'N/A' }}</strong></td></tr>
            <tr><td>Monto debitado</td><td><strong>{{ $pagoOnline->moneda }} {{ number_format($pagoOnline->monto, 2) }}</strong></td></tr>
            <tr><td>Periodo cubierto</td><td>{{ $pagoOnline->periododesde?->format('d/m/Y') }} — {{ $pagoOnline->periodohasta?->format('d/m/Y') }}</td></tr>
            <tr><td>Fecha procesado</td><td>{{ $pagoOnline->fecha_aprobacion?->format('d/m/Y H:i') }}</td></tr>
        </table>

        @if($pagoOnline->suscripcion)
        @php
            $sus = $pagoOnline->suscripcion;
            $periodicidad = $sus->plan?->periodicidad ?? 'mensual';
            $diasAdicionales = match($periodicidad) {
                'trimestral' => 90,
                'anual'      => 365,
                default      => 30,
            };
        @endphp
        <table style="margin-top:8px">
            <tr><td>Proximo vencimiento</td>
                <td><strong>{{ $sus->proximopago?->format('d/m/Y') }}</strong></td></tr>
            <tr><td>Proximo monto</td>
                <td><strong>{{ $pagoOnline->moneda }} {{ number_format($pagoOnline->monto, 2) }}</strong></td></tr>
        </table>
        @endif

        <p style="color:#666;font-size:13px;margin-top:16px">
            Si no reconoces esta transaccion, contactanos de inmediato desde la seccion Contacto.
        </p>
    </div>
    <div class="footer">
        GestionAula &copy; {{ date('Y') }} — Sistema de gestion aulica para docentes
    </div>
</div>
</body>
</html>