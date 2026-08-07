<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\EnderecoPessoa;
use App\Models\Municipio;
use App\Models\Pessoa;
use App\Models\User;
use App\Services\CnpjService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PessoaController extends Controller
{
    public $request, $regiao, $user, $pessoa, $enderecoPessoa, $municipio, $cnpjService;
    public function __construct(
        Request $request,
        User $user,
        Pessoa $pessoa,
        EnderecoPessoa $enderecoPessoa,
        Municipio $municipio,
        CnpjService $cnpjService
    ) {
        $this->request = $request;
        $this->user = $user;
        $this->pessoa = $pessoa;
        $this->enderecoPessoa = $enderecoPessoa;
        $this->municipio = $municipio;
        $this->cnpjService = $cnpjService;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $title_page   = 'Pessoa';
        $user_auth    = $this->user;
        $uri         = $this->request->route()->uri();       
        $user =  $this->user->getData();
        
        return view('admin.usuarios.pessoa', compact(
            'title_page',
            'user_auth',
            'uri',
            'user'
        ));
    }
    public function create()
    {        
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);

        if ($this->pessoa->verifyIfExists($input)) {
            return response()->json(['errors' => 'Pessoa já está vinculada com esse usúario!']);
        };
        $store =  $this->pessoa->storeData($input);
        if ($store) {
            return response()->json(['success' => 'Pessoa vinculada com sucesso!']);
        }
        return response()->json(['errors' => 'Houve algum erro ao vincular!']);
    }
    
    public function _validate($request)
    {
        return $request->validate(
            [
                'cd_usuario'       => 'required|integer',
                'cd_pessoa'     => 'required|integer',
                'nm_pessoa' => 'string',
                'cd_cadusuario'    => 'integer'                               
            ],
            [
                'cd_usuario.required'    => 'Por favor informe um nome.',
                'cd_pessoa.required'    => 'Por favor informe uma pessoa.',
            ]
        );
    }

    public function list()
    {
        // $empresa = $this->empresa->CarregaEmpresa($this->user->conexao);
        // foreach($empresa as $e){
        //     $array[] = $e->CD_EMPRESA;
        // }
        $data = $this->pessoa->showUserPessoa();
        return DataTables::of($data)
            ->addColumn('Actions', function ($data) {
                return '
                <a href="#" class="btn btn-warning btn-xs btn-edit">Editar</a>
                <a href="#" data-id="' . $data->id . '" class="btn btn-danger btn-xs" id="getDeleteId">Excluir</a>';
            })
            ->rawColumns(['Actions'])
            ->make(true);
    }

    public function update()
    {
        $this->request['cd_cadusuario'] = $this->user->id;
        $input = $this->_validate($this->request);
        
        return $this->pessoa->updateData($this->request);
    }

    public function destroy()
    {
        $this->pessoa->destroyData($this->request->id);
        return response()->json(['success' => 'Excluido com sucesso!']);
    }

    public function searchPessoas()
    {
        $q = trim($this->request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        try {
            $results = $this->pessoa->FindPessoaJunsoftAll($q);

            return response()->json(array_map(function ($r) {
                return [
                    'id'        => $r->ID,
                    'text'      => $r->NM_PESSOA,
                    'nr_celular' => $r->NR_CELULAR ?? '',
                ];
            }, $results));
        } catch (\Exception) {
            return response()->json([]);
        }
    }

    /**
     * Consulta um CNPJ/CPF: primeiro no Firebird (se já existir, avisa); para CNPJ
     * novo, busca na BrasilAPI e devolve os campos para preencher o modal (com o
     * município já resolvido pelo código IBGE). CPF é sempre preenchimento manual.
     */
    public function consultarCnpj()
    {
        $cnpj   = trim($this->request->get('cnpj', ''));
        $digits = preg_replace('/\D/', '', $cnpj);

        if (!in_array(strlen($digits), [11, 14], true)) {
            return response()->json(['errors' => 'Informe um CNPJ ou CPF válido.']);
        }

        // Já existe no ERP? (vale para CNPJ e CPF)
        $existente = $this->pessoa->FindPessoaJunsoftId(null, 1, $cnpj);
        if ($existente) {
            return response()->json([
                'exists'  => true,
                'message' => "Este CNPJ/CPF já está cadastrado: #{$existente->CD_PESSOA} - {$existente->PESSOA}.",
            ]);
        }

        // CPF (11 dígitos): a BrasilAPI só consulta CNPJ — preenchimento manual.
        if (strlen($digits) === 11) {
            return response()->json(['found' => false, 'cpf' => true]);
        }

        // CNPJ: busca na BrasilAPI
        $dados = $this->cnpjService->consultar($cnpj);
        if (!$dados) {
            return response()->json(['found' => false]);
        }

        // Resolve o município do ERP pelo código IBGE
        $cdMunicipio = null;
        $dsMunicipio = null;
        if (!empty($dados['cd_ibge'])) {
            $mun = $this->municipio->findByIbge((int) $dados['cd_ibge']);
            if ($mun) {
                $cdMunicipio = $mun->CD_MUNICIPIO;
                $dsMunicipio = $mun->DS_MUNICIPIO . ' - ' . $mun->SG_ESTADO;
            }
        }

        $dados['cd_municipio'] = $cdMunicipio;
        $dados['ds_municipio'] = $dsMunicipio;

        return response()->json(['found' => true, 'data' => $dados]);
    }

    public function searchMunicipio()
    {
        $term = $this->request->get('q', '');

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        return response()->json($this->municipio->search($term));
    }

    public function storePessoa()
    {
        $input = $this->request->validate([
            'nr_cnpjcpf'    => 'required|string|max:22',
            'cd_tipopessoa' => 'required|in:1,2',
            'nm_pessoa'     => 'required|string|max:60',
            'ds_endereco'   => 'nullable|string|max:60',
            'nr_endereco'   => 'nullable|string|max:10',
            'cd_municipio'  => 'nullable|integer',
            'nr_cep'        => 'nullable|string|max:12',
            'ds_bairro'     => 'nullable|string|max:60',
            'nr_fone'       => 'nullable|string|max:15',
            'nr_celular'    => 'nullable|string|max:15',
        ], [
            'nr_cnpjcpf.required'    => 'Informe o CNPJ/CPF.',
            'nm_pessoa.required'     => 'Informe a razão social / nome.',
            'cd_tipopessoa.required' => 'Selecione o tipo (Cliente/Fornecedor).',
        ]);

        // Trava: não cadastra CNPJ/CPF já existente
        if ($this->pessoa->FindPessoaJunsoftId(null, 1, $input['nr_cnpjcpf'])) {
            return response()->json(['errors' => 'Este CNPJ/CPF já está cadastrado.']);
        }

        // OBS.: o driver Firebird deste projeto NÃO suporta rollback transacional
        // (DB::transaction não desfaz e ainda conflita com selects anteriores). Por
        // isso rodamos em autocommit e, se o endereço falhar, desfazemos o PESSOA
        // manualmente (CREDITO é criado pelo trigger TRGAI_PESSOA, apagado antes).
        $db = DB::connection('firebird');

        // Sessão do ERP (empresa/usuário) exigida pelos triggers de PESSOA. Padrão do projeto.
        $db->select("EXECUTE PROCEDURE GERA_SESSAO");

        try {
            $cdPessoa = $this->pessoa->insertPessoa($input);
        } catch (\Exception $e) {
            return response()->json(['errors' => 'Erro ao cadastrar: ' . $e->getMessage()]);
        }

        try {
            $this->enderecoPessoa->store(array_merge($input, ['cd_pessoa' => $cdPessoa]));
        } catch (\Exception $e) {
            // Desfaz o PESSOA para não deixar cadastro sem endereço (best-effort).
            try {
                $db->statement("DELETE FROM CREDITO WHERE CD_PESSOA = ?", [$cdPessoa]);
                $db->statement("DELETE FROM PESSOA  WHERE CD_PESSOA = ?", [$cdPessoa]);
            } catch (\Throwable) {
                // se não conseguir desfazer, ao menos inativa para não aparecer nas buscas
                try {
                    $db->statement("UPDATE PESSOA SET ST_ATIVA = 'N' WHERE CD_PESSOA = ?", [$cdPessoa]);
                } catch (\Throwable) {
                }
            }
            return response()->json(['errors' => 'Erro ao salvar o endereço: ' . $e->getMessage()]);
        }

        return response()->json([
            'success' => 'Cadastro realizado!',
            'id'      => $cdPessoa,
            'text'    => $cdPessoa . ' - ' . mb_strtoupper(trim($input['nm_pessoa']), 'UTF-8'),
        ]);
    }
}
