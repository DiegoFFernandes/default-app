<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseAuthService
{
    private $auth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/service-account.json'));
        $this->auth = $factory->createAuth();
    }

    /**
     * Gera um custom token do Firebase para o usuario informado.
     * Usado para autenticar o navegador no Firestore (sincronizacao em tempo real).
     */
    public function customToken($uid)
    {
        return $this->auth->createCustomToken((string) $uid)->toString();
    }
}
