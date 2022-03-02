<?php

namespace Lakestone\SubCommon\Service;

use Amp\Http\Client\HttpException;
use Amp\Loop;
use Amp\Promise;
use JetBrains\PhpStorm\ArrayShape;
use Lakestone\SubCommon\Service\SendEvent\GraylogEvent;
use Lakestone\SubCommon\Service\SendEvent\EventInterface;
use Lakestone\SubCommon\Service\SendEvent\LoggerTransport;
use Lakestone\SubCommon\Service\SendEvent\TransportInterface;
use Lakestone\Trait\LoggedSingleton;
use Lakestone\Trait\Singleton;
use function Amp\call;

/**
 * __Attributes:__
 * - Transport.
 * You can set transport by TransportClass attribute.
 * The TransportClass MUST be implements to TransportInterface
 * - Event.
 * You can set default EventClass by EventInterface attribute
 * with one argument which be contents class of you events.
 *
 * __Methods:__
 * - pushEvent() pushes new event to sending
 * - setGroup() sets default _groupName_ for any event
 * - setSubject() sets default _subject_ for any event
 */
abstract class SendEventsAbstract {
  
  use LoggedSingleton {
    getInstance as getBaseInstance;
  }
  
  const limitPoll = 10;
  
  #[ArrayShape([SendEvent\GraylogEvent::class])]
  protected array $poll = [];
  protected int $limitPoll = self::limitPoll;
  protected ?string $groupName = null;
  protected ?string $subject = null;
  protected TransportInterface $transport;
  protected string $eventClass = GraylogEvent::class;
  
  static public function getInstance(): static {
    if (static::$instance == null) {
      static::$instance = static::getBaseInstance();
      $reflection = new \ReflectionObject(static::$instance);
      foreach ($reflection->getAttributes() as $attribute) {
        if ($attribute->getName() == EventInterface::class) {
          $arguments = $attribute->getArguments();
          static::$instance->eventClass = array_shift($arguments);
        } elseif (class_exists($attribute->getName())) {
          switch (true) {
            case self::checkInterface($attribute->getName(), TransportInterface::class):
              static::$instance->transport = $attribute->newInstance();
              break;
          }
        }
      }
      if (empty(static::$instance->transport)) {
        static::$instance->transport = new LoggerTransport();
      }
    }
    
    return static::$instance;
  }
  
  /**
   * Pushes new event.
   * You can push EventInterface $event, or you can push separately $body of the event
   * and optional $group and $subject. $body MUST be stringable.
   * @param mixed               $body
   * @param string|null         $groupName
   * @param string|null         $subject
   * @param EventInterface|null $event
   * @return void
   */
  public function pushEvent(mixed $body = '', string $groupName = null, string $subject = null, EventInterface $event = null): void {
    try {
      if (empty($event)) {
        $groupName = $groupName ?? $this->groupName;
        $subject = $subject ?? $this->subject;
        /**
         * @var EventInterface $event
         */
        $event = (new $this->eventClass())
            ->setBody($body)
            ->setSubject($subject)
            ->setGroup($groupName);
      }
      $this->poll[] = $event;
      $this->checkLimitPoll();
    } catch (\Throwable $e) {
      $this->logger->error($e->getMessage(), ['exception' => $e]);
    }
  }
  
  public function __destruct() {
    if (php_sapi_name() == 'fpm-fcgi') {
      fastcgi_finish_request();
    }
    $this->transferEvents($this->poll);
  }
  
  protected function checkLimitPoll(): void {
    if (sizeof($this->poll) >= $this->limitPoll) {
      $this->transferEvents(array_splice($this->poll, 0, $this->limitPoll));
    }
  }
  
  /**
   * @param EventInterface[] $events
   * @return void
   */
  protected function transferEvents(array $events): void {
    foreach ($events as $event) {
      Loop::defer(function () use ($event) {
        try {
          $promise = $this->transport->sendEvent($event);
          if ($promise instanceof Promise) {
            yield $promise;
          }
        } catch (\Throwable $e) {
          $this->logger->error('Unable to send event: ' . $e->getMessage(), ['exception' => $e]);
        }
      });
    }
    Loop::run();
  }
  
  private static function checkInterface(string $className, string $interfaceName) {
    return in_array($interfaceName, class_implements($className));
  }
  
  /**
   * @return int
   */
  public function getLimitPoll(): int {
    return $this->limitPoll;
  }
  
  /**
   * @param int $limitPoll
   * @return SendEventsAbstract
   */
  public function setLimitPoll(int $limitPoll): SendEventsAbstract {
    $this->limitPoll = $limitPoll;
    return $this;
  }
  
  /**
   * @return string|null
   */
  public function getGroupName(): ?string {
    return $this->groupName;
  }
  
  /**
   * @param string|null $groupName
   * @return SendEventsAbstract
   */
  public function setGroupName(?string $groupName): SendEventsAbstract {
    $this->groupName = $groupName;
    return $this;
  }
  
  /**
   * @return string|null
   */
  public function getSubject(): ?string {
    return $this->subject;
  }
  
  /**
   * @param string|null $subject
   * @return SendEventsAbstract
   */
  public function setSubject(?string $subject): SendEventsAbstract {
    $this->subject = $subject;
    return $this;
  }
  
  /**
   * @return TransportInterface
   */
  public function getTransport(): TransportInterface {
    return $this->transport;
  }
  
  /**
   * @param TransportInterface $transport
   * @return SendEventsAbstract
   */
  public function setTransport(TransportInterface $transport): SendEventsAbstract {
    $this->transport = $transport;
    return $this;
  }
  
  
}