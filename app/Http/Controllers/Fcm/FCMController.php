<?php

namespace App\Http\Controllers\Fcm;

use App\Http\Controllers\Controller;
use App\Models\FMCToken;
use App\Models\NotificationUsers;
use App\Models\User;
use App\Services\FCMService;
use App\Services\ServiceEstoqueNegativo;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FCMController extends Controller
{
    public $request;
    public $fcmToken;
    public $user;
    public $tipo_notificacao;
    public $serviceEstoqueNegativo;

    public function __construct(
        Request $request,
        FMCToken $fcmToken,
        User $user,
        NotificationUsers $notificationUsers,
        ServiceEstoqueNegativo $serviceEstoqueNegativo
    ) {
        $this->request = $request;
        $this->fcmToken = $fcmToken;
        $this->user = $user;
        $this->tipo_notificacao = $notificationUsers;
        $this->serviceEstoqueNegativo = $serviceEstoqueNegativo;
    }


    public function saveToken()
    {
        $user = Auth::user();

        $notification = $this->request->input('notification', 'N');

        if ($notification === 'S') {
            $this->request->validate(['token' => 'required|string']);
            // Salva o token apenas se o usuário quiser receber notificações
            $this->fcmToken->updateOrCreateToken(
                $user->id,
                $this->request->token,
                'web' // opcional
            );

            //Muda o status do usuário para permitir notificações
            $this->user->updateNotification(['id' => $user->id, 'notifications' => $notification]);

            return response()->json(['message' => 'Notificações ativadas ativadas.']);
        } else {
            //Muda o status do usuário para não permitir notificações
            $this->user->updateNotification(['id' => $user->id, 'notifications' => $notification]);

            return response()->json(['message' => 'Notificações desativadas.']);
        }
    }

    public function sendToUser($tipoNotificacao = 'Estoque')
    {

        // if ($tipoNotificacao === 'Estoque') {
        //     $validaTipoNotificacao = $this->validaTipoNotificacao();
        //     if (isset($validaTipoNotificacao['error'])) {
        //         return $validaTipoNotificacao;
        //     }
        // }

        //valida se o usuario permite notificações e depois pega quais os tipos de notificações ele vai receber
        $users = $this->tipo_notificacao->allListTypeNotificationUsers($tipoNotificacao);

        if (empty($users)) {
            return ['error' => 'Nenhum usuário permite notificações deste tipo'];
        }

        $users_id = $users->pluck('id')->toArray();

        $tokens = DB::table('fcm_tokens')
            ->whereIn('user_id', $users_id)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return ['error' => 'Nenhum token para este usuário'];
        }


        $results = [];

        $fcm = new FCMService();
        
        foreach ($tokens as $token) {
            $results[] = $fcm->sendToToken($token, $users[0]->tipo_notificacao, $users[0]->ds_notificacao);
        }

        return $results;
    }

    public function validaTipoNotificacao()
    {
        //valida se o estaque esta negativo antes de enviar a notificação
        $estoqueNegativo = $this->serviceEstoqueNegativo->EstoqueNegativo();

        if (Helper::is_empty_object($estoqueNegativo)) {
            return ['error' => 'Estoque sem produtos negativos.'];
        }

    }
}
