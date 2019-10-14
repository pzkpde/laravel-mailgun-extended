<?php

namespace LaravelMailgunExtended;

interface MailgunExtendedExceptionInterface
{
    public function __construct($message, \Exception $e);
}
