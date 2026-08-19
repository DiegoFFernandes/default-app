<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusão de Dados — {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f4f6f8;
            color: #212529;
            line-height: 1.6;
        }
        .wrap {
            max-width: 780px;
            margin: 0 auto;
            padding: 48px 24px 80px;
        }
        h1 { font-size: 1.75rem; margin-bottom: 4px; }
        .updated { color: #6c757d; font-size: 0.9rem; margin-bottom: 32px; }
        h2 { font-size: 1.15rem; margin-top: 32px; border-left: 4px solid #0d6efd; padding-left: 10px; }
        p, li { color: #343a40; }
        ul { padding-left: 20px; }
        a { color: #0d6efd; }
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 32px 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Exclusão de Dados</h1>
            <p class="updated">Última atualização: {{ now()->format('d/m/Y') }}</p>

            <p>
                Você pode solicitar a exclusão dos seus dados pessoais coletados por
                <strong>{{ config('app.name') }}</strong>, incluindo informações de cadastro,
                pedidos e histórico de conversas via WhatsApp.
            </p>

            <h2>Como solicitar</h2>
            <p>
                Envie um pedido de exclusão por um dos canais abaixo, informando seu nome
                completo e o número de telefone ou e-mail utilizado em seu cadastro:
            </p>
            <ul>
                <li>E-mail: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></li>
                <li>WhatsApp: <a href="https://wa.me/{{ preg_replace('/\D/', '', config('app.contact_numbers.cadastro')) }}">{{ config('app.contact_numbers.cadastro') }}</a></li>
            </ul>

            <h2>Prazo e confirmação</h2>
            <p>
                O pedido será processado em até 15 dias úteis. Após a exclusão, você
                receberá uma confirmação pelo mesmo canal utilizado na solicitação.
            </p>

            <h2>Observação</h2>
            <p>
                Dados que precisem ser mantidos por obrigação legal ou fiscal (como notas
                fiscais e registros de transações) poderão ser preservados pelo prazo exigido
                por lei, mesmo após a solicitação de exclusão.
            </p>
        </div>
    </div>
</body>
</html>
