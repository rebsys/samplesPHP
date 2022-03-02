<?php

namespace Lakestone\SubCommon\Service;

use Lakestone\SubCommon\Service\SendEvent\EventInterface;
use Lakestone\SubCommon\Service\SendEvent\GraylogEvent;
use Lakestone\SubCommon\Service\SendEvent\GraylogTransport;

#[GraylogTransport(graylogUrlConfigPath: 'graylog.syncInstances[0].url')]
#[EventInterface(GraylogEvent::class)]
final class SyncLogging extends SendEventsAbstract {
  
  protected static ?self $instance = null;
  protected static string $message;
  
  /**
   * Sends a $message with optional fields added from the $addFields array.
   * $addFields is an associative key:value array.
   * @param int                   $level
   * @param string                $message
   * @param array<string, string> $addFields
   * @return void
   */
  public static function send(
      array  $addFields = [],
      string $message = null,
      int    $level = LOG_INFO,
  ) {
    $event = (new GraylogEvent())
        ->setLevel($level)
        ->setShortMessage($message ?? static::$message);
    foreach ($addFields as $key => $value) {
      $event->setAdditionalField($key, $value);
    }
    
    static::getInstance()->pushEvent(event: $event);
    
  }
  
  /**
   * @return string
   */
  public static function getMessage(): string {
    return self::$message;
  }
  
  /**
   * @param string $message
   */
  public static function setMessage(string $message): void {
    self::$message = $message;
  }
  
}
