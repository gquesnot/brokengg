<?php

namespace App\Enums;

enum FLashEnum: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';

    public function getColor(): string
    {
        return match ($this) {
            self::SUCCESS => 'green',
            self::ERROR => 'red',
            self::WARNING => 'yellow',
            self::INFO => 'blue',
        };
    }

    public function getMessage(): string
    {
        return match ($this) {
            self::SUCCESS => 'Success',
            self::ERROR => 'Error',
            self::WARNING => 'Warning',
            self::INFO => 'Info',
        };
    }
}
