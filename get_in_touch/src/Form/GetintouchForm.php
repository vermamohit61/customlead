<?php

namespace Drupal\get_in_touch\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class GetintouchForm.
 */
class GetintouchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'getintouch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="expert_result_message"></div>'
    ];

    $form['exname'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 64,
      '#attributes' => ['placeholder' => 'First Name*', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert first-name">',
      '#suffix' => '<div class="exname_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $form['exlname'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 64,
      '#attributes' => ['placeholder' => 'Last Name', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert last-name">',
      '#suffix' => '<div class="exlname_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $form['exmail'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#attributes' => ['placeholder' => 'Email Id', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert emailid">',
      '#suffix' => '<div class="exmail_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $form['exmobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 10,
      '#attributes' => ['placeholder' => 'Mobile Number*', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert mobileno">',
      '#suffix' => '<div class="exmobile_error_msg error-text"></div></div>',
      '#default_value' => '',
    ];
    $form['exmessage'] = [
      '#type' => 'textarea',
      '#title' => $this->t(''),
      '#attributes' => ['placeholder' => 'Type your message here...', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert message">',
      '#suffix' => '<div class="exmessage_error_msg error-text"></div></div>',
    ];
    $prefix_html = '<div class="talktoexpert-msg">I agree to receive communications by Whatsapp</div>';
    $form['exptermscondition'] = [
      '#type' => 'checkbox',
      '#title' => $prefix_html,
      '#prefix' => '<div class="form-group-expert terms-block">',
      '#suffix' => '<div class="exptermscondition_error_msg error-text"></div></div>',
      '#attributes' => array('checked' => 'checked')
    ];

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
    $subscribe_event = SUBSCRIBER_EVENT;
    $form['subscribe_event'] = [
      '#type' => 'hidden',
      '#value' => $subscribe_event,
      '#default_value' => $subscribe_event,
    ];
    $moengage_debug = MOENGAGE_DEBUG;
    $form['moengage_debug'] = [
      '#type' => 'hidden',
      '#value' => $moengage_debug,
      '#default_value' => $moengage_debug,
    ];

    $form['markup'] = [
      '#markup' => '<div class="sub-clbck"><a class="expert-subinfo">Submit Query</a></div>',
    ];
    $form['#attached']['library'][] = 'get_in_touch/getintouchblockjs';
    $form['#attached']['library'][] = 'get_in_touch/googleanalyticstrackjs';
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
