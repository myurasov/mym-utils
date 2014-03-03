<?php

/**
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace mym\Util;

use Psr\Log\AbstractLogger;

class ArrayLogger extends AbstractLogger {

  private $log = array();

  public function log($level, $message, array $context = array())
  {
    $this->log[] = $message;
  }

  public function setLog($log)
  {
    $this->log = $log;
  }

  public function getLog()
  {
    return $this->log;
  }
}
