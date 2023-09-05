<?php

namespace Drupal\parent_login_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Class GetintouchForm.
 */
class ParentregisterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'parentregister_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="register_result_message"></div>'
    ];
    $form['pname'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 64,
      '#attributes' => ['placeholder' => 'Parent Name', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert pname">',
      '#suffix' => '<div class="pname_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $classmob = 'signup-otp';
    $otp_options = ['absolute' => TRUE, 'attributes' => ['class' => $classmob]];
    $otp_object = Link::fromTextAndUrl(t(''), Url::fromRoute('<none>', [], $otp_options))->toString();

    $form['cname'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 64,
      '#attributes' => ['placeholder' => 'Child Name', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert cname">',
      '#suffix' => '<div class="cname_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $entityManager = \Drupal::service('entity_field.manager');
    $fields = $entityManager->getFieldStorageDefinitions('user', 'field_grade_applying_for');
    $options = options_allowed_values($fields['field_grade_applying_for']);
    $selar = array('' => 'Grade Applying For');
    $meroption = array_merge($selar,$options);
    $form['gradeapplying'] = [
      '#type' => 'select',
      '#title' => $this->t(''),
      '#options' => $meroption,
      '#prefix' => '<div class="form-group-expert grade">',
      '#suffix' => '<div class="grade_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];

    $form['pmobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 10,
      '#attributes' => ['placeholder' => 'Mobile number', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert pmobile">',
      '#suffix' => '<div class="pmobile_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $form['potp'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 4,
      '#attributes' => ['placeholder' => 'OTP', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert remove-bottom-margin-mb mob-wrap">',
      '#suffix' => '<div class="potp_error_msg error-text"></div><div class="potp_success_msg success-text"></div><span class="otp-link">'.$otp_object.'</span><span class="otp-msg"><span class="error-text"></span><span class="success-text"></span></span></div>',
      '#description' => $this->t(''),
      '#default_value' => '',
    ];
    $prefix_html = '<div class="talktoexpert-msg">I agree to receive communications by Whatsapp</div>';
    $form['exptermscondition'] = [
      '#type' => 'checkbox',
      '#title' => $prefix_html,
      '#prefix' => '<div class="form-group-expert terms-block">',
      '#suffix' => '<div class="exptermscondition_error_msg error-text"></div></div>',
      '#attributes' => array('checked' => 'checked')
    ];
    $form['bbr_field'] = array(      
      '#type' => 'hidden',      
      '#default_value' => 'no',
      '#attributes' => array('id' => 'bbr'),     
    );
    $utm_source = isset($_GET['utm_source'])?$_GET['utm_source']:'';
    $utm_medium = isset($_GET['utm_medium'])?$_GET['utm_medium']:'';
    $utm_campaign = isset($_GET['utm_campaign'])?$_GET['utm_campaign']:'';
    $utm_term = isset($_GET['utm_term'])?$_GET['utm_term']:'';
    $utm_content = isset($_GET['utm_content'])?$_GET['utm_content']:'';
    $gclid = isset($_GET['gclid'])?$_GET['gclid']:'';
    $fbclid = isset($_GET['fbclid'])?$_GET['fbclid']:'';
    $form['utm_source'] = [
      '#type' => 'hidden',
      '#value' => $utm_source
    ];
    $form['utm_medium'] = [
      '#type' => 'hidden',
      '#value' => $utm_medium
    ];
    $form['utm_campaign'] = [
      '#type' => 'hidden',
      '#value' => $utm_campaign
    ];
    $form['utm_term'] = [
      '#type' => 'hidden',
      '#value' => $utm_term
    ];
    $form['utm_content'] = [
      '#type' => 'hidden',
      '#value' => $utm_content
    ];
    $form['gclid'] = [
      '#type' => 'hidden',
      '#value' => $gclid
    ];
    $form['fbclid'] = [
      '#type' => 'hidden',
      '#value' => $fbclid
    ];
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
    $psignUp = P_SIGN_UP;
    $form['prsignUp'] = [
      '#type' => 'hidden',
      '#value' => $psignUp,
      '#default_value' => $psignUp,
    ];
    $form['markup'] = [
      '#markup' => '<div class="sub-clbck"><a class="registration-subinfo">Submit</a></div>',
    ];
    $form['#attached']['library'][] = 'parent_login_registration/parentloginjs';
    $form['#attached']['library'][] = 'parent_login_registration/parentregsloginjs';
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
