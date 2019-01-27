<?php

namespace rsavinkov\SmsSender\Input;


class Request
{
    private $recipient;
    private $originator;
    private $message;

    public function __construct(string $recipient, string $originator, string $message)
    {
        $this->recipient = $recipient;
        $this->originator = $originator;
        $this->message = $message;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getOriginator(): string
    {
        return $this->originator;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
