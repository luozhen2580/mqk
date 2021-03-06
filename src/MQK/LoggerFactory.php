<?php
namespace MQK;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerFactory
{
    private $defaultLevel = Logger::WARNING;

    /**
     * @var LoggerFactory
     */
    private static $shared;

    public function defaultLevel()
    {
        return $this->defaultLevel;
    }

    public function setDefaultLevel($level)
    {
        $this->defaultLevel = $level;
    }

    /**
     * Logger的工厂方法
     *
     * @param $name
     * @param integer $level Logger level
     * @return Logger
     */
    public function getLogger($name, $level=null)
    {
        $logger = new Logger($name);
        $handler = new StreamHandler("php://stdout");
        $pid = posix_getpid();
        $output = "[%datetime%] {$pid} %channel%.%level_name%: %message% %context% %extra%\n";

        $formatter = new LineFormatter($output);
        $handler->setFormatter($formatter);

        if ($level)
            $handler->setLevel($level);
        else
            $handler->setLevel($this->defaultLevel);
        $logger->pushHandler($handler);
        return $logger;
    }

    public function cliLogger()
    {
        $config = Config::defaultConfig();
        if ($config->quite()) {
            $level = Logger::NOTICE;
        } else {
            $level = Logger::INFO;
        }

        return $this->getLogger("", $level);
    }

    public static function shared()
    {
        if (null == self::$shared) {
            self::$shared = new LoggerFactory();
        }

        return self::$shared;
    }
}