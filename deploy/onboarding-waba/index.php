<?php

/**
 * Cadastro Incorporado (Embedded Signup) com coexistencia.
 *
 * Roda fora do ERP, num subdominio proprio da DF, porque:
 *  - o onboarding e feito uma vez por cliente, centralizado aqui;
 *  - a Meta exige HTTPS e o dominio na whitelist do app;
 *  - o cliente ve esta URL na hora de autorizar o acesso a conta dele.
 *
 * Nao depende do Laravel nem de banco - so PHP com curl.
 */

session_start();

$cfg = file_exists(__DIR__ . '/config.php')
    ? require __DIR__ . '/config.php'
    : null;

if (!$cfg) {
    exit('Falta o config.php. Copie o config.exemplo.php e preencha os valores.');
}

// --------------------------------------------------------------- trava
// Esta pagina executa acoes privilegiadas com o app secret, entao nao pode
// ficar aberta na internet.
if (!empty($cfg['senha'])) {
    if (isset($_POST['senha']) && hash_equals($cfg['senha'], $_POST['senha'])) {
        $_SESSION['ok'] = true;
    } elseif (isset($_POST['senha'])) {
        $erroSenha = 'Senha incorreta.';
    }

    if (empty($_SESSION['ok'])) {
        ?>
        <!doctype html>
        <html lang="pt-BR"><head><meta charset="utf-8"><title>Acesso</title>
        <style>
          body{font-family:system-ui,sans-serif;background:#f4f6f8;display:flex;height:100vh;
               align-items:center;justify-content:center;margin:0}
          form{background:#fff;padding:32px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);width:300px}
          input{width:100%;padding:9px;margin:10px 0;box-sizing:border-box;border:1px solid #ced4da;border-radius:4px}
          button{width:100%;padding:10px;background:#25D366;color:#fff;border:0;border-radius:4px;cursor:pointer}
          .erro{color:#c00;font-size:13px;margin:0}
        </style></head><body>
        <form method="post">
          <h3 style="margin-top:0">Onboarding WhatsApp</h3>
          <?php if (!empty($erroSenha)) { echo '<p class="erro">' . htmlspecialchars($erroSenha) . '</p>'; } ?>
          <input type="password" name="senha" placeholder="Senha" autofocus required>
          <button type="submit">Entrar</button>
        </form></body></html>
        <?php
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Conectar WhatsApp Business | DF Tecnologia</title>
<style>
  body { font-family: system-ui, -apple-system, sans-serif; background:#f4f6f8; margin:0; padding:40px 16px; color:#212529; }
  .card { max-width:660px; margin:0 auto; background:#fff; border-radius:8px; padding:32px 36px; box-shadow:0 1px 4px rgba(0,0,0,.1); }
  h1 { font-size:1.4rem; margin:0 0 4px; }
  .sub { color:#6c757d; margin:0 0 22px; font-size:.95rem; }
  label { display:block; font-weight:600; font-size:.88rem; margin-top:16px; }
  input { width:100%; padding:9px 10px; margin-top:5px; border:1px solid #ced4da; border-radius:4px; box-sizing:border-box; font-size:.92rem; }
  small { color:#6c757d; font-size:.8rem; }
  button { margin-top:24px; width:100%; padding:12px; background:#25D366; color:#fff; border:0; border-radius:4px; font-size:1rem; font-weight:600; cursor:pointer; }
  button:disabled { background:#9bd8b3; cursor:default; }
  pre { background:#282c34; color:#e6e6e6; padding:16px; border-radius:6px; overflow-x:auto; font-size:.82rem; line-height:1.5; }
  .box { margin-top:24px; padding:14px 16px; border-radius:6px; font-size:.9rem; display:none; }
  .ok { background:#e8f7ee; border:1px solid #25D366; }
  .falha { background:#fdecea; border:1px solid #dc3545; color:#842029; }
  .passos { background:#fff8e1; border:1px solid #ffe08a; border-radius:6px; padding:14px 18px; font-size:.88rem; }
  .passos ol { margin:8px 0 0; padding-left:20px; }
  .passos li { margin-bottom:5px; }
</style>
</head>
<body>
<div class="card">
  <h1>Conectar WhatsApp Business</h1>
  <p class="sub">Vincula o numero que a empresa ja usa no celular a Plataforma de Negocios, mantendo o atendimento manual funcionando normalmente.</p>

  <div class="passos">
    <strong>Antes de comecar</strong> &mdash; tenha o celular da empresa em maos, com o WhatsApp Business instalado e atualizado.
    <ol>
      <li>Preencha os campos abaixo e clique em <em>Conectar</em>.</li>
      <li>Na janela da Meta, escolha conectar a conta existente do WhatsApp Business.</li>
      <li>No celular, abra a conversa da Meta e toque em <em>Conectar a Plataforma de Negocios</em>.</li>
      <li>Confirme e copie o codigo exibido, colando-o na janela.</li>
    </ol>
  </div>

  <label>Empresa</label>
  <input id="empresa" placeholder="ex: Nome da empresa">

  <label>URL do webhook do servidor dela</label>
  <input id="webhook" placeholder="https://empresa.dbytech.com.br/whatsapp-cloud/webhook">
  <small>Para onde as mensagens dessa empresa serao entregues.</small>

  <label>Token de verificacao</label>
  <input id="verify" placeholder="mesmo valor do WHATSAPP_VERIFY_TOKEN daquela instalacao">

  <button id="btn">Conectar</button>

  <div id="ok" class="box ok"></div>
  <div id="erro" class="box falha"></div>
</div>

<script>
  window.fbAsyncInit = function () {
    FB.init({
      appId  : <?= json_encode($cfg['app_id']) ?>,
      cookie : true,
      xfbml  : false,
      version: <?= json_encode($cfg['graph_version']) ?>
    });
  };
</script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/pt_BR/sdk.js"></script>

<script>
var CONFIG_ID = <?= json_encode($cfg['config_id']) ?>;
var dadosSessao = {};

// Os IDs da conta chegam por postMessage; o codigo de troca vem no callback
// do FB.login. Sao dois canais separados, por isso guardamos aqui.
window.addEventListener('message', function (e) {
  var host;
  try { host = new URL(e.origin).hostname; } catch (_) { return; }
  if (host !== 'www.facebook.com' && host !== 'web.facebook.com' && host !== 'facebook.com') return;

  try {
    var msg = JSON.parse(e.data);
    if (msg.type === 'WA_EMBEDDED_SIGNUP' && msg.data) dadosSessao = msg.data;
  } catch (_) {}
});

document.getElementById('btn').addEventListener('click', function () {
  var empresa = document.getElementById('empresa').value.trim();
  var webhook = document.getElementById('webhook').value.trim();
  var verify  = document.getElementById('verify').value.trim();

  if (!empresa || !webhook || !verify) { return mostrarErro('Preencha os tres campos antes de conectar.'); }
  if (webhook.indexOf('https://') !== 0) { return mostrarErro('O webhook precisa ser HTTPS.'); }

  esconder();
  var btn = this;
  btn.disabled = true;

  FB.login(function (resposta) {
    var code = resposta && resposta.authResponse && resposta.authResponse.code;

    if (!code) {
      btn.disabled = false;
      return mostrarErro('Conexao cancelada ou nao autorizada.');
    }

    // O codigo expira em 30 segundos - troca no servidor imediatamente.
    fetch('conectar.php', {
      method : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body   : JSON.stringify({
        code: code,
        empresa: empresa,
        webhook: webhook,
        verify: verify,
        waba_id: dadosSessao.waba_id || '',
        phone_number_id: dadosSessao.phone_number_id || ''
      })
    })
    .then(function (r) { return r.json(); })
    .then(function (r) {
      btn.disabled = false;
      if (r.erro) { mostrarErro(r.erro); } else { mostrarOk(r); }
    })
    .catch(function () {
      btn.disabled = false;
      mostrarErro('Falha ao falar com o servidor.');
    });

  }, {
    config_id: CONFIG_ID,
    response_type: 'code',
    override_default_response_type: true,
    extras: {
      setup: {},
      // habilita a coexistencia: conecta um numero que ja roda no app do celular
      featureType: 'whatsapp_business_app_onboarding',
      sessionInfoVersion: '3'
    }
  });
});

function esconder() {
  document.getElementById('ok').style.display = 'none';
  document.getElementById('erro').style.display = 'none';
}

function mostrarErro(m) {
  var e = document.getElementById('erro');
  e.textContent = m;
  e.style.display = 'block';
}

function escapar(s) {
  return String(s).replace(/[<>&]/g, function (c) {
    return { '<': '&lt;', '>': '&gt;', '&': '&amp;' }[c];
  });
}

function mostrarOk(r) {
  var o = document.getElementById('ok');
  var html = '<strong>Conectado.</strong> ';

  html += r.webhook_ok
    ? 'Webhook apontado para o servidor da empresa.'
    : '<em>Atencao: o webhook nao pode ser configurado - veja o aviso abaixo.</em>';

  html += '<p style="margin:14px 0 6px">Cole no <code>.env</code> da instalacao dessa empresa:</p>';
  html += '<pre>' + escapar(r.env) + '</pre>';

  if (r.aviso) { html += '<p style="color:#842029;margin:0">' + escapar(r.aviso) + '</p>'; }

  o.innerHTML = html;
  o.style.display = 'block';
}
</script>
</body>
</html>
