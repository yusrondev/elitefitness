<?php

namespace App\Services;

use GuzzleHttp\Client;

class WablasService
{
    protected $client;
    protected $token;
    protected $secretKey;

    public function __construct()
    {
        // Menginisialisasi Guzzle client dan mengambil konfigurasi dari file config
        $this->client = new Client(['base_uri' => config('wablas.base_url')]);  // Menggunakan base_url dari config/wablas.php
        $this->token = config('wablas.api_key');  // Menggunakan api_key dari config/wablas.php
        $this->secretKey = config('wablas.secret_key');  // Menggunakan secret_key dari config/wablas.php
    }

    public function sendMessages(array $messages)
    {
        try {
            // Mempersiapkan data pesan dalam format JSON
            $payload = [
                'data' => $messages,  // Data pesan dikirim dalam format 'data'
            ];

            // Melakukan request POST ke API Wablas untuk mengirim pesan
            $response = $this->client->post('/send-message', [
                'headers' => [
                    'Authorization' => "{$this->token}.{$this->secretKey}",  // Autentikasi dengan token dan secret key
                    'Content-Type'  => 'application/json',  // Menentukan format konten sebagai JSON
                ],
                'json' => $payload,  // Mengirim payload dalam format JSON
                'verify' => false,   // Menonaktifkan verifikasi SSL (tergantung pada pengaturan server)
            ]);

            // Mengembalikan respons dalam bentuk array
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, menangkap exception dan mengembalikan pesan error
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}