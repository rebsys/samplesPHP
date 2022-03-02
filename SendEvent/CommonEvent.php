<?php

namespace Lakestone\SubCommon\Service\SendEvent;

use Attribute;

#[Attribute]
class CommonEvent implements EventInterface {
  
  protected int $timeCreated;
  protected string $sequence;
  
  public function __construct(
      protected mixed $body = null,
      protected ?string $group = null,
      protected ?string $subject = null,
  ) {
    $this->timeCreated = intval(microtime(true) * 10000);
    $this->sequence = bin2hex(random_bytes(20));
  }
  
  /**
   * @return mixed
   */
  public function getBody(): mixed {
    return $this->body;
  }
  
  /**
   * @param mixed $body
   * @return self
   */
  public function setBody(mixed $body): self {
    $this->body = $body;
    return $this;
  }
  
  /**
   * @return string|null
   */
  public function getGroup(): ?string {
    return $this->group;
  }
  
  /**
   * @param string|null $group
   * @return self
   */
  public function setGroup(?string $group): self {
    $this->group = $group;
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
   * @return self
   */
  public function setSubject(?string $subject): self {
    $this->subject = $subject;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getTimeCreated(): int {
    return $this->timeCreated;
  }
  
  /**
   * @param int $timeCreated
   * @return self
   */
  public function setTimeCreated(int $timeCreated): self {
    $this->timeCreated = $timeCreated;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getSequence(): string {
    return $this->sequence;
  }
  
  /**
   * @param string $sequence
   * @return self
   */
  public function setSequence(string $sequence): self {
    $this->sequence = $sequence;
    return $this;
  }
  
}