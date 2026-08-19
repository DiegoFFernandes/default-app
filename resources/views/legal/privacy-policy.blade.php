<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade — {{ config('app.name') }}</title>
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
            <h1>Política de Privacidade</h1>
            <p class="updated">Última atualização: {{ now()->format('d/m/Y') }}</p>

            <p>
                Esta Política de Privacidade descreve como <strong>{{ config('app.name') }}</strong>
                ("nós") coleta, usa e protege as informações de seus clientes e usuários,
                incluindo as trocadas por meio do WhatsApp.
            </p>

            <h2>1. Quem somos</h2>
            <p>
                {{ config('app.name') }} é responsável pelo tratamento dos dados descritos nesta
                política, incluindo o número de WhatsApp utilizado para comunicação com clientes.
                Site: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>.
            </p>

            <h2>2. Quais dados coletamos</h2>
            <ul>
                <li>Dados de identificação e contato (nome, telefone, e-mail);</li>
                <li>Dados de pedidos, compras e histórico de atendimento;</li>
                <li>Conteúdo das mensagens trocadas via WhatsApp para fins de atendimento e notificação.</li>
            </ul>

            <h2>3. Como usamos seus dados</h2>
            <ul>
                <li>Enviar notificações sobre pedidos, cobranças e atendimento via WhatsApp;</li>
                <li>Responder dúvidas e prestar suporte;</li>
                <li>Cumprir obrigações legais e contratuais.</li>
            </ul>

            <h2>4. Compartilhamento de dados</h2>
            <p>
                Utilizamos a plataforma WhatsApp Business (Meta) como canal de comunicação e
                processamento de mensagens. Não vendemos nem compartilhamos seus dados com
                terceiros para fins de marketing de terceiros.
            </p>

            <h2>5. Segurança e retenção</h2>
            <p>
                Adotamos medidas técnicas e organizacionais para proteger os dados armazenados,
                mantendo-os apenas pelo tempo necessário às finalidades descritas nesta política
                ou conforme exigido por lei.
            </p>

            <h2>6. Seus direitos (LGPD)</h2>
            <p>
                Você pode solicitar acesso, correção, portabilidade ou exclusão dos seus dados
                pessoais a qualquer momento, entrando em contato pelos canais abaixo.
            </p>

            <h2>7. Contato</h2>
            <p>
                Dúvidas sobre esta política ou sobre o tratamento dos seus dados podem ser
                enviadas para <a href="mailto:diego@dbytech.com.br">diego@dbytech.com.br</a>.
            </p>
        </div>
    </div>
</body>
</html>
