<?php

namespace LaravelMailgunExtended;

use Illuminate\Mail\TransportManager;

class ExtendedTransportManager extends TransportManager
{
    /**
     * Create an instance of the Mailgun Swift Transport driver.
     *
     * @return MailgunExtendedTransport
     */
    protected function createMailgunDriver()
    {
        $config = $this->app['config']->get('services.mailgun', []);

        return new MailgunExtendedTransport(
            $this->guzzle($config),
            $config['secret'],
            $config['domain'],
            $config['endpoint'] ?? null
        );
    }
}
