<?php

namespace pjpawel\LightApi\Component\Logger\SimpleLogger;

enum Level: int
{

    case Debug = 100;

    case Info = 200;

    case Notice = 250;

    case Warning = 300;

    case Error = 400;

    case Critical = 500;

    case Alert = 550;

    case Emergency = 600;

    /**
     * @param Level $level Minimal log level
     * @return bool
     */
    public function isToLog(Level $level): bool
    {
        return $this->value >= $level->value;
    }

    /*public static function fromName(string $name): self
    {
        return match ($name) {
            'debug', 'Debug', 'DEBUG' => self::Debug,
            'info', 'Info', 'INFO' => self::Info,
            'notice', 'Notice', 'NOTICE' => self::Notice,
            'warning', 'Warning', 'WARNING' => self::Warning,
            'error', 'Error', 'ERROR' => self::Error,
            'critical', 'Critical', 'CRITICAL' => self::Critical,
            'alert', 'Alert', 'ALERT' => self::Alert,
            'emergency', 'Emergency', 'EMERGENCY' => self::Emergency,
        };
    }*/

}
