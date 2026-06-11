# Sistema de Gestão — Recapadora de Pneus

Sistema web para gestão operacional e comercial de recapadora de pneus, integrando um ERP legado em Firebird com módulos próprios em Laravel/MySQL.

---

## Tecnologias

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 9 (PHP 8.x) |
| Frontend | AdminLTE 3 + Bootstrap 5 + jQuery |
| Banco Principal (ERP) | Firebird (somente leitura + alguns writes) |
| Banco Próprio | MySQL |
| Autenticação | Laravel Session + Spatie Permission |
| Notificações Push | Firebase Cloud Messaging (FCM) |
| PDF | wkhtmltopdf via barryvdh/laravel-snappy |
| Excel | maatwebsite/excel |
| DataTables | yajra/laravel-datatables 9 |
| Assets | Vite 4 |

---

## Configuração Local

### Pré-requisitos

- PHP 8.x com extensões: `pdo_firebird`, `pdo_mysql`, `gd`, `zip`
- MySQL 8+
- Firebird client (fbclient.dll no Windows)
- Node.js 18+ e npm
- wkhtmltopdf (para geração de PDF)
- Composer 2

### Passo a passo

```bash
# 1. Clonar e instalar dependências PHP
composer install

# 2. Copiar e configurar variáveis de ambiente
cp .env.example .env
php artisan key:generate

# 3. Configurar .env
# DB_CONNECTION=mysql  (banco MySQL local)
# Configurar FIREBIRD_* com dados do servidor ERP
# Configurar FIREBASE_* com credenciais FCM

# 4. Criar tabelas e dados iniciais
php artisan migrate
php artisan db:seed

# 5. Instalar e compilar assets frontend
npm install
npm run dev       # desenvolvimento
npm run build     # produção

# 6. Permissões de storage (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### Variáveis de Ambiente Críticas

```env
# Banco MySQL (dados da aplicação)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recapadora
DB_USERNAME=root
DB_PASSWORD=

# Firebird (ERP legado — não alterar estrutura)
FIREBIRD_HOST=
FIREBIRD_PORT=3050
FIREBIRD_DATABASE=
FIREBIRD_USERNAME=
FIREBIRD_PASSWORD=
FIREBIRD_CHARSET=ISO8859_1

# Firebase Cloud Messaging
FIREBASE_PROJECT_ID=
FIREBASE_CREDENTIALS_PATH=

# Fila (sync em produção)
QUEUE_CONNECTION=sync
```

---

## Executando o Projeto

```bash
# Servidor de desenvolvimento
php artisan serve

# Compilar assets (modo watch)
npm run dev

# Executar jobs agendados manualmente
php artisan send:msg_fcm
php artisan pedidos:atualizar-alterados

# Scheduler (em produção, configurar no crontab)
* * * * * php /path/to/artisan schedule:run
```

---

## Estrutura Geral

```
app/
├── Console/Commands/       # Comandos artisan agendados
├── Http/
│   ├── Controllers/Admin/  # Controllers por módulo
│   └── Middleware/         # Auth, UserActivity
├── Jobs/                   # Processamento assíncrono (FCM)
├── Models/                 # Eloquent models (MySQL + Firebird)
└── Services/               # Lógica de negócio isolada

routes/
├── web.php                 # Rotas de autenticação e base
├── comercial.php           # Módulo comercial (principal)
├── pedido.php              # Pedidos de pneus
├── estoque.php             # Estoque e carcaças
├── expedicao.php           # Expedição
├── producao.php            # Produção e PCP
├── faturamento.php         # Análise de faturamento
├── financeiro.php          # Liberação de ordens financeiras
├── cobranca.php            # Relatórios de cobrança
├── nota.php                # Notas e devoluções
├── cliente.php             # Portal do cliente
├── usuarios.php            # Gestão de usuários e permissões
├── tarefas.php             # Kanban de tarefas
├── fcm.php                 # Notificações push
└── importa-junsoft.php     # Integração Junsoft

resources/views/admin/      # Views por módulo (blade)
database/migrations/        # Apenas tabelas MySQL próprias
```

---

## Principais Módulos

| Módulo | Descrição |
|---|---|
| Comercial | Tabela de preços, bloqueio de pedidos, acompanhamento, coletas, garantia, comissões |
| Pedidos | Gestão de pedidos de pneus via Firebird |
| Estoque | Carcaças, lotes, estoque negativo |
| Expedição | Lotes de expedição e itens |
| Produção | Executor de etapas, PCP (Planejamento e Controle) |
| Faturamento | Análise e relatórios de faturamento |
| Financeiro | Liberação de ordens e contas |
| Cobrança | Inadimplência, prazo médio, canhotos |
| Notas | Notas de devolução, divergências de vendedor |
| Cliente | Portal com notas emitidas e boletos |
| Usuários | CRUD de usuários, roles e permissões (Spatie) |
| Tarefas | Board Kanban interno |
| Notificações | Push via Firebase FCM |

---

## Controle de Acesso

O sistema usa RBAC via `spatie/laravel-permission`. Os principais papéis são:

- `admin` — Acesso total
- `diretoria` — Relatórios e visão gerencial
- `gerente comercial` — Módulo comercial completo
- `supervisor comercial` — Supervisão de equipe
- `vendedor comercial` — Operações de vendas
- `gerente de unidade` — Gestão de unidade
- `usuario comercial` — Operações básicas
- `cliente` — Portal restrito do cliente

Permissões granulares por tela, ex.: `ver-libera-ordem-comercial`, `ver-pedidos-alterados-valor`.

---

## Documentação Adicional

- [Arquitetura Detalhada](docs/architecture.md)
