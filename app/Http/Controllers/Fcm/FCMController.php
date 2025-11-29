<?php

namespace App\Http\Controllers\Fcm;

use App\Http\Controllers\Controller;
use App\Models\FMCToken;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FCMController extends Controller
{
    public $request;
    public $fcmToken;
    public $user;

    public function __construct(Request $request, FMCToken $fcmToken)
    {
        $this->request = $request;
        $this->fcmToken = $fcmToken;
    }


    public function saveToken()
    {
        $this->request->validate(['token' => 'required|string']);
        $user = Auth::user();

        $this->fcmToken->updateOrCreateToken(
            $user->id,
            $this->request->token,
            'web' // opcional
        );

        // Aqui você pode salvar o token no banco de dados ou fazer qualquer outra lógica necessária
        // Por exemplo, associar o token ao usuário autenticado

        return response()->json(['message' => 'Token salvo com sucesso.']);
    }

    public function sendToUser($userId, $title ='Teste', $body = 'Mensagem enviada do Laravel via Firebase!')
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
