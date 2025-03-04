<?php

namespace App\Console\Commands;

use App\Enum\PlanType;
use App\Models\UserRecharge;
use App\Support\Mikrotik;
use Illuminate\Console\Command;

class CheckExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info("Starting the check-expired command...");


        // search for expired services
        $services = UserRecharge::where('expired_at', '<', now())
            ->where('status', 'on')
            ->get();
        $this->info("Found {$services->count()} expired services.");
        foreach ($services as $service) {
            /** @var UserRecharge $service */
            $client = Mikrotik::getClient($service->router->ip_address, $service->router->username, $service->router->password);
            if ($service->type == PlanType::HOTSPOT) {
                if ($service->plan->is_radius) {
                    //TODO
                } else {
                    if (! empty($service->plan->pool_expired_id)) {
                        $this->info("Setting user package to EXPIRED {$service->plan->pool_expired->pool_name}");
                        Mikrotik::setHotspotUserPackage($client, $service->username, 'EXPIRED '.$service->plan->pool_expired->pool_name);
                    } else {
                        $this->info("Removing user {$service->username}");
                        Mikrotik::removeHotspotUser($client, $service->username);
                    }
                    Mikrotik::removeHotspotActiveUser($client, $service->username);
                }
            } else {
                if ($service->plan->is_radius) {
                    //TODO
                } else {
                    if (! empty($service->plan->pool_expired_id)) {
                        $this->info("Setting user plan to EXPIRED {$service->plan->pool_expired->pool_name}");
                        Mikrotik::setPpoeUserPlan($client, $service->username, 'EXPIRED '.$service->plan->pool_expired->pool_name);
                    } else {
                        $this->info("Removing user {$service->username}");
                        Mikrotik::removePpoeUser($client, $service->username);
                    }

                    Mikrotik::removePpoeActive($client, $service->username);
                }
            }
            $service->status = 'off';
            $service->save();
            $this->info("$service->expired_at : $service->username : EXPIRED");
        }
    }
}
