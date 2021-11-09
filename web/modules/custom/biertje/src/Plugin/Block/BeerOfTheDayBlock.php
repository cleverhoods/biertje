<?php

namespace Drupal\biertje\Plugin\Block;

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
  protected $getBeerService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->getBeerService = $container->get('biertje.get_beer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'beer_of_the_day_block',
    ];

    $beer = $this->getBeerService->getBeer('beers/random', [], 86400);
    $data = $this->getBeerService->prepareData($beer[0]);
    $build = array_merge($build, $data);

    return $build;
  }

}
