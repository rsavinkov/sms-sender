<?php

namespace rsavinkov\SmsSender;

use MessageBird\Objects\Message;
use rsavinkov\SmsSender\Error\TooManyRequestsError;
use rsavinkov\SmsSender\Input\RequestConverter;
use rsavinkov\SmsSender\Error\InvalidParameterError;
use rsavinkov\SmsSender\Error\BadRequestError;
use rsavinkov\SmsSender\Output\Response;
use Throwable;

class Controller
{
    private $environment;
    private $messageService;

    public function __construct(MessageService $messageService, string $environment)
    {
        $this->messageService = $messageService;
        $this->environment = $environment;
    }

    public function sendMessageAction(): Response
    {
        if (!$this->isPost()) {
            return Response::methodNotAllowed()->addError('Method not allowed');
        }

        try {
            $json = file_get_contents('php://input');
            $request = RequestConverter::fromJson($json)->getRequest();

            $message             = new Message();
            $message->originator = $request->getOriginator();
            $message->recipients = [$request->getRecipient()];
            $message->body       = $request->getMessage();

            $this->messageService->sendMessage($message);

            return Response::ok();
        } catch (InvalidParameterError $error) {
            return Response::badRequest()->addError($error->getMessage(), $error->getParameterName());
        } catch (BadRequestError $error) {
            return Response::badRequest()->addError($error->getMessage());
        } catch (TooManyRequestsError $error) {
            return Response::tooManyRequests()->addError($error->getMessage());
        } catch (Throwable $exception) {
            error_log(date('Y-m-d h:i:s') . ' | ' . $exception->getMessage());
            return Response::internalServerError()->addError(
                $this->environment === ApplicationRegistry::ENVIRONMENT_PROD
                    ? 'Something went wrong.'
                    : $exception->getMessage()
            );
        }
    }

    private function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
