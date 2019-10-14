<?php

namespace LaravelMailgunExtended;

interface MailgunExtendedProcessInterface
{
    public function __construct(Swift_Mime_SimpleMessage $message, $response);
}
