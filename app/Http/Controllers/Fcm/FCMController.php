<?php

namespace App\Http\Controllers\Fcm;

use App\Http\Controllers\Controller;
use App\Models\FMCToken;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FCMController extends Controller
{
    public $request;
    public $fcmToken;
    public $user;

    public function __construct(
        Request $request,
        FMCToken $fcmToken,
        User $user
    ) {
        $this->request = $request;
        $this->fcmToken = $fcmToken;
        $this->user = $user;
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

    public function sendToUser($userId, $title = 'Teste', $body = 'Mensagem enviada do Laravel via Firebase!')
    {
        $tokens = DB::table('fcm_tokens')
            ->where('user_id', $userId)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return ['error' => 'Nenhum token para este usuário'];
        }

        $fcm = new FCMService();
        $results = [];

        foreach ($tokens as $token) {
            return $results[] = $fcm->sendToToken($token, $title, $body);
        }

        return $results;
    }
}
