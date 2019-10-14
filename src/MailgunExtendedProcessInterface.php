<?php

namespace LaravelMailgunExtended;

interface MailgunExtendedProcessInterface
{
    public function __construct($message, $response);
}
