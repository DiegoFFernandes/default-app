<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IaIntencao;
use App\Models\User;
use App\Models\WppDisparo;
use App\Models\WppLidPendente;
use App\Models\WppParametro;
use App\Services\IAService;
use App\Services\WppConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;

class WppConnectController extends Controller
{
    public function __construct(private WppConnectService $wpp) {}

    public function index()
    {
        $sessions = array_keys(config('services.wppconnect.sessions', []));
        return view('admin.wppconnect.index', compact('sessions'));
    }

    private function resolveWpp(Request $request): WppConnectService
    {
        $key      = $request->input('session');
        $sessions = config('services.wppconnect.sessions', []);

        if ($key && isset($sessions[$key])) {
            return new WppConnectService($sessions[$key]);
        }

        return $this->wpp;
    }

    public function phoneCode(Request $request): JsonResponse
    {
        $key      = $request->input('session');
        $sessions = config('services.wppconnect.sessions', []);
        $session  = ($key && isset($sessions[$key])) ? $sessions[$key] : config('services.wppconnect.session');
        $code     = Cache::get("wppconnect_phone_code_{$session}");
        return response()->json(['code' => $code]);
    }

    public function startSessionPhone(Request $request): JsonResponse
    {
        $request->validate(['phone' => 'required|string|min:10']);

        try {
            $result = $this->resolveWpp($request)->startSessionWithPhone($request->phone);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function closeSession(Request $request): JsonResponse
    {
        try {
            $result = $this->resolveWpp($request)->closeSession();
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logoutSession(Request $request): JsonResponse
    {
        try {
            $result = $this->resolveWpp($request)->logoutSession();
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function startSession(Request $request): JsonResponse
    {
        try {
            $result = $this->resolveWpp($request)->startSession();
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request): JsonResponse
    {
        try {
            $wpp       = $this->resolveWpp($request);
            $data      = $wpp->statusSession();
            $connected = $wpp->isConnected();

            return response()->json([
                'success'   => true,
                'connected' => $connected,
                'data'      => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'connected' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function qrCode(Request $request): JsonResponse
    {
        try {
            $data = $this->resolveWpp($request)->getQrCode();
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

    // -------------------------------------------------------
    // Parametrizações
    // -------------------------------------------------------

    public function parametros(): JsonResponse
    {
        // Garante que a permission existe
        Permission::firstOrCreate(['name' => 'wppconnect-ia', 'guard_name' => 'web']);

        $usuarios = User::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'wpp_lid'])
            ->map(fn($u) => [
                'id'      => $u->id,
                'name'    => $u->name,
                'phone'   => $u->phone,
                'wpp_lid' => $u->wpp_lid ?? '',
                'wpp_ia'  => $u->hasPermissionTo('wppconnect-ia'),
            ]);

        return response()->json([
            'wpp_ia_ativo' => (bool) WppParametro::get('wpp_ia_ativo', '0'),
            'usuarios'     => $usuarios,
        ]);
    }

    public function salvarParametro(Request $request, string $chave): JsonResponse
    {
        $allowed = ['wpp_ia_ativo'];

        if (! in_array($chave, $allowed)) {
            return response()->json(['errors' => 'Parâmetro inválido.'], 422);
        }

        WppParametro::set($chave, $request->boolean('valor') ? '1' : '0');

        return response()->json(['success' => 'Salvo com sucesso!']);
    }

    public function lidsPendentes(): JsonResponse
    {
        $pendentes = WppLidPendente::orderByDesc('updated_at')
            ->get()
            ->map(fn($p) => [
                'lid'          => $p->lid,
                'pushname'     => $p->pushname ?? '—',
                'ultimo_texto' => $p->ultimo_texto ?? '',
                'updated_at'   => $p->updated_at?->format('d/m/Y H:i') ?? '',
            ]);

        $usuarios = User::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        return response()->json(['pendentes' => $pendentes, 'usuarios' => $usuarios]);
    }

    public function associarLid(Request $request): JsonResponse
    {
        $request->validate([
            'lid'     => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::find($request->user_id);
        $user->update(['wpp_lid' => $request->lid]);

        WppLidPendente::where('lid', $request->lid)->delete();

        return response()->json(['success' => "LID associado a {$user->name} com sucesso!"]);
    }

    public function salvarWppLid(int $id, Request $request): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['errors' => 'Usuário não encontrado.'], 404);
        }

        $request->validate([
            'wpp_lid' => ['nullable', 'string', 'max:100', "unique:users,wpp_lid,{$id}"],
        ]);

        $user->update(['wpp_lid' => $request->input('wpp_lid') ?: null]);

        return response()->json(['success' => 'WPP LID salvo com sucesso!', 'wpp_lid' => $user->wpp_lid ?? '']);
    }

    // -------------------------------------------------------
    // Contexto IA
    // -------------------------------------------------------

    public function iaIntencoes(): JsonResponse
    {
        $intencoes = IaIntencao::orderBy('id')->get(['id', 'nome', 'descricao', 'ativo', 'sql_template']);
        return response()->json(['data' => $intencoes]);
    }

    public function toggleIntencao(int $id): JsonResponse
    {
        $intencao = IaIntencao::find($id);

        if (! $intencao) {
            return response()->json(['errors' => 'Intenção não encontrada.'], 404);
        }

        $intencao->update(['ativo' => ! $intencao->ativo, 'updated_at' => now()]);

        $msg = $intencao->ativo
            ? "Intenção '{$intencao->nome}' ativada."
            : "Intenção '{$intencao->nome}' desativada.";

        return response()->json(['success' => $msg, 'ativo' => $intencao->ativo]);
    }

    public function previaIntencao(int $id, Request $request): JsonResponse
    {
        $intencao = IaIntencao::find($id);

        if (! $intencao) {
            return response()->json(['errors' => 'Intenção não encontrada.'], 404);
        }

        if (! $intencao->sql_template) {
            return response()->json(['errors' => 'Esta intenção não tem SQL configurado.'], 422);
        }

        $dtInicio = $request->input('dt_inicio')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('dt_inicio'))->format('d.m.Y')
            : now()->startOfMonth()->format('d.m.Y');

        $dtFim = $request->input('dt_fim')
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('dt_fim'))->format('d.m.Y')
            : now()->format('d.m.Y');

        $texto = app(IAService::class)->previa($intencao, $dtInicio, $dtFim);

        return response()->json([
            'texto'     => $texto,
            'dt_inicio' => $dtInicio,
            'dt_fim'    => $dtFim,
        ]);
    }

    public function salvarSqlIntencao(int $id, Request $request): JsonResponse
    {
        $intencao = IaIntencao::find($id);

        if (! $intencao) {
            return response()->json(['errors' => 'Intenção não encontrada.'], 404);
        }

        $request->validate([
            'sql_template' => ['nullable', 'string'],
        ]);

        $intencao->update([
            'sql_template' => $request->input('sql_template') ?: null,
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => "SQL da intenção '{$intencao->nome}' salvo com sucesso!"]);
    }

    public function toggleWppIa(int $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['errors' => 'Usuário não encontrado.'], 404);
        }

        Permission::firstOrCreate(['name' => 'wppconnect-ia', 'guard_name' => 'web']);

        if ($user->hasPermissionTo('wppconnect-ia')) {
            $user->revokePermissionTo('wppconnect-ia');
            $ativo = false;
        } else {
            $user->givePermissionTo('wppconnect-ia');
            $ativo = true;
        }

        return response()->json([
            'success' => $ativo ? "{$user->name} habilitado para receber IA via WhatsApp." : "{$user->name} removido do acesso.",
            'wpp_ia'  => $ativo,
        ]);
    }
}
