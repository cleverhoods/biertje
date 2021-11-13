<?php

namespace Drupal\biertje\Plugin\Block;

use Drupal\biertje\Services\GetBeerService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'BeerOfTheDayBlock' block.
 *
 * @Block(
 *  id = "beer_of_the_day_block",
 *  admin_label = @Translation("Beer of the day"),
 * )
 */
class BeerOfTheDayBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\biertje\GetRandomBeerServiceInterface definition.
   *
   * @var \Drupal\biertje\Services\GetBeerService
   */
  protected GetBeerService $getBeerService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): BeerOfTheDayBlock|ContainerFactoryPluginInterface|static {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->getBeerService = $container->get('biertje.get_beer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [
      '#theme' => 'beer_of_the_day_block',
    ];

    $beer = $this->getBeerService->getBeer('beers/random', [], 86400);
    $data = $this->getBeerService->prepareData($beer[0]);

    return array_merge($build, $data);
  }

}
