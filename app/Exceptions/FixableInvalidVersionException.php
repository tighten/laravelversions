<?php

namespace App\Exceptions;

use Exception;

class FixableInvalidVersionException extends Exception
{
    public $redirectTo;

    public function __construct($message, $code = 0, Exception $previous = null, $redirectTo)
    {
        $this->redirectTo = $redirectTo;

        parent::__construct($message, $code, $previous);
    }

    public static function toUrl($redirectTo)
    {
        return new static(
            'Redirect to ' . $redirectTo,
            0,
            null,
            $redirectTo
        );
    }

    public function report()
    {
        return false;
    }

    public function render()
    {
        return redirect($this->redirectTo);
    }
}
