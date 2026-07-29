<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
</head>

<body style="font-family: Arial, Helvetica, sans-serif; color: #333; background:#f4f4f4; padding:20px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:6px; overflow:hidden;">
        <tr>
            <td style="padding:24px;">
                <p>Olá, <strong>{{ $nmPessoa }}</strong>!</p>

                <p>
                    Segue em anexo a Nota Fiscal <strong>{{ $nrNota }}</strong>
                    @if ($temBoleto)
                        e o(s) boleto(s) para pagamento.
                    @else
                        .
                    @endif
                </p>

                <div
                    style="background:#e7f3fe; border-left:4px solid #2196F3; padding:10px 14px; font-size:13px; margin:16px 0;">
                    Esta é uma mensagem automática. Caso não a encontre na caixa de entrada, verifique a caixa de spam
                    ou lixo eletrônico.
                </div>

                <p style="font-size:12px; color:#888;">
                    Em caso de dúvidas, entre em contato com o nosso faturamento pelo telefone
                    {{ $foneEmpresa }} ou pelo e-mail {{ strtolower($emailEmpresa) }}
                </p>
                <p style="font-size:12px; color:#888;">
                    Horário de Atendimento: de segunda a sexta, das 8h às 18h
                </p>
                <p style="font-size:12px; color:#888;">
                    Faturamento - {{ $foneEmpresa }} </br>
                    {{ strtolower($emailEmpresa) }}
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
