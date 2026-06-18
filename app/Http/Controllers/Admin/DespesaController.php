<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comprovante;
use App\Models\ComprovanteFoto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DespesaController extends Controller
{
    protected Request $request;
    protected User $user;
    protected Comprovante $comprovante;
    protected ComprovanteFoto $comprovanteFoto;

    public function __construct(
        Request $request,
        Comprovante $comprovante,
        ComprovanteFoto $comprovanteFoto
    ) {
        $this->request         = $request;
        $this->comprovante     = $comprovante;
        $this->comprovanteFoto = $comprovanteFoto;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page       = 'Adiantamento para Despesas';
        $user_auth        = $this->user;
        $uri              = $this->request->route()->uri();
        $canStatusDespesas = $this->user->can('ver-status-despesas');

        return view('admin.despesa.despesa', compact('uri', 'title_page', 'user_auth', 'canStatusDespesas'));
    }

    public function store()
    {
        $input = $this->request->all();

        $validator = Validator::make($input, [
            'tp_despesa'   => 'required|in:ALI,COM,HOS,PED',
            'vl_consumido' => 'required|numeric|min:0.01',
            'dt_despesa'   => 'required|date',
        ], [
            'tp_despesa.required'   => 'Selecione o tipo de despesa.',
            'tp_despesa.in'         => 'Tipo de despesa inválido.',
            'vl_consumido.required' => 'Informe o valor consumido.',
            'vl_consumido.numeric'  => 'O valor deve ser numérico.',
            'vl_consumido.min'      => 'O valor deve ser maior que zero.',
            'dt_despesa.required'   => 'Informe a data da despesa.',
            'dt_despesa.date'       => 'Data inválida.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $comprovante = $this->comprovante->create([
                'cd_user'       => $this->user->id,
                'tp_despesa'    => $input['tp_despesa'],
                'vl_consumido'  => $input['vl_consumido'],
                'ds_observacao' => $input['ds_observacao'] ?? null,
                'st_visto'      => 'N',
                'dt_despesa'    => $input['dt_despesa'],
            ]);

            if ($this->request->hasFile('fotos')) {
                foreach ($this->request->file('fotos') as $foto) {
                    $path = $foto->store("comprovantes/{$comprovante->id}", 'public');
                    $this->comprovanteFoto->create([
                        'id_comprovante' => $comprovante->id,
                        'path'           => $path,
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar o comprovante: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Comprovante registrado com sucesso!']);
    }

    public function getComprovantes()
    {
        $tipos = Comprovante::tiposDespesa();

        $query = $this->comprovante->with(['fotos', 'user']);

        if (!$this->user->can('ver-status-despesas')) {
            $query->where('cd_user', $this->user->id);
        }

        $data = $query
            ->orderByDesc('dt_despesa')
            ->get()
            ->map(function ($c) use ($tipos) {
                return [
                    'id'            => $c->id,
                    'nm_usuario'    => $c->user->name ?? '-',
                    'tp_despesa'    => $c->tp_despesa,
                    'nm_despesa'    => $tipos[$c->tp_despesa] ?? $c->tp_despesa,
                    'vl_consumido'  => number_format($c->vl_consumido, 2, ',', '.'),
                    'ds_observacao' => $c->ds_observacao ?? '-',
                    'st_visto'      => $c->st_visto,
                    'dt_despesa'    => Carbon::parse($c->dt_despesa)->format('d/m/Y'),
                    'fotos'         => $c->fotos->map(fn($f) => asset('storage/' . $f->path))->values(),
                    'created_at'    => $c->created_at->format('d/m/Y H:i'),
                ];
            });

        return datatables()->of($data)->toJson();
    }

    public function toggleVisto(int $id)
    {
        if (!$this->user->can('ver-status-despesas')) {
            return response()->json(['error' => 'Sem permissão.'], 403);
        }

        $comprovante = $this->comprovante->findOrFail($id);
        $novoStatus  = $comprovante->st_visto === 'S' ? 'N' : 'S';
        $comprovante->update(['st_visto' => $novoStatus]);

        return response()->json(['success' => true, 'st_visto' => $novoStatus]);
    }
}
