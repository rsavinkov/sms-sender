<?php

namespace rsavinkov\SmsSender\Input;

use rsavinkov\SmsSender\Error\BadRequestError;
use rsavinkov\SmsSender\Error\InvalidParameterError;
use rsavinkov\SmsSender\Error\MethodNotAllowedError;

class RequestConverter
{
    private const PARAM_RECIPIENT = 'recipient';
    private const PARAM_ORIGINATOR = 'originator';
    private const PARAM_MESSAGE = 'message';

    private const MAX_MESSAGE_LENGTH_GSM = 160;
    private const MAX_MESSAGE_LENGTH_UNICODE = 70;

    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromJson($json)
    {
        $jsonData = json_decode($json, true);
        if (!is_array($jsonData)) {
            throw new BadRequestError('Request doesn\'t contain valid JSON');
        }

        return new self($jsonData);
    }

    public function getRequest()
    {
        return new Request(
            $this->getRecipient(),
            $this->getOriginator(),
            $this->getMessage()
        );
    }

    private function getRecipient(): string
    {
        $recipientPhoneNumber = $this->getParameterAsString(self::PARAM_RECIPIENT);

        if (!$this->isValidPhoneNumber($recipientPhoneNumber)) {
            throw new InvalidParameterError(self::PARAM_RECIPIENT, 'It is not valid phone number');
        }

        return $recipientPhoneNumber;
    }

    private function getOriginator(): string
    {
        $originator = $this->getParameterAsString(self::PARAM_ORIGINATOR);

        if (!$this->isValidPhoneNumber($originator) && !$this->isValidOriginatorName($originator)) {
            throw new InvalidParameterError(self::PARAM_ORIGINATOR, 'It is not valid originator name');
        }

        return $originator;
    }

    private function getMessage(): string
    {
        $message = $this->getParameterAsString(self::PARAM_MESSAGE);
        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH_GSM) {
            throw new InvalidParameterError(self::PARAM_MESSAGE, 'Max length is 160 characters (GSM)');
        } elseif (mb_strlen($message) > self::MAX_MESSAGE_LENGTH_UNICODE && !$this->isGSM0338($message)) {
            throw new InvalidParameterError(self::PARAM_MESSAGE, 'Max length is 70 characters (unicode)');
        }

        return $message;
    }

    private function isValidPhoneNumber($phoneNumber): bool
    {
        return (bool)preg_match('/^[1-9]{1}[0-9]{3,14}$/', $phoneNumber);
    }

    private function isValidOriginatorName($originator)
    {
        // alphanumeric string, the maximum length is 11
        return (bool)preg_match('/^[A-Za-z0-9]{1,11}$/', $originator);
    }

    private function isGSM0338($string)
    {
        $stringUTF8 = mb_convert_encoding($string, 'UTF-8');
        $gsm0338 = array(
            '@', 'Δ', ' ', '0', '¡', 'P', '¿', 'p',
            '£', '_', '!', '1', 'A', 'Q', 'a', 'q',
            '$', 'Φ', '"', '2', 'B', 'R', 'b', 'r',
            '¥', 'Γ', '#', '3', 'C', 'S', 'c', 's',
            'è', 'Λ', '¤', '4', 'D', 'T', 'd', 't',
            'é', 'Ω', '%', '5', 'E', 'U', 'e', 'u',
            'ù', 'Π', '&', '6', 'F', 'V', 'f', 'v',
            'ì', 'Ψ', '\'', '7', 'G', 'W', 'g', 'w',
            'ò', 'Σ', '(', '8', 'H', 'X', 'h', 'x',
            'Ç', 'Θ', ')', '9', 'I', 'Y', 'i', 'y',
            "\n", 'Ξ', '*', ':', 'J', 'Z', 'j', 'z',
            'Ø', "\x1B", '+', ';', 'K', 'Ä', 'k', 'ä',
            'ø', 'Æ', ',', '<', 'L', 'Ö', 'l', 'ö',
            "\r", 'æ', '-', '=', 'M', 'Ñ', 'm', 'ñ',
            'Å', 'ß', '.', '>', 'N', 'Ü', 'n', 'ü',
            'å', 'É', '/', '?', 'O', '§', 'o', 'à'
        );

        for ($i = 0; $i < mb_strlen($stringUTF8); $i++) {
            if (!in_array(mb_substr($stringUTF8, $i, 1), $gsm0338)) {
                return false;
            }
        }

        return true;
    }

    private function getParameterAsString(string $parameterName): string
    {
        if (!isset($this->data[$parameterName])) {
            throw new InvalidParameterError($parameterName, 'Parameter doesn\'t exist!');
        }

        return (string)$this->data[$parameterName];
    }
}
