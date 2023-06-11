<?php
declare(strict_types=1);

namespace logger;

class Logger
{
    public function write(string $message): void
    {
        echo $message;
    }

}