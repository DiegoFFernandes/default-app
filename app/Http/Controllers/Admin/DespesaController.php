<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comprovante;
use App\Models\ComprovanteFoto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $rules    = [
            'tp_despesa'   => 'required|in:ALI,COM,HOS,PED',
            'vl_consumido' => 'required|numeric|min:0.01',
            'dt_despesa'   => 'required|date',
        ];
        $messages = [
            'tp_despesa.required'   => 'Selecione o tipo de despesa.',
            'tp_despesa.in'         => 'Tipo de despesa inválido.',
            'vl_consumido.required' => 'Informe o valor consumido.',
            'vl_consumido.numeric'  => 'O valor deve ser numérico.',
            'vl_consumido.min'      => 'O valor deve ser maior que zero.',
            'dt_despesa.required'   => 'Informe a data da despesa.',
            'dt_despesa.date'       => 'Data inválida.',
        ];

        if (($input['tp_despesa'] ?? '') === 'COM') {
            $rules['km']       = 'required|integer|min:0';
            $rules['nr_placa'] = 'required|string|max:10';
            $messages['km.required']       = 'Informe o KM do veículo.';
            $messages['km.integer']        = 'KM deve ser um número inteiro.';
            $messages['nr_placa.required'] = 'Informe a placa do veículo.';
        }

        $validator = Validator::make($input, $rules, $messages);

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
                'km'            => ($input['tp_despesa'] === 'COM') ? ($input['km'] ?? null) : null,
                'nr_placa'      => ($input['tp_despesa'] === 'COM') ? ($input['nr_placa'] ?? null) : null,
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

    public function update(int $id)
    {
        $input = $this->request->all();

        $rules    = [
            'tp_despesa'   => 'required|in:ALI,COM,HOS,PED',
            'vl_consumido' => 'required|numeric|min:0.01',
            'dt_despesa'   => 'required|date',
        ];
        $messages = [
            'tp_despesa.required'   => 'Selecione o tipo de despesa.',
            'tp_despesa.in'         => 'Tipo de despesa inválido.',
            'vl_consumido.required' => 'Informe o valor consumido.',
            'vl_consumido.numeric'  => 'O valor deve ser numérico.',
            'vl_consumido.min'      => 'O valor deve ser maior que zero.',
            'dt_despesa.required'   => 'Informe a data da despesa.',
            'dt_despesa.date'       => 'Data inválida.',
        ];

        if (($input['tp_despesa'] ?? '') === 'COM') {
            $rules['km']       = 'required|integer|min:0';
            $rules['nr_placa'] = 'required|string|max:10';
            $messages['km.required']       = 'Informe o KM do veículo.';
            $messages['km.integer']        = 'KM deve ser um número inteiro.';
            $messages['nr_placa.required'] = 'Informe a placa do veículo.';
        }

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $comprovante = $this->comprovante->findOrFail($id);

            if (!$this->user->can('ver-status-despesas') && $comprovante->cd_user !== $this->user->id) {
                return response()->json(['error' => 'Sem permissão para editar este comprovante.'], 403);
            }

            $comprovante->update([
                'tp_despesa'    => $input['tp_despesa'],
                'vl_consumido'  => $input['vl_consumido'],
                'ds_observacao' => $input['ds_observacao'] ?? null,
                'dt_despesa'    => $input['dt_despesa'],
                'km'            => ($input['tp_despesa'] === 'COM') ? ($input['km'] ?? null) : null,
                'nr_placa'      => ($input['tp_despesa'] === 'COM') ? ($input['nr_placa'] ?? null) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar o comprovante: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Comprovante atualizado com sucesso!']);
    }

    public function searchVeiculos()
    {
        $q = strtoupper(trim($this->request->get('q', '')));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        try {
            $rows = DB::connection('firebird')->select("
                SELECT FIRST 20
                    V.NR_PLACA,
                    COALESCE(M.DS_MODELOVEICULO, 'SEM MODELO') DS_MODELO,
                    COALESCE(MARCAVEICULO.DS_MARCAVEICULO, 'SEM MARCA') DS_MARCA
                FROM VEICULO V
                LEFT JOIN MODELOVEICULO M
                       ON M.CD_MODELOVEICULO = V.CD_MODELOVEICULO
                      AND M.CD_MARCAVEICULO  = V.CD_MARCAVEICULO
                LEFT JOIN MARCAVEICULO
                       ON MARCAVEICULO.CD_MARCAVEICULO = V.CD_MARCAVEICULO
                WHERE V.NR_PLACA CONTAINING ?
            ", [$q]);

            $results = array_map(function ($r) {
                $placa = trim($r->nr_placa ?? $r->NR_PLACA);
                $marca = trim($r->ds_marca  ?? $r->DS_MARCA);
                $modelo = trim($r->ds_modelo ?? $r->DS_MODELO);
                return [
                    'id'   => $placa,
                    'text' => $placa . ' — ' . $marca . ' ' . $modelo,
                ];
            }, $rows);

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([]);
        }
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
