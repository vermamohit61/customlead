<?php
/**
 * @file
 * Contains Drupal\common_leadschool\Form\BannerSettingsForm.
 */
namespace Drupal\common_leadschool\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Class SettingsForm.
 *
 * @package Drupal\common_leadschool\Form
 */
class BannerSettingsForm extends ConfigFormBase{
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(){
    return [
      'common_leadschool.settings',
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'banner_settings_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    $config = $this->config('common_leadschool.settings');
    $form['bnr_text'] = [
      '#type' => 'text_format',
      '#title' => t('Banner Text'),
      '#rows' => 4,
      '#cols' => 5,
      '#required' => true,
      '#default_value' => $config->get('bnr_text.value'),
      '#format' => $config->get('bnr_text.format'),
    ];
    return parent::buildForm($form, $form_state);
  }
 /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state){
    parent::validateForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    parent::submitForm($form, $form_state);
    $this->config('common_leadschool.settings')
      ->set('bnr_text', $form_state->getValue('bnr_text'))
      ->save();
      drupal_flush_all_caches();
  }
}
