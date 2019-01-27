<?php

namespace rsavinkov\SmsSender\tests\Input;

use rsavinkov\SmsSender\Error\BadRequestError;
use rsavinkov\SmsSender\Error\InvalidParameterError;
use rsavinkov\SmsSender\Input\RequestConverter;

class RequestConverterTest extends \PHPUnit\Framework\TestCase
{
    public function testInvalidJson()
    {
        $this->expectException(BadRequestError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_JSON);
        RequestConverter::fromJson('{"message":"message";}');
    }

    public function testRecipientDoesntExist()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_PARAMETER_DOESNT_EXIST);
        RequestConverter::fromJson('{"originator":"Test","message":"test message"}')->getRequest();
    }

    public function testEmptyRecipient()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_PHONE_NUMBER);
        RequestConverter::fromJson('{"recipient":0, "originator":"Test","message":"test message"}')->getRequest();
    }

    public function testVeryLongRecipientPhoneNumber()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_PHONE_NUMBER);
        RequestConverter::fromJson('{"recipient":"1234567890987654321", "originator":"Test","message":"test message"}')->getRequest();
    }

    public function testInvalidRecipientPhoneNumber()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_PHONE_NUMBER);
        RequestConverter::fromJson('{"recipient":"123456lasdfjk7890987654321", "originator":"Test","message":"test message"}')->getRequest();
    }

    public function testOriginatorDoesntExist()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_PARAMETER_DOESNT_EXIST);
        RequestConverter::fromJson('{"recipient":79635064065,"message":"test message"}')->getRequest();
    }

    public function testEmptyOriginator()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_ORIGINATOR_NAME);
        RequestConverter::fromJson('{"recipient":79635064065,"originator":"","message":"test message"}')->getRequest();
    }

    public function testVeryLongOriginatorPhoneNumber()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_ORIGINATOR_NAME);
        RequestConverter::fromJson('{"recipient":79635064065, "originator":"1234567890987654321","message":"test message"}')->getRequest();
    }

    public function testVeryLongOriginatorName()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_ORIGINATOR_NAME);
        RequestConverter::fromJson('{"recipient":79635064065,"originator":"RomanSavinkov","message":"test message"}')->getRequest();
    }

    public function testInvalidOriginatorName()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_INVALID_ORIGINATOR_NAME);
        RequestConverter::fromJson('{"recipient":79635064065,"originator":"John Smith","message":"test message"}')->getRequest();
    }

    public function testMessageDoesntExist()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_PARAMETER_DOESNT_EXIST);
        RequestConverter::fromJson('{"recipient":79635064065,"originator":"Test"}')->getRequest();
    }

    public function testEmptyMessage()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_EMPTY_MESSAGE);
        RequestConverter::fromJson('{"recipient":79635064065,"originator":"Test","message":""}')->getRequest();
    }

    public function testVeryLongGsmMessage()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_MESSAGE_MAX_LENGTH_160);
        RequestConverter::fromJson(
<<<EOS
{
    "recipient":79635064065,
    "originator":"Test",
    "message":"aaaaaaaaa bbbbbbbbb ccccccccc aaaaaaaaa bbbbbbbbb ccccccccc aaaaaaaaa bbbbbbbbb ccccccccc aaaaaaaaa bbbbbbbbb ccccccccc aaaaaaaaa bbbbbbbbb ccccccccc aaaaaaaaa bbbbbbbbb ccccccccc "
}
EOS
        )->getRequest();
    }

    public function testVeryLongUnicodeMessage()
    {
        $this->expectException(InvalidParameterError::class);
        $this->expectExceptionMessage(RequestConverter::ERROR_MESSAGE_MAX_LENGTH_70);
        RequestConverter::fromJson(
<<<EOS
{
    "recipient":79635064065,
    "originator":"Test",
    "message":"ййййййййй ыыыыыыыыы щщщщщщщщщ ййййййййй ыыыыыыыыы щщщщщщщщщ ййййййййй ыыыыыыыыы щщщщщщщщщ "
}
EOS
        )->getRequest();
    }

    public function testHappyPath()
    {
        $request = RequestConverter::fromJson('{"recipient":79635064065,"originator":"Test","message":"message"}')
            ->getRequest();

        $this->assertEquals($request->getRecipient(), "79635064065");
        $this->assertEquals($request->getOriginator(), "Test");
        $this->assertEquals($request->getMessage(), "message");
    }
}
