<?php
/**
 * NotificacaoService — Envio de e-mails
 */
class NotificacaoService
{
    /**
     * Envia e-mail de confirmação de agendamento
     */
    public static function confirmarAgendamento(array $agendamento): bool
    {
        $config = config('mail');
        $tenant = config('tenant');

        $para = $agendamento['cliente_email'] ?? '';
        if (empty($para)) return false;

        $assunto = "{$tenant['nome']} — Agendamento Confirmado";

        $corpo = "
            <div style='font-family: Inter, sans-serif; color: #0A0A0A; max-width: 480px; margin: 0 auto;'>
                <h1 style='font-size: 24px;'>{$tenant['nome']}</h1>
                <p>Olá, <strong>{$agendamento['cliente_nome']}</strong>!</p>
                <p>Seu agendamento foi confirmado:</p>
                <table style='width: 100%; border-collapse: collapse; margin: 16px 0;'>
                    <tr><td style='padding: 8px 0; color: #6D6D6D;'>Serviço</td><td style='padding: 8px 0; font-weight: 600;'>{$agendamento['servico_nome']}</td></tr>
                    <tr><td style='padding: 8px 0; color: #6D6D6D;'>Profissional</td><td style='padding: 8px 0; font-weight: 600;'>{$agendamento['profissional_nome']}</td></tr>
                    <tr><td style='padding: 8px 0; color: #6D6D6D;'>Data</td><td style='padding: 8px 0; font-weight: 600;'>" . DateHelper::format($agendamento['data']) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6D6D6D;'>Horário</td><td style='padding: 8px 0; font-weight: 600;'>" . substr($agendamento['hora'], 0, 5) . "</td></tr>
                </table>
                <p style='color: #6D6D6D; font-size: 14px;'>Em caso de dúvidas, entre em contato: {$tenant['telefone']}</p>
            </div>
        ";

        return self::sendMail($para, $assunto, $corpo);
    }

    /**
     * Envia e-mail via SMTP usando mail() (fallback para shared hosting)
     */
    private static function sendMail(string $to, string $subject, string $body): bool
    {
        $config = config('mail');

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$config['from_name']} <{$config['from_address']}>",
            "Reply-To: {$config['from_address']}",
        ];

        try {
            return mail($to, $subject, $body, implode("\r\n", $headers));
        } catch (\Exception $e) {
            error_log("Mail Error: " . $e->getMessage());
            return false;
        }
    }
}
