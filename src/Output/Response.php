<?php

namespace rsavinkov\SmsSender\Output;


class Response
{
    private $code;
    private $success;
    private $errors;

    public static function ok()
    {
        return new self(200, true);
    }

    public static function badRequest()
    {
        return new self(400, false);
    }

    public static function methodNotAllowed()
    {
        return new self(405, false);
    }

    public static function internalServerError()
    {
        return new self(400, false);
    }

    public function __construct(int $code, bool $success)
    {
        $this->code = $code;
        $this->success = $success;
    }

    public function addError(string $message, ?string $field = null): self
    {
        $this->success = false;
        $this->errors[] = $field
            ? ['field' => $field, 'message' => $message]
            : ['message' => $message];

        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    public function toArray()
    {
        $arrResult = ['success' => $this->success];
        if (!$this->success) {
            $arrResult['errors'] = $this->errors;
        }

        return $arrResult;
    }
}
