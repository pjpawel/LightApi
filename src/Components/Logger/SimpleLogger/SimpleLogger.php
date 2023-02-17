<?php

namespace pjpawel\LightApi\Components\Logger\SimpleLogger;

use DateTime;
use DateTimeZone;
use Exception;
use Psr\Log\LoggerInterface;

class SimpleLogger implements LoggerInterface
{

    private const SEPARATOR = ' ';

    private Level $logLevel;
    private string $logFilePath;
    private DateTimeZone $timeZone;

    public function __construct(string $logFilePath, Level $logLevel = Level::Info)
    {
        if (!is_file($logFilePath)) {
            $this->createLogFile($logFilePath);
        }
        $this->logFilePath = realpath($logFilePath);
        $this->logLevel = $logLevel;
        $this->timeZone = new DateTimeZone(date_default_timezone_get());
    }

    /**
     * @throws Exception
     */
    private function createLogFile(string $filePath): void
    {
        if (!file_put_contents($filePath, 'Started logging...')) {
            throw new Exception('Cannot create log file');
        }
        chmod($filePath, 0777);
    }

    private function addRecord(Level $level, string $message, array $context): void
    {
        if ($level->isToLog($this->logLevel)) {
            $message = (new DateTime('now', $this->timeZone))->format(DATE_RFC3339_EXTENDED)
                . self::SEPARATOR . $level->name . self::SEPARATOR . $message;
            if (!empty($context)) {
                $message .= self::SEPARATOR . var_export($context, true);
            }
            file_put_contents($this->logFilePath, $message . PHP_EOL, FILE_APPEND);
        }
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Emergency, $message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Alert, $message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Critical, $message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Error, $message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Warning, $message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Notice, $message, $context);
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Info, $message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Emergency, $message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->addRecord(Level::Emergency, $message, $context);
    }
}