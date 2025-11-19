<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaEnvio;
use App\Models\Empresa;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    protected Empresa $empresa;
    protected Request $request;
    protected User $user;
    protected AgendaEnvio $envio;

    public function __construct(
        Empresa $empresa,
        Request $request,
        User $user,
        AgendaEnvio $envio
    ) {
        $this->empresa  = $empresa;
        $this->request = $request;
        $this->user = $user;
        $this->envio = $envio;


        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function searchEnvio()
    {
        $title_page  = 'Agenda';
        $user_auth   = $this->user;
        $uri         = $this->request->route()->uri();
        $contexto    = $this->envio->contextoEmail();
        // return $search = $this->envio->searchSend(7369, 201);

        return view("admin.follow-up.index", compact('uri', 'contexto'));
    }

    public function getSearchEnvio(Request $request)
    {
        $search = $this->envio->searchSend($request);

        return DataTables()
            ->of($search)
            ->addColumn('action', function ($row) {
                $btn = '';
                $btn .= '<button class="btn btn-secondary btn-xs ver-email mr-1" 
                    data-nr_contexto="' . $row->NR_CONTEXTO . '" 
                    data-id="' . $row->NR_ENVIO . '" 
                    data-nr_agenda="' . $row->NR_AGENDA . '"
                    aria-hidden="true"><i class="fa fa-envelope" aria-hidden="true"></i></button>';
                $btn .= '<button class="btn btn-secondary btn-xs reenviar-email" data-id="' . $row->NR_ENVIO . '" aria-hidden="true"><i class="fa fa-redo" aria-hidden="true"></i></button>';

                return $btn;
            })
            ->setRowClass(function ($row) {
                return $row->ST_ENVIO == 'F' ? 'bg-info' : '';
            })
            ->rawColumns(['action'])
            ->make(true);


        //             <thead>
        //                 <tr>                    
        //                     <th>Descrição</th>                            
        //                     <th>Nr Agenda</th>                            
        //                     <th style="width: 15%">Nome</th>
        //                     <th>Dt Envio</th>
        //                     <th>Ações</th>                            
        //                 </tr>
        //             </thead>
        //             <tbody>';
        // foreach ($search as $s) {
        //     $exploder        = explode('\\', $s->BI_ANEXORELAT);
        //     $anexo = is_null($s->BI_ANEXORELAT) ? '<button class="btn btn-danger btn-xs">Não Existe</button>' : '<a href="file:///\\172.29.0.9/' . $exploder[2] . '/' . $exploder[3] . '/' . $exploder[4] . '/' . $exploder[5] . '/' . $exploder[6] . '" class="btn btn-xs btn-primary">Anexo</a>';
        //     $email = '<button class="btn btn-default btn-xs ver-email" data-id="' . $s->NR_ENVIO . '" aria-hidden="true"> Ver E-mail </button>';
        //     $reenviar = '<button class="btn btn-warning btn-xs reenviar-email" data-id="' . $s->NR_ENVIO . '" aria-hidden="true"> Reenviar </button>';
        //     //var_dump($exploder);
        //     $html .= '
        //             <tr>                    
        //                 <td>' . $s->DS_CONTEXTO . '</td>                        
        //                 <td>' . $s->NR_AGENDA . '</td>                        
        //                 <td>' . $s->CD_PESSOA . '-' . $s->NM_PESSOA . '</td>
        //                 <td>' . $s->DT_ENVIO . '</td>
        //                 <td>' . $anexo . ' ' . $email . ' ' . $reenviar . '</td>

        //             </tr>';
        // }
        // $html .= '</tbody>
        //         ';
        // return $html;
    }

    public function getEmailEnvio()
    {
        $nr_envio = $this->request->nr_envio;
        $nr_agenda = $this->request->nr_agenda;
        $nr_contexto = $this->request->nr_contexto;

        $email = $this->envio->verEmail($nr_envio, $nr_agenda, $nr_contexto);
        
        return $email;
    }

    public function reenviaFollow()
    {
        $reenvio = $this->envio->reenviaFollow($this->request->nr_envio, $this->request->email);

        if ($reenvio) {
            return response()->json(['success' => 'Reenviado com sucesso, pode demorar até 5 minutos para chegar ao destinatario!']);
        } else {
            return response()->json(['error' => 'Houve algum erro ao reenviar, contate setor de TI!']);
        }
    }
}
