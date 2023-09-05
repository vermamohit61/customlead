<?php

namespace Drupal\parent_login_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class GetintouchForm.
 */
class ParentloginForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'parentlogin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {    
    $form['plmobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 10,
      '#attributes' => ['placeholder' => 'Mobile No.', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert mob-wrap mobile-login">',
      '#suffix' => '<div class="plmobile_error_msg error-text otp-msg"></div><div class="plotp_success_msgg success-text otp-msg"></div></div>',
      '#description' => $this->t(''),
      '#default_value' => '',
    ];
    $form['plotp'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 4,
      '#attributes' => ['placeholder' => 'OTP', 'class' => ['required', 'valid', '']],
      '#prefix' => '<div class="form-group-expert remove-bottom-margin-mb mob-wrap mobile-otp">',
      '#suffix' => '<div class="plotp_error_msg error-text"></div><div class="plotp_success_msg success-text"></div><div class="plotp-rsend-lnk otp-link"></div></div>',
      '#description' => $this->t(''),
      '#default_value' => '',
    ];
    $form['bbr_field'] = array(      
      '#type' => 'hidden',      
      '#default_value' => 'no',
      '#attributes' => array('id' => 'bbr'),     
    );
    $app_id = APP_ID_KEY;
    $form['appid'] = [
      '#type' => 'hidden',
      '#value' => $app_id,
      '#default_value' => $app_id,
    ];
    $cdnmoengage = CDN_MOENGAGE_COM;
    $form['cdnmoengage'] = [
      '#type' => 'hidden',
      '#value' => $cdnmoengage,
      '#default_value' => $cdnmoengage,
    ];
    $prlogin = P_LOGIN;
    $form['prlogin'] = [
      '#type' => 'hidden',
      '#value' => $prlogin,
      '#default_value' => $prlogin,
    ];
    $form['markup'] = [
      '#markup' => '<div class="sub-clbck"><a id="editpllogin-submit" class="parentloginsend--subinfo">Login</a></div>',
    ];
    $form['#attached']['library'][] = 'parent_login_registration/parentloginjs';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
