<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class DynamicMailService
{
    public function applyConfiguration(): string
    {
        $driver = Setting::getValue('mail_driver', 'log'); // smtp, brevo, log
        $fromAddress = Setting::getValue('mail_from_address', 'contact@chefchaouen.info');
        $fromName = Setting::getValue('mail_from_name', 'Chefchaouen.info');

        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', $fromName);

        if ($driver === 'smtp') {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', Setting::getValue('smtp_host', '127.0.0.1'));
            Config::set('mail.mailers.smtp.port', (int) Setting::getValue('smtp_port', 587));
            Config::set('mail.mailers.smtp.encryption', Setting::getValue('smtp_encryption', 'tls'));
            Config::set('mail.mailers.smtp.username', Setting::getValue('smtp_username', ''));
            Config::set('mail.mailers.smtp.password', Setting::getValue('smtp_password', ''));
        } elseif ($driver === 'brevo_smtp') {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', 'smtp-relay.brevo.com');
            Config::set('mail.mailers.smtp.port', 587);
            Config::set('mail.mailers.smtp.encryption', 'tls');
            Config::set('mail.mailers.smtp.username', Setting::getValue('brevo_smtp_login', ''));
            Config::set('mail.mailers.smtp.password', Setting::getValue('brevo_api_key', ''));
        }

        return $driver;
    }

    public function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): array
    {
        $driver = $this->applyConfiguration();
        $fromAddress = Setting::getValue('mail_from_address', 'contact@chefchaouen.info');
        $fromName = Setting::getValue('mail_from_name', 'Chefchaouen.info');

        try {
            if ($driver === 'brevo_api') {
                $apiKey = trim((string) Setting::getValue('brevo_api_key', ''));
                if ($apiKey === '') {
                    return ['success' => false, 'message' => 'Brevo API Key is not configured.'];
                }

                $response = Http::withHeaders([
                    'api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->timeout(15)->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => ['name' => $fromName, 'email' => $fromAddress],
                    'to' => [['email' => $toEmail, 'name' => $toName]],
                    'subject' => $subject,
                    'htmlContent' => $htmlBody,
                ]);

                if (!$response->successful()) {
                    return ['success' => false, 'message' => 'Brevo API error: ' . $response->body()];
                }

                return ['success' => true, 'message' => 'Email sent via Brevo Transactional API.'];
            }

            if ($driver === 'log') {
                Mail::html($htmlBody, function ($message) use ($toEmail, $toName, $subject) {
                    $message->to($toEmail, $toName)->subject($subject);
                });
                return ['success' => true, 'message' => 'Email logged locally (configure SMTP or Brevo in Email Settings for live delivery).'];
            }

            Mail::html($htmlBody, function ($message) use ($toEmail, $toName, $subject) {
                $message->to($toEmail, $toName)->subject($subject);
            });

            return ['success' => true, 'message' => 'Email dispatched via ' . strtoupper($driver) . '.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Mail delivery failed: ' . $e->getMessage()];
        }
    }
}
