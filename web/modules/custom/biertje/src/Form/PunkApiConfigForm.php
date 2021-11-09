<?php

namespace Drupal\biertje\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PunkApiConfigForm.
 */
class PunkApiConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'biertje.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'punk_api_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('biertje.settings');
    $form['punk_api_base_uri'] = [
      '#type' => 'url',
      '#title' => $this->t('Punk API endpoint'),
      '#description' => $this->t('Punk API root endpoint URL'),
      '#default_value' => $config->get('punk_api.base_uri'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('biertje.settings')
      ->set('punk_api.base_uri', $form_state->getValue('punk_api_base_uri'))
      ->save();
  }

}
