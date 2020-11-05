<?php

namespace LaravelMailgunExtended;

class SmtpExtendedTransport extends Swift_SmtpTransport
{
    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param string[] $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        if (!$this->isStarted()) {
            $this->start();
        }

        $sent = 0;
        $failedRecipients = (array) $failedRecipients;

        if ($evt = $this->eventDispatcher->createSendEvent($this, $message)) {
            $this->eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        if (!$reversePath = $this->getReversePath($message)) {
            $this->throwException(new Swift_TransportException('Cannot send message without a sender address'));
        }

        $to = (array) $message->getTo();
        $cc = (array) $message->getCc();
        $tos = array_merge($to, $cc);
        $bcc = (array) $message->getBcc();

        $message->setBcc([]);

        try {
            $sent += $this->sendTo($message, $reversePath, $tos, $failedRecipients);
            $sent += $this->sendBcc($message, $reversePath, $bcc, $failedRecipients);
        } finally {
            $message->setBcc($bcc);
        }

        if ($evt) {
            if ($sent == count($to) + count($cc) + count($bcc)) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
            } elseif ($sent > 0) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_TENTATIVE);
            } else {
                $evt->setResult(Swift_Events_SendEvent::RESULT_FAILED);
            }
            $evt->setFailedRecipients($failedRecipients);
            $this->eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        $id = $message->generateId(); //Make sure a new Message ID is used
        if (function_exists('t_dump')) {
            t_dump('SmtpExtendedTransport: ' . $id);
        } else {
            \Log::info('SmtpExtendedTransport: ' . $id);
        }

        return $sent;
    }
}
