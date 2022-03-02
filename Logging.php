<?php

namespace Lakestone\SubCommon\Service;

use Lakestone\SubCommon\Service\SendEvent\GraylogEvent;
use Lakestone\SubCommon\Service\SendEvent\EventInterface;
use Lakestone\SubCommon\Service\SendEvent\GraylogTransport;

#[GraylogTransport()]
#[EventInterface(GraylogEvent::class)]
final class Logging extends SendEventsAbstract {
  
  protected static $instance = null;
  
  /**
   * Sends a $message with optional fields added from the $addFields array.
   * $addFields is an associative key:value array.
   * @param int                   $level
   * @param string                $message
   * @param array<string, string> $addFields
   * @return void
   */
  public static function send(int $level, string $message, array $addFields = []) {
    
    $event = (new GraylogEvent())
        ->setLevel($level)
        ->setShortMessage($message)
    ;
    foreach ($addFields as $key => $value) {
      $event->setAdditionalField($key, $value);
    }
    
    static::getInstance()->pushEvent(event: $event);
    
  }
  
  public static function log(int $level, string $message, array $addFields = [], array $context = []) {
    if (!empty($context)) {
      $replace = [];
      foreach ($context as $key => $val) {
        $replace['{' . $key . '}'] = $val;
      }
      $message = strtr($message, $replace);
    }
    self::send($level, $message, $addFields);
  }
  
  public static function emergency(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_EMERG, $message, $addFields, $context);
  }
  
  public static function alert(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_ALERT, $message, $addFields, $context);
  }
  
  public static function critical(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_CRIT, $message, $addFields, $context);
  }
  
  public static function error(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_ERR, $message, $addFields, $context);
  }
  
  public static function warning(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_WARNING, $message, $addFields, $context);
  }
  
  public static function notice(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_NOTICE, $message, $addFields, $context);
  }
  
  public static function info(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_INFO, $message, $addFields, $context);
  }
  
  public static function debug(string $message, array $addFields = [], array $context = []) {
    self::log(LOG_DEBUG, $message, $addFields, $context);
  }
  
}
