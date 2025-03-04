<?php

//php artisan queue:work buat jalanin job   

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected $tokenDevice;
    /**
     * Create a new job instance.
     */
    public function __construct($phone, $message, $tokenDevice)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->tokenDevice = $tokenDevice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //6285157723971
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $this->phone,
                'message' => $this->message,
                'delay' => '5', 
                'countryCode' => '62', // optional
            ),
            CURLOPT_HTTPHEADER => [
                "Authorization: {$this->tokenDevice}"
            ],
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
}
