<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;

class MikrotikController extends Controller
{
    protected $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function showInterfaces()
    {
        $interfaces = $this->mikrotik->getInterfaces();

        if ($interfaces) {
            // return view('mikrotik.interfaces', ['interfaces' => $interfaces]);
        }

        return response()->json(['message' => 'Unable to connect to Mikrotik.'], 500);
    }
}
