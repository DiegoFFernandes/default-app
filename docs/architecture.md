# Arquitetura do Sistema

## Visão Geral

Sistema Laravel 9 para gestão operacional de recapadora de pneus. Atua como camada de aplicação sobre um ERP legado em Firebird, adicionando módulos próprios em MySQL e integrações externas.

```
┌─────────────────────────────────────────────────────┐
│                    Navegador                         │
│         AdminLTE + Bootstrap + jQuery               │
│         AJAX / DataTables / Vanilla JS              │
└─────────────────┬───────────────────────────────────┘
                  │ HTTP/HTTPS
┌─────────────────▼───────────────────────────────────┐
│               Apache 2.4 (Windows)                   │
│          c:\Apache24\htdocs\default-app             │
└─────────────────┬───────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────┐
│                  Laravel 9                           │
│  Rotas → Middleware → Controllers → Services        │
│  Models (Eloquent) → Views (Blade)                  │
└──────────┬───────────────────────┬──────────────────┘
           │                       │
┌──────────▼──────────┐  ┌────────▼────────────────────┐
│   MySQL             │  │   Firebird (ERP Legado)      │
│   (dados próprios)  │  │   (dados de negócio)         │
└─────────────────────┘  └─────────────────────────────┘
```

---

## Fluxo da Aplicação

```
Request HTTP
    → RouteServiceProvider (routes/*.php)
    → Middlewares (auth, role, permission, UserActivity)
    → Controller
        → Model (MySQL ou Firebird via DB::connection('firebird'))
        → Service (lógica de negócio isolada, quando necessário)
        → Response (View Blade ou JSON para AJAX)
```

---

## Módulos Existentes

| Módulo | Arquivo de Rotas | Controller Principal | Banco |
|---|---|---|---|
| Comercial | routes/comercial.php | Multiple | Firebird + MySQL |
| Pedidos | routes/pedido.php | PedidoPneuController | Firebird |
| Estoque | routes/estoque.php | EstoqueController | MySQL + Firebird |
| Expedição | routes/expedicao.php | LoteExpedicaoController | MySQL |
| Produção | routes/producao.php | ProducaoController / PcpProducaoController | Firebird + MySQL |
| Faturamento | routes/faturamento.php | FaturamentoController | Firebird |
| Financeiro | routes/financeiro.php | FinanceiroController | Firebird |
| Cobrança | routes/cobranca.php | Multiple | Firebird |
| Notas | routes/nota.php | NotaDevolucaoController / NotaVendedorDivergenteController | Firebird + MySQL |
| Cliente | routes/cliente.php | AcessoClienteController | Firebird |
| Usuários | routes/usuarios.php | UserController / RoleController / PermissionController | MySQL |
| Tarefas | routes/tarefas.php | TarefasController | MySQL |
| Notificações | routes/fcm.php | FCMController | MySQL + Firebase |
| Importação | routes/importa-junsoft.php | ImportaJunsoftController | MySQL |

---

## Banco de Dados

### MySQL — Tabelas Próprias

Gerenciado por migrations Laravel. Dados da aplicação, não do ERP.

| Grupo | Tabelas |
|---|---|
| Auth | `users`, `password_resets`, `personal_access_tokens` |
| RBAC | `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` |
| Comercial | `vendedor_comercial`, `area_comercial`, `regiao_comercial`, `supervisor_comercial`, `gerente_unidades`, `supervisor_subgrupo`, `percentual_desconto_comercial` |
| Estoque | `lote_estoque`, `item_lote_estoque`, `marca_lote_estoque`, `marca_pneus`, `sub_grupos`, `items` |
| Notificações | `fmc_tokens`, `notification_users` |
| Configurações | `configuracoes_painel`, `filtro_agrupamentos`, `filtro_grupos`, `filtro_subrupos` |
| Kanban | `kanban_projetos`, `kanban_colunas`, `kanban_cartoes` |
| Pessoas | `pessoas` |

### Firebird — ERP Legado

Banco de terceiros. Estrutura não controlada pela aplicação. Acesso via driver `harrygulliford/laravel-firebird`.

**Características críticas:**
- Charset: `ISO8859_1` — conversão para UTF-8 obrigatória via `Helper::ConvertFormatText()`
- Procedures: `GERA_SESSAO`, `RECALCULA_COMISSAOV2`
- UDFs customizadas: `MES_EXTENSO`, `RETORNA_CHAVEPEDIDO`, `JV_RETORNADIASUTEIS`
- Multi-empresa: campo `CD_EMPRESA` presente em quase todas as tabelas

**Principais tabelas consultadas:**

| Tabela Firebird | Uso no Sistema |
|---|---|
| `PEDIDOPNEU` / `ITEMPEDIDOPNEU` | Pedidos de pneus |
| `NOTA` / `ITEMNOTA` | Notas fiscais |
| `PESSOA` | Clientes e fornecedores |
| `VENDEDOR` | Vendedores do ERP |
| `ESTOQUE` | Estoque do ERP |
| `PRODUCAO` | Ordens de produção |
| `COMISSAO` / `ITEMCOMISSAO` | Comissões |
| `FINANCEIRO` | Contas a pagar/receber |
| `COBRANCA` | Cobrança/inadimplência |
| `ITEMTAB` / `TABELAPRECOS` | Tabela de preços |

### Fluxo de Sincronização / Acesso Dual

```
Firebird (ERP)          MySQL (Aplicação)
    │                        │
    │── leitura direta ──────┤  (maioria das consultas)
    │── insert/update ───────┤  (pedidos, comissão, sessão)
    │                        │
    │                   cache local de
    │                   vendedores, configs,
    │                   permissões, notificações
```

**Estratégia de cache:** Queries Firebird pesadas usam `Cache::remember()` com TTL de 30–60 minutos para reduzir carga no ERP.

---

## Integrações

### Firebase Cloud Messaging (FCM)

**Propósito:** Notificações push para usuários (web/mobile).

**Fluxo:**
```
Scheduler (08h, 12h, 16h)
    → Command: send:msg_fcm
        → Job: SendFcmJob (dispatch)
            → FCMController::sendToUser()
                → FCMService (Guzzle HTTP POST)
                    → https://fcm.googleapis.com/v1/projects/{id}/messages:send
```

**Arquivos:**
- `app/Services/FCMService.php` — autenticação OAuth2 + envio HTTP
- `app/Http/Controllers/Fcm/FCMController.php` — salvar token, enviar notificação
- `app/Jobs/SendFcmJob.php` — job assíncrono (ShouldQueue)
- `app/Console/Commands/SendMsgFcm.php` — comando artisan
- `app/Models/FMCToken.php` — tokens de dispositivos
- `app/Models/NotificationUsers.php` — registro de notificações enviadas

**Credenciais:** JSON de service account Google (path configurado via `FIREBASE_CREDENTIALS_PATH`).

---

### Firebird (ERP Legado)

**Propósito:** Fonte principal de dados de negócio.

**Conexão:** `DB::connection('firebird')` — configurado em `config/database.php`.

**Padrão de acesso:**
```php
// Models com dual connection
protected $connection = 'firebird';

// Query Builder direto
DB::connection('firebird')->select('SELECT * FROM PEDIDOPNEU WHERE ...');

// Cache obrigatório em queries pesadas
Cache::remember('key', 1800, fn() => DB::connection('firebird')->select(...));
```

**Cuidados:**
- Sempre converter strings: `Helper::ConvertFormatText($string)`
- Evitar N+1 com queries Firebird — custo alto por conexão
- Transações em Firebird devem ser explicitamente abertas/fechadas

---

### Excel (Import/Export)

**Pacote:** `maatwebsite/excel 3.1.43`

**Uso:** Importação de dados Junsoft (`routes/importa-junsoft.php`) e exportações de relatórios.

---

### PDF

**Pacote:** `barryvdh/laravel-snappy` (wkhtmltopdf)

**Uso:** Geração de boletos e relatórios em PDF.

---

### PWA (Progressive Web App)

**Pacote:** `ladumor/laravel-pwa`

**Manifesto:** `routes/web.php` → GET `/manifest.json`

**Uso:** Permite instalação do sistema como app no mobile.

---

## Frontend

### Estrutura AdminLTE

```
resources/views/
├── layouts/
│   ├── master-simple.blade.php   # Layout base (AdminLTE)
│   └── page.blade.php            # Variação simplificada
├── components/
│   ├── btn-topo-modal.blade.php  # Botão de ação no topo de modais
│   └── loading-card.blade.php   # Card de carregamento
├── admin/
│   └── {modulo}/
│       ├── index.blade.php       # Listagem principal
│       ├── tabs/                 # Componentes de abas
│       └── modals/               # Modais reutilizáveis
└── auth/                         # Telas de autenticação
```

### Componentes Reutilizáveis

| Componente | Arquivo | Uso |
|---|---|---|
| Botão topo modal | `components/btn-topo-modal.blade.php` | Fechar/ação em modais |
| Loading card | `components/loading-card.blade.php` | Spinner de carregamento |
| DataTable padrão | JS inline (por view) | Listagens com server-side |

### Padrão de DataTables

Todas as listagens seguem o mesmo padrão:

```javascript
// Inicialização padrão
$('#table-id').DataTable({
    processing: false,
    serverSide: false,       // ou true para server-side
    ajax: window.routes.dataUrl,
    language: { url: window.routes.languageDatatables },
    columns: [ ... ],
    pagingType: "simple",
    scrollY: "400px",
    scrollCollapse: true,
});
```

**Rotas expostas via `window.routes`** no `@section('js')` de cada view — padrão consistente em todo o sistema.

### Padrão AJAX

```javascript
$.ajax({
    type: "POST",
    url: window.routes.nomeRota,
    data: { _token: window.routes.token, ... },
    beforeSend: () => Swal.fire({ title: 'Processando...', didOpen: () => Swal.showLoading() }),
    success: (response) => { if (response.success) { ... } },
    error: () => Swal.fire({ icon: 'error', ... })
});
```

---

## Backend

### Controllers

Localizados em `app/Http/Controllers/Admin/`. Padrão de nomenclatura: `{Entidade}Controller.php`.

**Métodos padrão:**
| Método | Descrição |
|---|---|
| `index()` | Retorna view principal |
| `get{Entidade}()` | Retorna JSON para DataTable via AJAX |
| `store()` | Criação via AJAX → retorna `{success, message}` |
| `update()` | Atualização via AJAX → retorna `{success, message}` |
| `destroy()` | Exclusão via AJAX → retorna `{success, message}` |

**Resposta padrão:**
```php
return response()->json(['success' => true, 'message' => 'Operação realizada.']);
return response()->json(['success' => false, 'message' => 'Erro: ...' ]);
```

### Services

| Service | Responsabilidade |
|---|---|
| `FCMService` | Autenticação OAuth2 Google + envio HTTP para FCM |
| `ServiceEstoqueNegativo` | Cálculo e lógica de estoque negativo |
| `ServiceFiltroGrupoSubgrupo` | Filtros de grupos/subgrupos em relatórios |
| `SupervisorAuthService` | Autenticação e lookup de supervisores |
| `UserRoleFilterService` | Filtro de usuários por roles |

### Models

Dois tipos de model coexistem:

**MySQL (Eloquent padrão):**
```php
class Vendedor extends Model {
    protected $connection = 'mysql';  // ou omitido (default)
    protected $table = 'vendedor_comercial';
}
```

**Firebird (connection explícita):**
```php
class PedidoPneu extends Model {
    protected $connection = 'firebird';
    protected $table = 'PEDIDOPNEU';
    // Sem timestamps automáticos
    public $timestamps = false;
}
```

### Requests

Validações em `app/Http/Requests/` (verificar arquivos disponíveis). Para operações AJAX, validação inline nos controllers.

### Jobs

| Job | Trigger | Ação |
|---|---|---|
| `SendFcmJob` | Scheduler (3x/dia) | Envia notificações FCM pendentes |

**Queue driver:** `sync` (execução síncrona — não há worker de fila em produção).

### Commands (Artisan)

| Comando | Agendamento | Ação |
|---|---|---|
| `send:msg_fcm` | 08:00, 12:00, 16:00 | Envia notificações FCM agendadas |
| `pedidos:atualizar-alterados` | A cada 10 minutos | Sincroniza pedidos alterados do ERP |

**Kernel:** `app/Console/Kernel.php`

---

## Fluxos Críticos

### Autenticação

```
GET /login → LoginController@showLoginForm
POST /login → LoginController@login
    → Auth::attempt()
    → Sessão criada (TTL: 3 horas / 10800s)
    → Redirect → /home

Middleware 'auth' protege todas as rotas /admin/*
Middleware 'role' e 'permission' protegem rotas específicas
```

### Autorização (RBAC)

```
Request → Middleware 'permission:{nome}'
    → Spatie\Permission\Middlewares\PermissionMiddleware
        → $user->hasPermissionTo($permission)
            → model_has_permissions (MySQL)
            → model_has_roles + role_has_permissions (MySQL)
```

### Fluxo de Pedido (Firebird)

```
View → AJAX POST → PedidoPneuController@store
    → DB::connection('firebird')->beginTransaction()
    → INSERT INTO PEDIDOPNEU
    → INSERT INTO ITEMPEDIDOPNEU (loop por item)
    → Procedure: GERA_SESSAO ou RECALCULA_COMISSAOV2
    → DB::connection('firebird')->commit()
    → response()->json(['success' => true])
```

### Importação Excel (Junsoft)

```
View upload → POST → ImportaJunsoftController
    → Excel::import(new JunsoftImport, $request->file)
        → Lê planilha linha a linha
        → Upsert em tabelas MySQL
    → response()->json(['success' => true])
```

### Notificações Push (FCM)

```
Cron → php artisan schedule:run
    → send:msg_fcm (08h, 12h, 16h)
        → SendFcmJob::dispatch()
            → FCMService::sendNotification()
                → Google OAuth2 (ServiceAccount)
                → HTTP POST → FCM API
                    → Dispositivo recebe push
```

### Geração de PDF

```
GET /{rota}-pdf → Controller@gerarPdf
    → Snappy::loadView('template', $data)
    → return response PDF inline ou download
```

---

## Segurança

### Controles Implementados

| Controle | Implementação |
|---|---|
| CSRF | `VerifyCsrfToken` middleware (todas as rotas web) |
| Autenticação | Session-based via `Auth::attempt()` |
| Autorização | Spatie RBAC — role + permission por rota |
| XSS | `stevebauman/purify` para sanitização de HTML |
| SQL Injection | PDO parameterized queries (Eloquent + Query Builder) |
| Mass Assignment | `$fillable` definido em todos os models |
| Upload | Validação de tipo/tamanho via Request |

### Pontos de Atenção

- Queries Firebird com SQL raw devem usar bindings, nunca concatenação direta de input do usuário
- Charset ISO8859_1 do Firebird pode gerar problemas de encoding se não passar por `Helper::ConvertFormatText()`
- Tokens FCM armazenados em MySQL — rotacionar periodicamente

---

## Performance

### Estratégias Adotadas

1. **Cache de queries Firebird:** `Cache::remember('key', 1800, fn() => ...)` — evita queries repetidas no ERP
2. **DataTables client-side:** Para volumes menores (< 5.000 registros)
3. **DataTables server-side:** Para grandes volumes com paginação no banco
4. **scrollY + scrollCollapse:** Limita altura das tabelas sem paginar client-side

### Riscos de Performance

- Queries Firebird com múltiplos JOINs e UNIONs sem índice adequado
- N+1 em loops que consultam Firebird por item de pedido
- `QUEUE_CONNECTION=sync` — jobs FCM bloqueiam a request se lentos

---

## Dependências de Infraestrutura

| Serviço | Obrigatório | Descrição |
|---|---|---|
| Apache 2.4 | Sim | Servidor web (Windows) |
| MySQL 8+ | Sim | Banco de dados próprio |
| Firebird Server | Sim | ERP legado (remoto) |
| wkhtmltopdf | Sim | Geração de PDF |
| Firebase (Google) | Não | Notificações push |
| SMTP | Não | Envio de e-mails |
| Pusher | Não | Broadcasting em tempo real |
