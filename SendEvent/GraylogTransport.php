<?php

namespace Lakestone\SubCommon\Service\SendEvent;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\LazyPromise;
use Amp\Promise;
use Attribute;
use Lakestone\Config;
use Lakestone\Service\Logger;
use Monolog\Logger as LoggerMonolog;

#[Attribute]
class GraylogTransport implements TransportInterface {
  
  protected HttpClient $httpClient;
  protected LoggerMonolog $logger;
  
  public function __construct(
      protected ?string     $graylogUrl = null,
      protected ?string     $graylogUrlConfigPath = null,
  ) {
    $this->httpClient = HttpClientBuilder::buildDefault();
    $this->graylogUrl = $graylogUrl
        ?? Config::getInstance()->getParam($graylogUrlConfigPath ?? '', null)
        ?? Config::getInstance()->getParam('graylog.instances[0].url');
    $this->logger = (new Logger())->getLogger();
  }
  
  function sendEvent(GraylogEvent|EventInterface $event): null|Promise {
    $body = [
        'host' => $event->getHost(),
        'short_message' => $event->getShortMessage(),
        'full_message' => $event->getFullMessage(),
        'timestamp' => $event->getTimestamp(),
        'level' => $event->getLevel(),
        'line' => $event->getLine(),
        'file' => $event->getFile(),
        '_sequence' => $event->getSequence(),
    ];
    foreach ($event->getAdditionalFields() as $field => $value) {
      $name = '_' . $field;
      while (isset($body[$name])) {
        $name = '_' . $name;
      }
      $body[$name] = $value;
    }
    return $this->httpClient->request(
        new Request(
            $this->graylogUrl,
            'POST',
            json_encode($body)
        )
    );
  }
}