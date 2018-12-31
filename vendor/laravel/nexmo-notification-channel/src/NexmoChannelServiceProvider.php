<?php

namespace Illuminate\Notifications;

use Nexmo\Client as NexmoClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Nexmo\Client\Credentials\Basic as NexmoCredentials;

class NexmoChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Notification::extend('nexmo', function ($app) {
            return new Channels\NexmoSmsChannel(
                new NexmoClient(new NexmoCredentials(
                    $this->app['config']['services.nexmo.key'],
                    $this->app['config']['services.nexmo.secret']
                )),
                $this->app['config']['services.nexmo.sms_from']
            );
        });
    }
}
