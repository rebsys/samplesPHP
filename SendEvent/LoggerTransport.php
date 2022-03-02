<?php

namespace Lakestone\SubCommon\Service\SendEvent;

use Amp\LazyPromise;
use Amp\Promise;
use Attribute;
use Lakestone\Service\Logger;
use Monolog\Logger as LoggerMonolog;

#[Attribute]
class LoggerTransport implements TransportInterface {
  
  protected LoggerMonolog $logger;
  
  /**
   * Setups monolog logger. $level MUST be level number (__monolog__) or name (PSR-3)
   * @param int|string $level Level number (monolog) or name (PSR-3)
   * @param string     $logger_id
   * @param string     $file
   */
  public function __construct(
      protected int|string $level = LOG_INFO,
      string               $logger_id = 'app',
      string               $file = 'app.log'
  ) {
    $this->logger = (new Logger())->getLogger($logger_id, $file);
  }
  
  function sendEvent(CommonEvent|EventInterface $event): Promise|null {
    $prefix = $event->getGroup() . ':' . $event->getSubject();
    if ($prefix == ':') {
      $prefix = '';
    } else {
      $prefix = '[' . $prefix . '] ';
    }
    try {
      $message = (string) $event->getBody();
    } catch (\Throwable $e) {
      $message = json_encode($event->getBody());
    }
    $this->logger->log(
        $this->level,
        $prefix . $message,
        [
            'createdAt' => $event->getTimeCreated(),
            'sequence' => $event->getSequence(),
        ]
    );
    return null;
  }
}