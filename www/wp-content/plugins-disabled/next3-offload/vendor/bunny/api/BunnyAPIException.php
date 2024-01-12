<?php
namespace Next3Bunny\BunnyCdn;

use \Exception;

class BunnyAPIException extends exception
{
    public function errorMessage(): string
    {//Error message
        return "Error on line {$this->getLine()} in {$this->getFile()}. {$this->getMessage()}.";
    }
}