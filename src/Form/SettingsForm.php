<?php

namespace Drupal\fontawesome\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures fontawesome settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'fontawesome_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'fontawesome.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get current settings.
    $fontawesome_config = $this->config('fontawesome.settings');

    $form['fontawesome_use_cdn'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use CDN version of Font Awesome?'),
      '#description' => $this->t('Checking this box will cause the Font Awesome library to be loaded externally rather than from the local library file.'),
      '#default_value' => $fontawesome_config->get('fontawesome_use_cdn'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Save the updated settings.
    $this->config('fontawesome.settings')
      ->set('fontawesome_use_cdn', $values['fontawesome_use_cdn'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
