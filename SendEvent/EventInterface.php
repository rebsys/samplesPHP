<?php

namespace Lakestone\SubCommon\Service\SendEvent;

use Attribute;

#[Attribute]
interface EventInterface {
  
  /**
   * @return mixed
   */
  public function getBody(): mixed;
  
  /**
   * @return string|null
   */
  public function getSubject(): ?string;
  
  /**
   * @return string|null
   */
  public function getGroup(): ?string;
  
  /**
   * @param string|null $subject
   * @return self
   */
  public function setSubject(?string $subject): self;
  
  /**
   * @param string|null $group
   * @return self
   */
  public function setGroup(?string $group): self;
  
  /**
   * @param mixed $body
   * @return self
   */
  public function setBody(mixed $body): self;
}