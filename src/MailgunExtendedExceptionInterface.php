<?php

namespace LaravelMailgunExtended;

interface MailgunExtendedExceptionInterface
{
    public function __construct(Swift_Mime_SimpleMessage $message, \Exception $e);
}
