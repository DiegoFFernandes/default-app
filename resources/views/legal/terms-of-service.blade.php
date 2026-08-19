<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Serviço — {{ config('app.name') }}</title>
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
            <h1>Termos de Serviço</h1>
            <p class="updated">Última atualização: {{ now()->format('d/m/Y') }}</p>

            <p>
                Estes Termos de Serviço regem o uso dos canais de atendimento e comunicação
                disponibilizados por <strong>{{ config('app.name') }}</strong> ("nós"), incluindo
                o atendimento via WhatsApp. Ao interagir com nossos canais, você concorda com
                estes termos.
            </p>

            <h2>1. Objeto</h2>
            <p>
                Utilizamos o WhatsApp Business como canal para envio de notificações de pedidos,
                cobranças, atendimento e suporte relacionados aos serviços contratados junto a
                {{ config('app.name') }}.
            </p>

            <h2>2. Uso do canal de WhatsApp</h2>
            <ul>
                <li>As mensagens são enviadas apenas a contatos que possuem relacionamento comercial conosco;</li>
                <li>Você pode solicitar o encerramento do recebimento de mensagens a qualquer momento;</li>
                <li>Não nos responsabilizamos pelo uso indevido do canal por terceiros que se passem por nós.</li>
            </ul>

            <h2>3. Responsabilidades do usuário</h2>
            <p>
                O usuário compromete-se a fornecer informações corretas e a utilizar os canais de
                atendimento de forma adequada, sem fins ilícitos ou abusivos.
            </p>

            <h2>4. Alterações destes termos</h2>
            <p>
                Podemos atualizar estes Termos de Serviço periodicamente. A versão vigente estará
                sempre disponível nesta página.
            </p>

            <h2>5. Contato</h2>
            <p>
                Dúvidas sobre estes termos podem ser enviadas para
                <a href="mailto:diego@dbytech.com.br">diego@dbytech.com.br</a>.
            </p>
        </div>
    </div>
</body>
</html>
