# Firebase — Guia de configuração (por cliente/instalação)

Guia único para configurar o Firebase numa instalação. O sistema é instalado **por cliente** (deploy + banco
próprios), e **cada instalação usa um projeto Firebase próprio** (senão os ids de `kanban_projetos` colidem no
Firestore). Nenhuma mudança de código é necessária — tudo vem do `.env` + `service-account.json`.

O Firebase é usado em duas features:
- **Sync em tempo real do Quadro de Tarefas** (Firestore + Authentication via custom token).
- **Notificações push** (Cloud Messaging).

Detalhes da feature de tempo real: ver [quadro-tarefas-tempo-real.md](quadro-tarefas-tempo-real.md).

---

## ⚡ Se algo deu erro, comece aqui (troubleshooting)

| Erro no console do navegador | Causa | Correção |
|---|---|---|
| `auth/configuration-not-found` | **Authentication não ativado** no projeto | Console → Authentication → **Começar/Get started** (não precisa de provedor) |
| `Missing or insufficient permissions` (listener do Firestore) | **Regras do Firestore** não publicadas (banco criado em modo produção = nega tudo) | Console → Firestore → **Regras** → colar as regras (abaixo) → **Publicar** |
| `auth/invalid-api-key` / `auth/api-key-not-valid` | `FMC_API_KEY` errada/vazia no `.env` | Conferir o valor no `.env` e rodar `php artisan config:clear` |
| `auth/custom-token-mismatch` | `service-account.json` é de um projeto e o `.env` de outro | Garantir que o `project_id` do JSON == `FCM_PROJECT_ID` do `.env` |
| Rede "explode" de requisições (`channel?...`, iniciador `sw.js`) | Service worker do PWA quebrando o long-polling do Firestore | Já corrigido em `public/sw.js`; force a atualização do SW (DevTools → Aplicativo → Service Workers → Cancelar registro + recarregar) |
| Config não atualiza depois de mexer no `.env` | Config cacheada | `php artisan config:clear` |
| Service worker não pega a config nova | SW fica em cache no navegador | DevTools → Aplicativo → Service Workers → **Cancelar registro** + recarregar |

Regras do Firestore (colar em Firestore → Regras → Publicar):

```
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /quadros/{projetoId} {
      allow read, write: if request.auth != null;

      // presença (quem está com o quadro aberto)
      match /presenca/{userId} {
        allow read, write: if request.auth != null;
      }
    }
  }
}
```

> Se você já tinha publicado a versão **sem** o bloco `presenca`, republique com este — senão os avatares de
> presença dão `Missing or insufficient permissions`.

---

## Passo a passo (projeto novo)

Trocar `SEU_PROJECT_ID` pelo id do projeto (o mesmo do `FCM_PROJECT_ID` no `.env`).

1. **Criar o projeto** no [Firebase Console](https://console.firebase.google.com/).

2. **Registrar um app Web** e copiar a config
   `Configurações do projeto (⚙️) → Geral → Seus apps → Web (</>)`.
   Guarde: `apiKey`, `authDomain`, `projectId`, `storageBucket`, `messagingSenderId`, `appId`, `measurementId`.

3. **Ativar Authentication** (necessário para o custom token do sync em tempo real)
   `https://console.firebase.google.com/project/SEU_PROJECT_ID/authentication` → **Começar**.
   Não precisa ativar nenhum provedor (Email/Google/etc.).

4. **Ativar Cloud Firestore**
   `Firestore Database → Criar banco de dados`.

5. **Publicar as regras** do Firestore (as de cima)
   `https://console.firebase.google.com/project/SEU_PROJECT_ID/firestore/rules` → colar → **Publicar**.

6. **Gerar o service account** (chave privada — é o arquivo SECRETO)
   `https://console.firebase.google.com/project/SEU_PROJECT_ID/settings/serviceaccounts/adminsdk`
   → **Gerar nova chave privada** → baixa um `.json`.
   Renomeie para `service-account.json` e coloque em `storage/app/firebase/service-account.json`.

7. **Pegar o VAPID** (chave pública do push web)
   `https://console.firebase.google.com/project/SEU_PROJECT_ID/settings/cloudmessaging`
   → seção **Certificados push da Web** → copie o **Par de chaves** (gere um se não existir).

8. **Preencher o `.env`** (tabela abaixo) e rodar `php artisan config:clear`.

---

## Variáveis do `.env`

| Variável | De onde vem |
|---|---|
| `FMC_API_KEY` | `apiKey` (config web) — **atenção ao nome: `FMC_API_KEY`, não `FMCAPI_KEY`** |
| `FCM_AUTH_DOMAIN` | `authDomain` (`<projeto>.firebaseapp.com`) |
| `FCM_PROJECT_ID` | `projectId` |
| `FCM_STORAGE_BUCKET` | `storageBucket` |
| `FCM_MESSAGING_SENDER_ID` | `messagingSenderId` |
| `FCM_APP_ID` | `appId` |
| `FCM_MEASUREMENT_ID` | `measurementId` |
| `FCM_VAPID_PUBLIC_KEY` | Cloud Messaging → Certificados push da Web (par de chaves) |
| `FCM_CREDENTIALS` | caminho do JSON: `storage/app/firebase/service-account.json` (o caminho é sempre o mesmo; muda só o conteúdo do arquivo por cliente) |

> O `public/firebase-messaging-sw.js` **não** precisa ser editado por cliente: a config é injetada via query
> string no registro do SW (`master.blade.php`, constante `FCM_SW_URL`), lida do `.env`. Os `FCM_*`/`FMC_API_KEY`
> acima já configuram tanto o sync em tempo real quanto o push.

---

## Segurança (o que é secreto e o que não é)

- **Público (pode aparecer no fonte da página, sem problema):** toda a config web — `apiKey`, `authDomain`,
  `projectId`, `appId`, `measurementId`, VAPID. São só identificadores; a segurança vem das **regras do
  Firestore** + **Authentication**, não de esconder o `apiKey`.
- **Secreto (NUNCA expor):** `service-account.json` (chave privada, acesso admin). Fica em `storage/app/firebase/`
  (fora do `public/`), protegido por `storage/app/.gitignore`. Já validado: retorna 404 na web e não está no Git.

---

## Verificações rápidas (linha de comando)

```bash
# service-account e .env são do MESMO projeto?
grep '"project_id"' storage/app/firebase/service-account.json
grep '^FCM_PROJECT_ID' .env

# variável da apiKey resolve? (deve dizer DEFINIDA)
php artisan config:clear && php -r "require 'vendor/autoload.php'; \$a=require 'bootstrap/app.php'; \$a->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo env('FMC_API_KEY') ? 'DEFINIDA' : 'VAZIA';"
```
