<?php

namespace App\Services;

use RouterosAPI;

class MikrotikService
{
    protected $api;

    public function __construct()
    {
        $this->api = new \RouterosAPI();
        $this->api->debug = false; // Enable or disable debug
    }

    public function connect()
    {
        return $this->api->connect(
            env('MIKROTIK_HOST'),
            env('MIKROTIK_USERNAME'),
            env('MIKROTIK_PASSWORD'),
            env('MIKROTIK_PORT', 8728) // default port
        );
    }

    public function disconnect()
    {
        $this->api->disconnect();
    }

    public function getInterfaces()
    {
        if ($this->connect()) {
            $this->api->write('/interface/print');
            $interfaces = $this->api->read();
            $this->disconnect();
            return $interfaces;
        }

        return null;
    }

    // Add more methods for other Mikrotik operations
}
