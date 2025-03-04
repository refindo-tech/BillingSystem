<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyWhatsapp extends Model
{
    use HasFactory;
    protected $table = 'key_whatsapps';

    protected $fillable = [
        'key_device',
        'key_account',
        'phone',
        'status'
    ];

    public function checkDeviceStatus()
    {
        if (!$this->key_account) {
            return [
                'status' => false,
                'detail' => 'API Key tidak ditemukan.'
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/get-devices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Authorization:' . $this->key_account
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        if (!isset($result['data'][0]['status'])) {
            return [
                'status' => false,
                'detail' => 'Gagal mendapatkan status perangkat.'
            ];
        }

        return [
            'status' => true,
            'device_status' => $result['data'][0]['status'] // "connect" atau "disconnect"
        ];
    }


    public function connectDevice()
    {
        if (!$this->key) {
            return [
                'status' => false,
                'detail' => 'API Key tidak ditemukan.'
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/connect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->key
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true); // Mengembalikan respons dalam bentuk array
    }


    /**
     * Fungsi untuk memutuskan koneksi WhatsApp dari Fonnte.
     */
    public function disconnectDevice()
    {
        if (!$this->key) {
            return [
                'status' => false,
                'detail' => 'API Key tidak ditemukan.'
            ];
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/disconnect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->key
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true); // Mengembalikan respons dalam bentuk array
    }
}
