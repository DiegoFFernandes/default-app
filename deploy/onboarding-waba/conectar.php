<?php

/**
 * Backend do Cadastro Incorporado - executa o passo que nao pode acontecer no
 * navegador, porque depende do app secret:
 *
 *   1. troca o codigo (valido por 30s) por um token de acesso permanente;
 *   2. descobre a conta (WABA) e o numero conectados, se o navegador nao mandou;
 *   3. inscreve o app na conta e aponta os webhooks dela para o servidor do
 *      cliente (webhook override) - e o que faz cada empresa receber so as
 *      proprias mensagens, mesmo todas usando o mesmo app;
 *   4. devolve o bloco pronto para colar no .env daquela instalacao.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

$cfg = file_exists(__DIR__ . '/config.php') ? require __DIR__ . '/config.php' : null;

if (!$cfg) {
    exit(json_encode(['erro' => 'Falta o config.php no servidor.']));
}

// Mesma trava do index.php - este endpoint age com o app secret.
if (!empty($cfg['senha']) && empty($_SESSION['ok'])) {
    http_response_code(403);
    exit(json_encode(['erro' => 'Sessao expirada. Recarregue a pagina e entre novamente.']));
}

$entrada = json_decode(file_get_contents('php://input'), true) ?: [];

$code    = trim($entrada['code'] ?? '');
$empresa = trim($entrada['empresa'] ?? '');
$webhook = trim($entrada['webhook'] ?? '');
$verify  = trim($entrada['verify'] ?? '');
$wabaId  = trim($entrada['waba_id'] ?? '');
$phoneId = trim($entrada['phone_number_id'] ?? '');

if (!$code || !$empresa || !$webhook || !$verify) {
    exit(json_encode(['erro' => 'Dados incompletos na requisicao.']));
}

$base = 'https://graph.facebook.com/' . $cfg['graph_version'];

// ------------------------------------------------------------------ helpers

function chamar(string $metodo, string $url, array $dados = [], ?string $token = null): array
{
    $ch = curl_init();
    $cabecalhos = ['Accept: application/json'];

    if ($token) {
        $cabecalhos[] = 'Authorization: Bearer ' . $token;
    }

    if ($metodo === 'GET') {
        $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($dados);
    } else {
        $cabecalhos[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    }

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $cabecalhos,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $resposta = curl_exec($ch);
    $erroCurl = curl_error($ch);

    if ($resposta === false) {
        return ['error' => ['message' => 'Falha de conexao: ' . $erroCurl]];
    }

    return json_decode($resposta, true) ?: ['error' => ['message' => 'Resposta invalida da Meta.']];
}

function mensagemErro(array $r): string
{
    return $r['error']['error_user_msg']
        ?? $r['error']['message']
        ?? 'erro desconhecido';
}

function registrar(string $empresa, string $etapa, mixed $conteudo): void
{
    $linha = sprintf(
        "[%s] %s | %s | %s\n",
        date('Y-m-d H:i:s'),
        $empresa,
        $etapa,
        is_string($conteudo) ? $conteudo : json_encode($conteudo, JSON_UNESCAPED_UNICODE)
    );

    @file_put_contents(__DIR__ . '/onboarding.log', $linha, FILE_APPEND);
}

// ------------------------------------------------- 1. codigo -> token

$troca = chamar('GET', $base . '/oauth/access_token', [
    'client_id'     => $cfg['app_id'],
    'client_secret' => $cfg['app_secret'],
    'code'          => $code,
]);

if (empty($troca['access_token'])) {
    registrar($empresa, 'troca-codigo', $troca);
    exit(json_encode(['erro' => 'Nao foi possivel obter o token: ' . mensagemErro($troca)]));
}

$token = $troca['access_token'];
registrar($empresa, 'token-obtido', 'ok');

// -------------------------------- 2. descobre conta/numero, se faltarem
// O navegador normalmente manda os IDs por postMessage, mas se algo se perder
// no caminho da-se pra achar pelo proprio token.

if (!$wabaId) {
    $debug = chamar('GET', $base . '/debug_token', [
        'input_token'  => $token,
        'access_token' => $cfg['app_id'] . '|' . $cfg['app_secret'],
    ]);

    foreach ($debug['data']['granular_scopes'] ?? [] as $escopo) {
        if ($escopo['scope'] === 'whatsapp_business_management' && !empty($escopo['target_ids'][0])) {
            $wabaId = $escopo['target_ids'][0];
            break;
        }
    }
}

if (!$wabaId) {
    registrar($empresa, 'waba-nao-encontrada', $debug ?? []);
    exit(json_encode(['erro' => 'Conexao autorizada, mas nao foi possivel identificar a conta do WhatsApp.']));
}

if (!$phoneId) {
    $numeros = chamar('GET', $base . '/' . $wabaId . '/phone_numbers', [], $token);
    $phoneId = $numeros['data'][0]['id'] ?? '';
}

// --------------------------- 3. inscreve o app e aponta o webhook dela
// A Meta faz o handshake de verificacao na URL informada, entao a instalacao
// do cliente ja precisa estar no ar com este mesmo token de verificacao.

$assinatura = chamar('POST', $base . '/' . $wabaId . '/subscribed_apps', [
    'override_callback_uri' => $webhook,
    'verify_token'          => $verify,
], $token);

$webhookOk = !empty($assinatura['success']);
$aviso     = null;

if (!$webhookOk) {
    $aviso = 'O webhook nao foi configurado: ' . mensagemErro($assinatura)
           . ' Confira se a instalacao do cliente esta no ar e com o mesmo token de verificacao, e refaca por aqui.';
    registrar($empresa, 'webhook-override', $assinatura);
} else {
    registrar($empresa, 'webhook-override', 'ok -> ' . $webhook);
}

// ------------------------------------------------------ 4. resultado

$env = "WHATSAPP_ACCESS_TOKEN={$token}\n"
     . "WHATSAPP_PHONE_NUMBER_ID={$phoneId}\n"
     . "WHATSAPP_WABA_ID={$wabaId}\n"
     . "WHATSAPP_APP_ID={$cfg['app_id']}\n"
     . "WHATSAPP_APP_SECRET={$cfg['app_secret']}\n"
     . "WHATSAPP_VERIFY_TOKEN={$verify}";

registrar($empresa, 'concluido', "waba={$wabaId} phone={$phoneId}");

echo json_encode([
    'webhook_ok' => $webhookOk,
    'aviso'      => $aviso,
    'env'        => $env,
]);
