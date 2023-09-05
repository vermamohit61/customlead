<?php
/**
 * @file
 * Contains Drupal\xai\Form\SettingsForm.
 */
namespace Drupal\school_management\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * Class SettingsForm.
 *
 * @package Drupal\xai\Form
 */
class SettingsForm extends ConfigFormBase{
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(){
    return [
      'school_management.settings',
    ];
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'settings_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    $config = $this->config('school_management.settings');
    $form['state'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Please Select Display State'),
        '#default_value' => $config->get('state'),
      );
      $form['city'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Please Select Display City'),
        '#default_value' => $config->get('city'),
      );
      $form['board_affiliation'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Please Select Display Board Affiliation'),
        '#default_value' => $config->get('board_affiliation'),
      );
      $form['pincode'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Please Select Display Pincode'),
        '#default_value' => $config->get('pincode'),
      );
      $form['classes'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Please Select Display Classes'),
        '#default_value' => $config->get('classes'),
      );
      $form['infrastructure'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Please Select Display Infrastructure'),
        '#default_value' => $config->get('infrastructure'),
      );
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
    $this->config('school_management.settings')
      ->set('state', $form_state->getValue('state'))
      ->set('city', $form_state->getValue('city'))
      ->set('board_affiliation', $form_state->getValue('board_affiliation'))
      ->set('pincode', $form_state->getValue('pincode'))
      ->set('classes', $form_state->getValue('classes'))
      ->set('infrastructure', $form_state->getValue('infrastructure'))
      ->save();
      drupal_flush_all_caches();
  }
}
