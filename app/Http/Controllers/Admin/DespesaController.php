<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comprovante;
use App\Models\ComprovanteFoto;
use App\Models\Pessoa;
use App\Models\User;
use App\Models\Contas;
use App\Models\Veiculo;
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
                'cd_user_lanc'  => $this->user->id,
                'cd_pessoa'     => $input['cd_pessoa'] ?? null,
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

            if (!$this->user->can('ver-status-despesas') && $comprovante->cd_user_lanc !== $this->user->id) {
                return response()->json(['error' => 'Sem permissão para editar este comprovante.'], 403);
            }

            $comprovante->update([
                'cd_pessoa'     => $input['cd_pessoa'] ?? null,
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
        $q = $this->request->get('q', '');
        return response()->json(Veiculo::search($q));
    }

    public function getComprovantes()
    {
        $tipos = Comprovante::tiposDespesa();

        $query = $this->comprovante->with(['fotos', 'user']);

        if (!$this->user->can('ver-status-despesas')) {
            $query->where('cd_user_lanc', $this->user->id);
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

    public function revisarConnectCar()
    {
        $title_page = 'Importar Pedágio — ConnectCar';
        $uri        = $this->request->route()->uri();
        $user_auth  = $this->user;

        return view('admin.despesa.connectcar.revisar',
            compact('title_page', 'uri', 'user_auth'));
    }

    public function batchVeiculosConnectCar()
    {
        $placas = array_values(array_unique(array_filter(
            (array) $this->request->input('placas', [])
        )));

        return response()->json(Veiculo::findByPlacas($placas));
    }

    public function importarConnectCar()
    {
        $headers = json_decode($this->request->input('headers', '[]'), true);
        $rows    = json_decode($this->request->input('rows',    '[]'), true);

        if (empty($rows)) {
            return redirect()->route('despesa.connectcar.revisar')
                ->with('error', 'Nenhum registro recebido para importação.');
        }

        // Índices das colunas no array filtrado (ordem após filtrarColunas)
        // 0 = placa | 1 = data | 2 = tipo transação | 3 = valor
        $IDX_PLACA  = 0;
        $IDX_DATA   = 1;
        $IDX_TIPO   = 2;
        $IDX_VALOR  = 3;

        // 1. Extrai placas únicas e busca veículos no Firebird com UMA query
        $placas   = array_unique(array_filter(array_column($rows, $IDX_PLACA)));
        $veiculos = Veiculo::findByPlacas(array_values($placas)); // mapa placa => dados

        // 2. Persiste cada linha no comprovante
        $importados = 0;
        $erros      = 0;

        foreach ($rows as $row) {
            try {
                $placa     = trim($row[$IDX_PLACA]  ?? '');
                $dtRaw     = trim($row[$IDX_DATA]   ?? '');
                $tipo      = trim($row[$IDX_TIPO]   ?? '');
                $valorRaw  = $row[$IDX_VALOR] ?? 0;

                $veiculo      = $veiculos[$placa] ?? null;
                $cdMotorista  = $veiculo['cd_motorista'] ?? null;

                $valor = abs((float) str_replace(',', '.', $valorRaw));

                // Tenta parsear a data — ConnectCar usa dd/mm/yyyy ou yyyy-mm-dd
                try {
                    $dtDespesa = Carbon::createFromFormat('d/m/Y', $dtRaw)->format('Y-m-d');
                } catch (\Exception) {
                    $dtDespesa = Carbon::parse($dtRaw)->format('Y-m-d');
                }

                $this->comprovante->create([
                    'cd_user_lanc'  => $this->user->id,
                    'tp_despesa'    => 'PED',
                    'nm_solicitante'=> null,
                    'vl_consumido'  => $valor,
                    'ds_observacao' => $tipo,
                    'st_visto'      => 'N',
                    'dt_despesa'    => $dtDespesa,
                    'nr_placa'      => $placa,
                    'km'            => null,
                    'cd_pessoa'     => null,
                ]);

                $importados++;
            } catch (\Exception) {
                $erros++;
            }
        }

        $msg = "Importação concluída: {$importados} registro(s) importado(s).";
        if ($erros > 0) {
            $msg .= " {$erros} linha(s) com erro foram ignoradas.";
        }

        return redirect()->route('despesa.index')->with('success', $msg);
    }

    public function importarConnectCarFirebird()
    {
        $rows = (array) $this->request->input('rows', []);

        if (empty($rows)) {
            return response()->json(['error' => 'Nenhum registro para importar.'], 422);
        }

        try {
            $resultado = Contas::importarLote($rows, [
                'cd_empresa'    => (int)    $this->request->input('cd_empresa'),
                'cd_pessoa'     => (int)    $this->request->input('cd_pessoa'),
                'cd_tipoconta'  => (int)    $this->request->input('cd_tipoconta'),
                'cd_historico'  => (int)    $this->request->input('cd_historico'),
                'cd_formapagto' => (string) $this->request->input('cd_forma_pagto'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Falha na transação Firebird: ' . $e->getMessage(),
            ], 500);
        }

        $importados = $resultado['importados'];
        $erros      = $resultado['erros'];

        return response()->json([
            'success'    => true,
            'importados' => $importados,
            'erros'      => $erros,
            'message'    => $importados . ' registro(s) importado(s) com sucesso.'
                . (count($erros) ? ' ' . count($erros) . ' erro(s).' : ''),
        ]);
    }

    public function searchPessoas()
    {
        $q = trim($this->request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        try {
             $results = (new Pessoa())->FindPessoaJunsoftAll($q);

            return response()->json(array_map(function ($r) {
                return [
                    'id'   => $r->ID,
                    'text' => $r->NM_PESSOA,
                ];
            }, $results));
        } catch (\Exception) {
            return response()->json([]);
        }
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
