<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\HubEstadoClienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookEstadoClienteController extends Controller
{
    public function __invoke(Request $request, HubEstadoClienteService $hub)
    {
        $payload = $request->all();
        $firmaRecibida = $request->header('X-MiGestion-Signature', '');
        $firmaEsperada = hash_hmac('sha256', json_encode($payload), config('migestion_hub.webhook_secret'));

        if (! $firmaEsperada || ! hash_equals($firmaEsperada, $firmaRecibida)) {
            Log::warning('Webhook del hub con firma inválida', ['payload' => $payload]);

            return response()->json(['error' => 'firma inválida'], 401);
        }

        $request->validate([
            'referencia_externa' => 'required|string',
            'estado' => 'required|in:activa,vencida,suspendida,cancelada',
        ]);

        $user = User::find($request->referencia_externa);

        if (! $user) {
            Log::warning('Webhook del hub para usuario inexistente', ['payload' => $payload]);

            return response()->json(['error' => 'usuario no encontrado'], 404);
        }

        $hub->aplicarEstado($user, $request->estado);

        return response()->json(['ok' => true]);
    }
}