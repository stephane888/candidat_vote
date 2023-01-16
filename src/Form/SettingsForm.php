<?php

namespace Drupal\candidat_vote\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure candidat vote settings for this site.
 */
class SettingsForm extends ConfigFormBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'candidat_vote_settings';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'candidat_vote.settings'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $styles_images = [];
    foreach (\Drupal::entityTypeManager()->getStorage('image_style')->loadMultiple() as $style_image) {
      /**
       *
       * @var \Drupal\image\Entity\ImageStyle $style_image
       */
      $styles_images[$style_image->id()] = $style_image->label();
    }
    $form['style_image'] = [
      '#type' => 'select',
      '#title' => $this->t('Style d\'image'),
      '#options' => $styles_images,
      '#default_value' => $this->config('candidat_vote.settings')->get('style_image')
    ];
    return parent::buildForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // if ($form_state->getValue('example') != 'example') {
    // $form_state->setErrorByName('example', $this->t('The value is not
    // correct.'));
    // }
    parent::validateForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('candidat_vote.settings')->set('style_image', $form_state->getValue('style_image'))->save();
    parent::submitForm($form, $form_state);
  }
  
}
