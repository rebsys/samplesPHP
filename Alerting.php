<?php

namespace Lakestone\SubCommon\Service;

use Lakestone\SubCommon\Service\SendEvent\CommonEvent;
use Lakestone\SubCommon\Service\SendEvent\EventInterface;
use Lakestone\SubCommon\Service\SendEvent\LoggerTransport;
use Monolog\Logger;

#[LoggerTransport(Logger::ALERT)]
#[EventInterface(CommonEvent::class)]
final class Alerting extends SendEventsAbstract {
  
  protected static $instance = null;
  
}
