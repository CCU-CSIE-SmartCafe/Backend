<?php

namespace SmartCafe\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

class ValidateFail extends Exception
{
    public function __construct(MessageBag $message, int $code = 0, Exception $previous = null)
    {
        $message = json_encode($message->toArray());
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): MessageBag
    {
        $errors = json_decode($this->message);
        $messageBag = new MessageBag((array) $errors);

        return $messageBag;
    }
}
