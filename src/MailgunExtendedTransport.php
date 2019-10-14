<?php

namespace LaravelMailgunExtended;

use Illuminate\Mail\Transport\MailgunTransport;
use Swift_Mime_SimpleMessage;

class MailgunExtendedTransport extends MailgunTransport
{
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $config = config('services.mailgun.events');

        $this->beforeSendPerformed($message);

        $to = $this->getTo($message);

        $message->setBcc([]);
        $message->setContentType('text/html');

        $payload = $this->payload($message, $to);

        try {
            $response = $this->client->request(
                'POST',
                "https://{$this->endpoint}/v3/{$this->domain}/messages.mime",
                $payload
            );
        } catch (\Exception $e) {
            if (isset($config['exception'])) {
                new $config['exception']($message, $e);
                return null;
            }
            throw $e;
        }

        $this->sendPerformed($message);

        if (isset($config['process'])) {
            new $config['process']($message, $response);
        }

        return $this->numberOfRecipients($message);
    }
}
