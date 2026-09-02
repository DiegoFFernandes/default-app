# Onboarding WhatsApp (coexistência)

Mini-aplicação em PHP puro que conecta o número que uma empresa **já usa no
celular** à Plataforma de Negócios da Meta, sem que ela perca o WhatsApp
Business do aparelho (coexistência).

Não faz parte do ERP. Roda num subdomínio próprio da DF, é usada **uma vez por
cliente** e não é acessada por ninguém além de quem faz a implantação.

```
ERP (Laravel)                     Esta pasta
─────────────────────────         ──────────────────────────
servidor de cada cliente          Hostinger, subdomínio da DF
chega via git pull                upload manual
```

## Por que existe

O registro de número pelo painel da Meta torna o número **exclusivo da API** — o
app do celular para de funcionar. Para manter os dois, a Meta exige o fluxo de
Cadastro Incorporado (Embedded Signup), que só pode ser disparado a partir de uma
página do próprio provedor, em domínio autorizado e com HTTPS.

O `app secret` é necessário para trocar o código por token, e não pode ficar no
navegador nem espalhado em servidor de cliente — por isso a página é central.

## Antes de usar: configuração na Meta

Feito uma vez só, no app da DF.

1. **Facebook Login for Business → Configurações**, ligar:
   Client OAuth login, Web OAuth login, Enforce HTTPS, Embedded Browser OAuth
   Login, Strict Mode for redirect URIs e Login with the JavaScript SDK.
2. Criar uma **configuração** do Facebook Login for Business → anotar o
   **ID da configuração** (`config_id`).
3. Adicionar o subdomínio em **Allowed domains** e **Valid OAuth redirect URIs**
   (ex.: `https://conecta.dbytech.com.br`).
4. Ter as permissões `whatsapp_business_management` e
   `whatsapp_business_messaging` — em desenvolvimento funcionam para quem tem
   papel de admin/desenvolvedor/testador no app; para onboardar cliente real,
   precisam estar aprovadas em acesso avançado.

## Deploy na Hostinger

1. Criar o subdomínio no hPanel e ativar o SSL gratuito dele.
2. Subir na raiz da pasta do subdomínio: `index.php` e `conectar.php`.
3. Copiar `config.exemplo.php` para **`config.php`** no servidor e preencher
   `app_id`, `app_secret`, `config_id` e `senha`.
   O `config.php` não vai para o git — guarda o app secret.
4. Conferir que o PHP do subdomínio está em 8.0 ou superior.

## Como conectar um cliente

Requisito: a instalação do ERP daquele cliente precisa estar **no ar** e com
`WHATSAPP_VERIFY_TOKEN` já definido no `.env` dela. A Meta faz um handshake de
verificação na URL do webhook no momento da conexão — se a instalação estiver
fora do ar ou com token diferente, o webhook não é configurado.

Tenha o celular da empresa em mãos, com o WhatsApp Business atualizado.

1. Abrir o subdomínio e entrar com a senha.
2. Preencher empresa, URL do webhook (`https://<cliente>/whatsapp-cloud/webhook`)
   e o token de verificação daquela instalação.
3. Clicar em **Conectar** — abre a janela da Meta.
4. Na janela: entrar com o Facebook da empresa e escolher conectar a conta
   existente do WhatsApp Business.
5. No celular: abrir a conversa da Meta, tocar em *Conectar à Plataforma de
   Negócios*, confirmar e copiar o código exibido.
6. Colar o código na janela. Ao concluir, a tela mostra o bloco do `.env`.
7. Colar esse bloco no `.env` da instalação do cliente e limpar o cache
   (`php artisan config:clear`).

## O que a conexão muda no celular do cliente

Avisar antes de conectar:

- Todos os dispositivos conectados são desvinculados (dá para religar depois).
- Grupos não sincronizam com a API.
- Listas de transmissão ficam somente leitura.
- Mensagens temporárias, "ver uma vez" e chamadas ficam desativadas nas
  conversas individuais.

Para desfazer, é no aparelho: *Configurações → Conta → Plataforma de Negócios →
Desconectar*. Não existe desconexão pela API.

## Diagnóstico

Cada passo é registrado em `onboarding.log`, na mesma pasta.

| Sintoma | Causa provável |
|---|---|
| "Nao foi possivel obter o token" | O código expira em 30s — refazer o fluxo sem pausas. |
| "nao foi possivel identificar a conta" | O fluxo foi cancelado antes de concluir no celular. |
| Conectou mas o webhook falhou | Instalação do cliente fora do ar, ou token de verificação diferente do informado. |
| A janela nem abre | Domínio fora da whitelist, ou `config_id` errado no `config.php`. |
