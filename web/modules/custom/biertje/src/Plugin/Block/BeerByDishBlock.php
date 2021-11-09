<?php

namespace Drupal\biertje\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'BeerByDishBlock' block.
 *
 * @Block(
 *  id = "beer_by_dish_block",
 *  admin_label = @Translation("Beer by dish"),
 * )
 */
class BeerByDishBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->formBuilder = $container->get('form_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $builtForm = $this->formBuilder->getForm('Drupal\biertje\Form\BeerByDishSearchForm');
    $build['form'] = $builtForm;

    return $build;
  }

}
