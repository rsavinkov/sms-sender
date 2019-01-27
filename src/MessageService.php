<?php

namespace rsavinkov\SmsSender;

use MessageBird\Client;
use MessageBird\Objects\Message;
use rsavinkov\SmsSender\Error\TooManyRequestsError;
use Throwable;

class MessageService
{
    private $messageBirdClient;

    public function __construct(Client $messageBirdClient)
    {
        $this->messageBirdClient = $messageBirdClient;
    }

    public function sendMessage(Message $message)
    {
        $isLocked = false;
        $tooManyRequests = false;
        try {
            $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "send_message.txt";
            $fp = fopen($filename, "c+");

            /*
             * It is better to use something like Redis for this, instead of files,
             * but I think fot this project it is enough.
             */
            if ($isLocked = flock($fp, LOCK_EX)) {
                $lastMicroTime = (float) fread($fp, 4096);
                if (microtime(true) - $lastMicroTime > 1) {

                    $messageResult = $this->createMessage($message);

                    rewind($fp);
                    ftruncate($fp, 0);
                    fwrite($fp, (string)microtime(true), 4096);
                    fflush($fp);
                } else {
                    $tooManyRequests = true;
                }

                flock($fp, LOCK_UN);
                $isLocked = false;
            } else {
                $tooManyRequests = true;
            }
            fclose($fp);
        } catch (Throwable $exception) {
            if ($isLocked) {
                flock($fp, LOCK_UN);
            }
            throw $exception;
        }
        if ($tooManyRequests) {
            throw new TooManyRequestsError('Exceed limit 1 request per second');
        }

        return $messageResult;
    }

    protected function createMessage(Message $message)
    {
        return $this->messageBirdClient->messages->create($message);
    }
}
