<?php

namespace LaravelMailgunExtended;

interface MailgunExtendedProcessInterface
{
    public function handle(\Swift_Mime_SimpleMessage $message, $response);
}
