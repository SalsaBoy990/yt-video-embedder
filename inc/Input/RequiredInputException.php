<?php

namespace AG\YTVideoEmbedder\Input;

defined('ABSPATH') or die();

/**
 * Define a custom exception class
 */
class RequiredInputException extends FormException
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function recoveryFunction()
    {
    }
}