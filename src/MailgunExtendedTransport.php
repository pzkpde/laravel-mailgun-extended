<?php

namespace LaravelMailgunExtended;

use Illuminate\Mail\Transport\MailgunTransport;
use Swift_Mime_SimpleMessage;

class MailgunExtendedTransport extends MailgunTransport
{
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $config = config('services.mailgun.events');

        $process = new $config['process'];
        $exception = new $config['exception'];

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

            $this->sendPerformed($message);

            $process->handle($message, $response);

        } catch (\Exception $e) {

            $exception->handle($message, $e);
            throw $e;
        }

        return $this->numberOfRecipients($message);
    }
}
