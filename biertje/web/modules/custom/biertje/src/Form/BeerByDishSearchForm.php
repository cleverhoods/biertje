<?php

namespace Drupal\biertje\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BeerByDishSearchForm.
 */
class BeerByDishSearchForm extends FormBase {

  /**
   * Drupal\biertje\GetRandomBeerServiceInterface definition.
   *
   * @var \Drupal\biertje\Services\GetBeerService
   */
  protected $getBeerService;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->getBeerService = $container->get('biertje.get_beer');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'beer_by_dish_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['dish'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Dish'),
    ];

    $form['submit'] = [
      '#type' => 'button',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => '::getBeerByDishCallback',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Searching for beers'),
        ],
      ],
    ];

    $form['beers'] = [
      '#type' => 'markup',
      '#markup' => '<div id="beers-by-dish"></div>',
    ];

    $form['#attached']['library'][] = 'biertje/biertje';

    return $form;
  }

  /**
   * Ajax callback.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Return the rendered results.
   *
   * @throws \Exception
   */
  public function getBeerByDishCallback(array $form, FormStateInterface $form_state) {
    $output = '<div id="beers-by-dish">' . $this->getBeerByDish($form_state->getValue('dish')) . '</div>';

    $response = new AjaxResponse();
    $response->addCommand(
      new ReplaceCommand(
        '#beers-by-dish',
        $output),
      );

    return $response;
  }

  /**
   * Fetch beer by the provided dish.
   *
   * @param string $dish
   *   The dish provided by the user.
   *
   * @return \Drupal\Component\Render\MarkupInterface|\Drupal\Core\StringTranslation\TranslatableMarkup
   *   Rendered markup.
   *
   * @throws \Exception
   */
  private function getBeerByDish(string $dish) {

    $params = [
      'query' => [
        'food' => str_replace(" ", "_", $dish),
        'per_page' => 3,
      ],
    ];

    $beers = $this->getBeerService->getBeer('beers', $params);

    if (empty($beers)) {
      return $this->t("Couldn't find beer for @dish", ['@dish' => $dish]);
    }

    $build = [
      '#theme' => 'beer_by_dish_block',
      '#beers' => [],
    ];

    foreach ($beers as $item) {
      $beer = [
        '#theme' => 'beer_of_the_day_block',
      ];

      $data = $this->getBeerService->prepareData($item);
      $beer = array_merge($beer, $data);

      $build['#beers'][] = $beer;
    }

    return $this->renderer->render($build);

  }

  /**
   * Submit stub.
   *
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Added stub.
  }

}
