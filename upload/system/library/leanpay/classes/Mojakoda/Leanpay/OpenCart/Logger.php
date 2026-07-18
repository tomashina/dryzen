<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

namespace Mojakoda\Leanpay\OpenCart;

use Log;

class Logger
{
    const ERROR = 1;
    const DEBUG = 2;

    protected $logger;
    protected $debugMode;

    public function __construct(Log $logger, $debugMode = false)
    {
        $this->logger = $logger;
        $this->debugMode = $debugMode;
    }

    public function write($message, $level = self::ERROR)
    {
        if ($level != self::DEBUG || $this->debugMode) {
            $this->logger->write($message);
        }
    }
}