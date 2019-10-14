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
            if (isset($config['error']) && $config['exception'] instanceof MailgunExtendedExceptionInterface) {
                new $config['error']($message, $e);
                return null;
            }
            throw $e;
        }

        $this->sendPerformed($message);

        if (isset($config['process']) && $config['process'] instanceof MailgunExtendedProccessInterface) {
            new $config['process']($message, $response);
        }

        return $this->numberOfRecipients($message);
    }
}
