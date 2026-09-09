<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotasVendedorDivergencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class NotaVendedorDivergenteController extends Controller
{
    protected Request $request;
    protected NotasVendedorDivergencia $notasVendedorDivergencia;

    public function __construct(
        Request $request,
        NotasVendedorDivergencia $notasVendedorDivergencia
    ) {
        $this->request = $request;
        $this->notasVendedorDivergencia = $notasVendedorDivergencia;
    }

    public function index()
    {
        return view('admin.comercial.vendedor-nota.index');
    }

    public function getNotasVendedorDivergentes()
    {
        $dados = $this->notasVendedorDivergencia->getNotasDivergentes();

        return DataTables::of($dados)
            ->addColumn('actions', function ($row) {
                return '<button 
                            title="Editar o vendedor da nota"
                            class="btn btn-success btn-xs editar-vendedor-nota" 
                            data-cd_empresa="' . $row->CD_EMPRESA . '" 
                            data-nr_lancamento="' . $row->NR_LANCAMENTO . '" 
                            data-nr_nota="' . $row->NR_NOTAFISCAL . '" 
                            data-nm_vendedor_nota="' . $row->NM_VEND_NOTA . '"
                            data-nm_pessoa="' . $row->NM_PESSOA . '"
                            style="font-size: 10px;">
                                <i class="fa fa-edit"></i> Editar
                        </button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function substituirItemVendedorNota(Request $request)
    {

        $rules = [
            'notas' => 'required|array|min:1'
        ];

        $messages = [
            'notas.required' => 'Nenhuma nota selecionada. Por favor, selecione pelo menos uma nota para substituir o vendedor.',
            'notas.array' => 'Formato de dados inválido.',
            'notas.min' => 'Selecione pelo menos uma nota para substituir o vendedor.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // return $validator->validated()['notas'];

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }


        try {

            $effect = $this->notasVendedorDivergencia->updateItemVendedorNota($validator->validated()['notas']);

            $collect = collect($effect)->pluck('NR_LANCAMENTO')->values();

            return response()->json(['success' => true, 'message' => 'Vendedor alterado com sucesso nas notas: ' . $collect->implode(', ')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function manterVendedorNota(Request $request)
    {
        $rules = [
            'notas' => 'required|array|min:1'
        ];

        $messages = [
            'notas.required' => 'Nenhuma nota selecionada. Por favor, selecione pelo menos uma nota para manter o vendedor.',
            'notas.array' => 'Formato de dados inválido.',
            'notas.min' => 'Selecione pelo menos uma nota para manter o vendedor.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        try {

            $effect = $this->notasVendedorDivergencia->updateManterVendedorNota($validator->validated()['notas']);

            $sucesso = collect($effect['sucesso'])->pluck('NR_NOTAFISCAL')->values();
            $erros = collect($effect['erros'])->pluck('NR_LANCAMENTO')->values();

            $message = 'Vendedor da nota igualado ao da comissão nas notas: ' . $sucesso->implode(', ');

            if ($erros->isNotEmpty()) {
                $message .= '. Não foi possível alterar os lançamentos: ' . $erros->implode(', ') . '. Verifique se ainda estão divergentes e tente novamente.';
            }

            return response()->json(['success' => $sucesso->isNotEmpty(), 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateAlterarVendedorNota()
    {
        $rules = [
            'cd_empresa' => 'required|integer',
            'nr_lancamento' => 'required|integer',
            'nr_nota' => 'required|integer',
            'cd_vendedor_novo' => 'required|integer'
        ];

        $messages = [
            'cd_empresa.required' => 'Código da empresa é obrigatório.',
            'cd_empresa.integer' => 'Código da empresa deve ser um número inteiro.',
            'nr_lancamento.required' => 'Número de lançamento é obrigatório.',
            'nr_lancamento.integer' => 'Número de lançamento deve ser um número inteiro.',
            'nr_nota.required' => 'Número da nota é obrigatório.',
            'nr_nota.integer' => 'Número da nota deve ser um número inteiro.',
            'cd_vendedor_novo.required' => 'Seleção do novo vendedor é obrigatória.',
            'cd_vendedor_novo.integer' => 'Seleção do novo vendedor deve ser um número inteiro.'
        ];

        $validator = Validator::make($this->request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            $effect = $this->notasVendedorDivergencia->updateVendedorNota($data);

            if ($effect) {
                return response()->json(['success' => true, 'message' => 'Vendedor nota ' . $data['nr_nota'] . ' alterado com sucesso, falta substituir a comissão do vendedor']);
            } else {
                return response()->json(['success' => false, 'message' => 'Nenhuma alteração realizada. Verifique os dados e tente novamente.']);
            }
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
