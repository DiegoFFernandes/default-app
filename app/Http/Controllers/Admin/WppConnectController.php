<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WppDisparo;
use App\Services\WppConnectService;
use Illuminate\Http\JsonResponse;

class WppConnectController extends Controller
{
    public function __construct(private WppConnectService $wpp) {}

    public function index()
    {
        return view('admin.wppconnect.index');
    }

    public function startSession(): JsonResponse
    {
        try {
            $result = $this->wpp->startSession();
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(): JsonResponse
    {
        try {
            $data = $this->wpp->statusSession();
            $connected = $this->wpp->isConnected();

            return response()->json([
                'success'   => true,
                'connected' => $connected,
                'data'      => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'connected' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function qrCode(): JsonResponse
    {
        try {
            $data = $this->wpp->getQrCode();
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function disparos(): JsonResponse
    {
        $disparos = WppDisparo::with('user:id,name')
            ->where('dt_registro', '>=', now()->subDays(10)->startOfDay())
            ->latest('id')
            ->get()
            ->map(fn($d) => [
                'id'          => $d->id,
                'user'        => $d->user?->name ?? '-',
                'phone'       => $d->phone,
                'mensagem'    => $d->mensagem,
                'status'      => $d->status,
                'erro'        => $d->erro ?? '',
                'dt_envio'    => $d->dt_envio?->format('d/m/Y H:i:s') ?? '-',
                'dt_registro' => $d->dt_registro?->format('d/m/Y H:i:s') ?? '-',
            ]);

        return response()->json(['success' => true, 'data' => $disparos]);
    }

    public function reenviar(int $id): JsonResponse
    {
        $disparo = WppDisparo::find($id);

        if (!$disparo || $disparo->status !== WppDisparo::STATUS_FALHA) {
            return response()->json(['errors' => 'Disparo não encontrado ou não está com status de falha.'], 422);
        }

        try {
            $this->wpp->reenviarDisparo($disparo);
            return response()->json(['success' => 'Mensagem reenviada com sucesso!']);
        } catch (\Throwable $e) {
            return response()->json(['errors' => 'Falha ao reenviar: ' . $e->getMessage()], 500);
        }
    }
}
