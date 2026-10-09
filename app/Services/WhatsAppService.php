<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $url;
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
    }

    public function sendTemplate($to, $pdfUrl, $caption = 'Factura de Pago')
    {
        $url = "https://graph.facebook.com/v22.0/{$this->phoneId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "document",
            "document" => [
                "link" => $pdfUrl,   // enlace público al PDF
                "caption" => $caption
            ]
        ];

        $response = Http::withToken($this->token)
            ->post($url, $payload);

        return $response->json();
    }
}
