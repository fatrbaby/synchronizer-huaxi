<?php

namespace Bridge\Response;

class ErrorException extends \RuntimeException
{
    private static $errors = [
        38=>"invalid assert number",
        39=>"equipment does not exists",
    ];
    
    public function __construct($code)
    {
        $code = (int) $code;
        $message = $this->filterError($code);
        parent::__construct($message, $code);
    }
    
    public function getError()
    {
        return [
            'code' => $this->getCode(),
            'messages' => [
                $this->getMessage(),
            ]
        ];
    }
    
    private function filterError($code)
    {
        if (!isset(self::$errors[$code])) {
            restore_exception_handler();
            throw new \RuntimeException("invalid error code.");
        }
        
        return self::$errors[$code];
    }
}
