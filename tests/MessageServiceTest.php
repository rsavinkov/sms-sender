<?php
namespace rsavinkov\SmsSender\tests;

use MessageBird\Objects\Message;
use rsavinkov\SmsSender\Error\TooManyRequestsError;
use rsavinkov\SmsSender\MessageService;

class MessageServiceTest extends \PHPUnit\Framework\TestCase
{
    public function testRateLimit()
    {
        $message = new Message();
        $message->recipients = ['79635064065'];
        $message->originator = 'Test';
        $message->body = 'message';

        $mock = $this->getMockBuilder(MessageService::class)
            ->disableOriginalConstructor()
            ->setMethods(['createMessage'])
            ->getMock();
        $mock->expects($this->once())->method('createMessage');
        $mock->sendMessage($message);

        $this->expectException(TooManyRequestsError::class);
        $mock->sendMessage($message);
    }
}
