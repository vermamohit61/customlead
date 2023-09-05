<?php

namespace Drupal\parent_login_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\user\Entity\User;

/**
 * Class ParentenquiryForm.
 */
class ParentenquiryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'parentenquiry_form';
  }

  /**
   * {@inheritdoc}
   * Enquery form
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    $node_id = $node->id();
    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="register_result_message"></div>'
    ];
    $userid = \Drupal::currentUser()->id();
    $pname = '';
    $pmo = '';
    $pgrade = '';
    $cname = '';
    $read[] = '';
    if(!empty($userid)) {
      $user = User::load($userid);
      $pname = $user->get('field_parent_name')->getValue()?$user->get('field_parent_name')->getValue()[0]['value']:'';
      $pmo = $user->get('field_mobile_no')->getValue()?$user->get('field_mobile_no')->getValue()[0]['value']:'';
      $read['readonly']='readonly';
    }
    $formatt = ['placeholder' => 'Mobile number', 'class' => ['required', 'valid']];
    $form['pname'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 64,
      '#attributes' => ['placeholder' => 'Parent Name', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert pname">',
      '#suffix' => '<div class="pname_error_msg error-text"></div></div>',
      '#default_value' => $pname,
    ];
    
    $form['pmobile'] = [
      '#type' => 'textfield',
      '#title' => $this->t(''),
      '#maxlength' => 10,
      '#attributes' => ['placeholder' => 'Mobile number', 'class' => ['required', 'valid']],
      '#prefix' => '<div class="form-group-expert pmobile">',
      '#suffix' => '<div class="pmobile_error_msg error-text"></div></div>',
      '#default_value' => $pmo,
    ];
    if(empty($userid)) {
      $classmob = 'signup-otp';
    $otp_options = ['absolute' => TRUE, 'attributes' => ['class' => $classmob]];
    $otp_object = Link::fromTextAndUrl(t(''), Url::fromRoute('<none>', [], $otp_options))->toString();
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
  }
    $entityManager = \Drupal::service('entity_field.manager');
    $fields = $entityManager->getFieldStorageDefinitions('user', 'field_grade_applying_for');
    $options = options_allowed_values($fields['field_grade_applying_for']);
    $selar = array('' => 'Grade Applying For');
    $meroption = array_merge($selar,$options);
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $gr = $user->field_grade_applying_for->value;
    if(empty($userid)) {
      $form['gradeapplying'] = [
        '#type' => 'select',
        '#title' => $this->t(''),
        '#options' => $meroption,
        '#prefix' => '<div class="form-group-expert grade">',
        '#suffix' => '<div class="grade_error_msg error-text"></div></div>',
        '#default_value' => $pgrade,
      ];
    }
    if(!empty($userid)) {
      $coption = getmultpleprachild($userid);
      $form['cname'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Select Child'),
        '#options' => $coption,
        '#prefix' => '<div class="form-group-expert cname">',
        '#suffix' => '<div class="cname_error_msg error-text"></div></div>',
        '#attributes' => ['checked'=>['checked'],'class' => ['checkch']],
      ];
      //start for GTM data
      $gtm_option = getmultpleprachildname($userid);
      $form['gtm_cname'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Select Child'),
        '#options' => $gtm_option,
        '#prefix' => '<div class="form-group-expert gtm_cname">',
        '#suffix' => '<div class="gtm_cname_error_msg error-text"></div></div>',
        '#attributes' => ['checked'=>['checked'],'class' => ['checkgtm']],
      ];
      $gtm_grade_option = getmultplepragradename($userid);
      $form['gtm_gradename'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Select Grade'),
        '#options' => $gtm_grade_option,
        '#prefix' => '<div class="form-group-expert gtm_gradename">',
        '#suffix' => '<div class="gtm_gradename_error_msg error-text"></div></div>',
        '#attributes' => ['checked'=>['checked'],'class' => ['checkgtmgrade']],
      ];

      //end for GTM data
    } else {
      $form['cname'] = [
        '#type' => 'textfield',
        '#title' => $this->t(''),
        '#maxlength' => 64,
        '#attributes' => ['placeholder' => 'Child Name', 'class' => ['required', 'valid']],
        '#prefix' => '<div class="form-group-expert cname">',
        '#suffix' => '<div class="cname_error_msg error-text"></div></div>',
        '#default_value' => '',
      ];
    }
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
    $form['nodepath'] = [
      '#type' => 'hidden',
      '#value' => get_node_path_alias_en($node_id)
    ];
    $form['userid'] = [
      '#type' => 'hidden',
      '#value' => $userid,
      '#attributes' => array('id' => 'edit-userid')
    ];
    $form['schoolid'] = [
      '#type' => 'hidden',
      '#value' => $node_id,
      '#attributes' => array('id' => 'edit-schoolid')
    ];
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
    $b2b_lead = B2B_LEAD_SUBMITTED_EVENT;
    $form['b2b_lead'] = [
      '#type' => 'hidden',
      '#value' => $b2b_lead,
      '#default_value' => $b2b_lead,
    ];
    $moengage_debug = MOENGAGE_DEBUG;
    $form['moengage_debug'] = [
      '#type' => 'hidden',
      '#value' => $moengage_debug,
      '#default_value' => $moengage_debug,
    ];
    $form['markup'] = [
      '#markup' => '<div class="sub-clbck"><a class="parentenquiry-subinfo">Submit</a></div>',
    ];
    $form['#attached']['library'][] = 'parent_login_registration/parentregsloginjs';
    $form['#attached']['library'][] = 'parent_login_registration/parentenquiryjs';
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
