<?php

namespace LaravelMailgunExtended;

use Illuminate\Mail\MailServiceProvider;

class ExtendedMailServiceProvider extends MailServiceProvider
{
    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new ExtendedTransportManager($app);
        });
    }
}
