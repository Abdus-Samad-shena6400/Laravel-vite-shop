<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:notify-orders')]
#[Description('Command description')]
class NotifyOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = new \App\Http\Controllers\OrderNotificationController();

        $controller->sendNotifications();

        $this->info('Order notifications sent.');
    }
}

