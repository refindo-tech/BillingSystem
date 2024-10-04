<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccurateController extends Controller
{
    public function redirectToAccurate()
    {
        $query = http_build_query([
            'client_id' => env('ACCURATE_CLIENT_ID'),
            'redirect_uri' => env('ACCURATE_REDIRECT_URI'),
            'response_type' => 'code', // atau 'token'
            'scope' => 'item_view item_save', // sesuaikan dengan scope yang diinginkan
        ]);

        return redirect('https://account.accurate.id/oauth/authorize?' . $query);
    }

    public function handleAccurateCallback(Request $request)
    {
        $response = Http::asForm()->post('https://account.accurate.id/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('ACCURATE_CLIENT_ID'),
            'client_secret' => env('ACCURATE_CLIENT_SECRET'),
            'redirect_uri' => env('ACCURATE_REDIRECT_URI'),
            'code' => $request->code,
        ]);

        $accessToken = $response->json()['access_token'];

        // Save the access token to the .env or database for future requests
        env(['ACCURATE_ACCESS_TOKEN' => $accessToken]);

        return redirect('/')->with('success', 'Access token retrieved successfully');
        
    }

    public function getAccurateData()
    {
        $response = Http::withToken(env('ACCURATE_ACCESS_TOKEN'))
            ->get('https://account.accurate.id/api/some-endpoint');

        return $response->json();
    }
}