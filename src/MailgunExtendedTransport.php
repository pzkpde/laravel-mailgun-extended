<?php

namespace LaravelMailgunExtended;

use Illuminate\Mail\Transport\MailgunTransport;
use Swift_Mime_Message;

class MailgunExtendedTransport extends MailgunTransport
{
    const IS_FAKE_TRANSPORT = ':is_fake:';

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $config = config('services.mailgun.events');

        $process = new $config['process'];
        $exception = new $config['exception'];

        if (isset($config['fake']) && $config['fake']) {
            $process->handle($message, self::IS_FAKE_TRANSPORT);
            return count($message->getTo());
        }

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
