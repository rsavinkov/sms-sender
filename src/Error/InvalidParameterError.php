<?php

namespace rsavinkov\SmsSender\Error;

class InvalidParameterError extends BadRequestError
{
    private $parameterName;

    public function __construct($parameterName, $message)
    {
        $this->parameterName = $parameterName;
        parent::__construct($message);
    }

    public function getParameterName()
    {
        return $this->parameterName;
    }
}
