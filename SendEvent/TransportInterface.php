<?php

namespace Lakestone\SubCommon\Service\SendEvent;

use Amp\Promise;
use Attribute;

#[Attribute]
interface TransportInterface {
  
  function sendEvent(EventInterface $event): null|Promise;
  
}