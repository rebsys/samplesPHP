<?php

namespace Lakestone\SubCommon\Service\SendEvent;

use Attribute;

#[Attribute]
class GraylogEvent implements EventInterface {
  
  const stateNormal = 'normal';
  const stateWarning = 'warning';
  const stateError = 'error';
  
  const typeFlow = 'flow';
  
  const flowOrderProcessing = 'order_processing';
  
  protected string $timestamp;
  protected string $sequence;
  protected string $host;
  protected string $short_message;
  protected ?string $full_message = null;
  protected int $level = LOG_INFO;
  protected ?int $line = null;
  protected ?string $file = null;
  protected array $additionalFields = [];
  
  public function __construct() {
    $this->timestamp = microtime(true);
    $this->sequence = bin2hex(random_bytes(20));
    $this->host = gethostname();
  }
  
  public function setAdditionalField(string $field, mixed $value): self {
    $this->additionalFields[$field] = $value;
    return $this;
  }
  
  public function getAdditionalField(string $field): mixed {
    return $this->additionalFields[$field] ?? null;
  }
  
  public function getAdditionalFields(): array {
    return $this->additionalFields;
  }
  
  /**
   * @return string
   */
  public function getHost(): string {
    return $this->host;
  }
  
  /**
   * @param string $host
   * @return self
   */
  public function setHost(string $host): self {
    $this->host = $host;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getShortMessage(): string {
    return $this->short_message;
  }
  
  /**
   * @param string $short_message
   * @return self
   */
  public function setShortMessage(string $short_message): self {
    $this->short_message = $short_message;
    return $this;
  }
  
  /**
   * @return string|null
   */
  public function getFullMessage(): ?string {
    return $this->full_message;
  }
  
  /**
   * @param string $full_message
   * @return self
   */
  public function setFullMessage(string $full_message): self {
    $this->full_message = $full_message;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getLevel(): int {
    return $this->level;
  }
  
  /**
   * the standard syslog levels
   * @param int $level
   * @return self
   */
  public function setLevel(int $level): self {
    $this->level = $level;
    return $this;
  }
  
  /**
   * @return int|null
   */
  public function getLine(): ?int {
    return $this->line;
  }
  
  /**
   * @param int|null $line
   * @return self
   */
  public function setLine(?int $line): self {
    $this->line = $line;
    return $this;
  }
  
  /**
   * @return string|null
   */
  public function getFile(): ?string {
    return $this->file;
  }
  
  /**
   * @param string|null $file
   * @return self
   */
  public function setFile(?string $file): self {
    $this->file = $file;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getTimestamp(): string {
    return $this->timestamp;
  }
  
  /**
   * @return string
   */
  public function getSequence(): string {
    return $this->sequence;
  }
  
  public function setSubject(?string $subject): self {
    $this->setShortMessage($subject);
    return $this;
  }
  
  public function setGroup(?string $group): self {
    $this->setAdditionalField('Group', $group);
    return $this;
  }
  
  public function setBody(mixed $body): self {
    $this->setFullMessage($body);
    return $this;
  }

  public function getBody(): mixed {
    return $this->getFullMessage();
  }
  
  public function getSubject(): ?string {
    return $this->getShortMessage();
  }
  
  public function getGroup(): ?string {
    return $this->getAdditionalField('Group');
  }
  
}