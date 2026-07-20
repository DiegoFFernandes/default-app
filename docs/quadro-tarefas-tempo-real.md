# Quadro de Tarefas — Sincronização em tempo real (Firestore)

Quando um usuário altera o quadro (mover card, criar/editar/excluir card, criar/editar/arquivar coluna),
os demais que estão vendo o **mesmo projeto** recebem um sinal via Firestore e recarregam as colunas/cards
automaticamente.

## Como funciona

1. Ao abrir o quadro, o backend (`TarefasController@tarefas`) gera um **custom token** do Firebase para o
   usuário logado, via `App\Services\FirebaseAuthService` (usa o `kreait/firebase-php` + a service account em
   `storage/app/firebase/service-account.json`).
2. O navegador faz `signInWithCustomToken(token)` e escuta o documento `quadros/{projetoId}` no Firestore.
3. A cada alteração salva com sucesso, o navegador grava
   `quadros/{projetoId} = { atualizadoPor, atualizadoEm }`.
4. Os outros navegadores recebem o `onSnapshot`, e — se o sinal veio de outra pessoa e o quadro não estiver
   ocupado (arraste em andamento ou modal aberto) — chamam `initColunas()` para re-renderizar. Se estiver
   ocupado, a atualização fica pendente e é aplicada ao soltar o card / fechar o modal.

## Pré-requisitos no console do Firebase (projeto `meuapppwa-f72da`)

Estas etapas são feitas UMA vez no console, não no código:

1. **Ativar Authentication** — Build > Authentication > Get started. Custom tokens não exigem um provedor
   específico, mas o Authentication precisa estar habilitado no projeto.
2. **Ativar Cloud Firestore** — Build > Firestore Database > Create database.
3. **Regras de segurança do Firestore** — cole em Firestore > Rules:

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

   Isso libera leitura/escrita da coleção `quadros` apenas para usuários autenticados (via o custom token
   gerado pelo backend). Nenhuma outra coleção fica exposta.

## Gotcha: Service Worker x long-polling do Firestore

O `public/sw.js` interceptava **toda** requisição GET e refazia o fetch (cache-first). Isso quebrava o
long-polling do Firestore (`firestore.googleapis.com/.../Listen/channel`), gerando um loop de requisições
(centenas por minuto, visíveis no DevTools com iniciador `sw.js`). Corrigido em `public/sw.js` fazendo o
handler de `fetch` **ignorar requisições cross-origin** (`url.origin !== self.location.origin`) — Firebase/CDNs
passam direto para a rede. `CACHE_NAME` foi para `pwa-cache-v2` para limpar o cache antigo ao ativar.

Se mexer no service worker, é preciso forçar a atualização no navegador (DevTools > Aplicativo > Service
Workers > Cancelar registro + recarregar, ou "Atualizar ao recarregar").

## Multi-cliente: um projeto Firebase por instalação

O sistema é instalado **por cliente** (deploy + banco MySQL próprios). Como os ids de `kanban_projetos`
se repetem entre clientes, cada instalação precisa do **seu próprio projeto Firebase** — senão os sinais
de `quadros/{projetoId}` colidem entre clientes. Nenhuma mudança de código é necessária: cada instalação
lê a config do próprio `.env` + `service-account.json`.

### Checklist para cada cliente

1. **Criar um projeto no Firebase Console** (um por cliente).
2. **Registrar um app Web** no projeto (Project settings > General > Your apps > Web) e copiar a config.
3. **Ativar Authentication** (Build > Authentication > Get started) — não precisa de provedor, é só para custom token.
4. **Ativar Cloud Firestore** (Build > Firestore Database > Create database).
5. **Colar as regras** do Firestore (ver seção "Pré-requisitos" acima).
6. **Gerar o service account** (Project settings > Service accounts > Generate new private key) e salvar o JSON
   em `storage/app/firebase/service-account.json` **daquela instalação**.
7. **Preencher o `.env` daquela instalação** com os valores do projeto do cliente:

   | Variável | De onde vem (config Web do Firebase) |
   |---|---|
   | `FMC_API_KEY` | `apiKey` |
   | `FCM_AUTH_DOMAIN` | `authDomain` (`<projeto>.firebaseapp.com`) |
   | `FCM_PROJECT_ID` | `projectId` |
   | `FCM_STORAGE_BUCKET` | `storageBucket` |
   | `FCM_MESSAGING_SENDER_ID` | `messagingSenderId` |
   | `FCM_APP_ID` | `appId` |
   | `FCM_MEASUREMENT_ID` | `measurementId` |
   | `FCM_VAPID_PUBLIC_KEY` | Cloud Messaging > Web Push certificates (par de chaves) |
   | `FCM_CREDENTIALS` | caminho do JSON (fica `storage/app/firebase/service-account.json`; muda só o conteúdo do arquivo) |

8. Rodar `php artisan config:clear` após alterar o `.env`.

> O `public/firebase-messaging-sw.js` (push em segundo plano) **não** precisa ser editado por cliente: a config
> dele é passada via query string no registro do SW (`master.blade.php`, constante `FCM_SW_URL`), lida do
> `.env`. Ou seja, os `FCM_*` do passo 7 já configuram tanto o sync em tempo real quanto o push. Ao alterar o
> service worker, force a atualização no navegador (DevTools > Aplicativo > Service Workers).

## Limitação conhecida

Ainda existe uma janela de corrida mínima: se duas pessoas soltarem um card no mesmíssimo instante (dentro do
tempo de propagação do sinal, ~sub-segundo), uma alteração ainda pode ser sobrescrita. Para eliminar 100%
seria necessário o "delta save" (enviar só o card movido em vez da coluna inteira) — não implementado aqui.
