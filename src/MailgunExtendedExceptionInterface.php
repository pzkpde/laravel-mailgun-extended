<?php

namespace LaravelMailgunExtended;

interface MailgunExtendedExceptionInterface
{
    public function handle(\Swift_Mime_SimpleMessage $message, \Exception $e);
}
