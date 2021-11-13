<?php

namespace Drupal\biertje\Services;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GetBeerService.
 */
class GetBeerService implements ContainerInjectionInterface {

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected Client $httpClient;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The cache bin we can use to store responses in.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected CacheBackendInterface $cache;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * A logger to write to the log.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * GetBeerService constructor.
   *
   * @param \Drupal\Core\Http\ClientFactory $httpClientFactory
   *   Guzzle HTTP client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The configuration factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   Our cache bin to cache responses.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   Our logger channel.
   */
  public function __construct(
    ClientFactory $httpClientFactory,
    ConfigFactoryInterface $config,
    CacheBackendInterface $cache,
    TimeInterface $time,
    LoggerChannelInterface $logger
  ) {
    $this->configFactory = $config;
    $this->httpClient = $httpClientFactory->fromOptions([
      'base_uri' => $this->getUri(),
    ]);
    $this->cache = $cache;
    $this->time = $time;
    $this->logger = $logger;
  }

  /**
   * Instantiates a new instance of this class.
   *
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client_factory'),
      $container->get('config.factory'),
      $container->get('cache.biertje'),
      $container->get('datetime.time'),
      $container->get('logger.channel.biertje'),
    );
  }

  /**
   * Get a beer from cache or from the API.
   *
   * @param string $endpoint
   *   Endpoint, either beer or beer/random currently.
   * @param array $parameters
   *   Parameters for making the request.
   * @param int $cacheLifetime
   *   Cache lifetime.
   *
   * @return mixed
   *   Either the results in array or FALSE.
   */
  public function getBeer(string $endpoint, array $parameters = [], int $cacheLifetime = 120): mixed {

    $cid = $endpoint . Json::encode($parameters);
    $cache = $this->cache->get($cid);

    if ($cache) {
      return $cache->data;
    }

    try {
      $response = $this->httpClient->get($endpoint, $parameters);
      if ($response->getStatusCode() === 200) {
        $result = Json::decode($response->getBody());
        $this->cache->set($cid, $result, $this->time->getRequestTime() + $cacheLifetime);
        return $result;
      }
    }
    catch (RequestException $e) {
      $this->logger->warning('Failed to get beers due to "%error".', ['%error' => $e->getMessage()]);
    }

    return FALSE;
  }

  /**
   * Returns an immutable configuration object.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   An immutable config object.
   */
  protected function getConfig(): ImmutableConfig {
    return $this->configFactory->get('biertje.settings');
  }

  /**
   * Returns a string URI.
   *
   * @return string
   *   An URI string.
   */
  protected function getUri(): string {
    $config = $this->getConfig();
    return $config->get('punk_api.base_uri');
  }

  /**
   * Prepare data to be added into a render array.
   *
   * @param array $data
   *   Array from the get request.
   *
   * @return array
   *   Renderable array.
   */
  public function prepareData(array $data): array {
    $fields = [
      'name',
      'image_url',
      'tagline',
      'abv',
    ];

    $return = [];

    foreach ($fields as $field) {
      if (!empty($data[$field])) {
        $return['#' . $field] = $data[$field];
      }
    }

    return $return;
  }

}
