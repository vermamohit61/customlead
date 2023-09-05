<?php

/**
 * @file
 * Contains \Drupal\parent_login_registration\Controller\ParentloginController.
 */

namespace Drupal\parent_login_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ParentloginController extends ControllerBase {

  // callback for RegisterformCallback().
  function LoginformCallback(){
    $userid = \Drupal::currentUser()->id();
    if(\Drupal::currentUser()->isAuthenticated()){
      $path = '/user/'.$userid.'/edit';
      $response = new RedirectResponse($path);
      $response->send();
    }
    $queryurl = isset($_GET['dest'])?$_GET['dest']:'';
    $form = \Drupal::formBuilder()->getForm('Drupal\parent_login_registration\Form\ParentloginForm');
    $form_array_data = array(
      'parent_login_form' => $form,
      'queryurl' => $queryurl
    );
    return array(
      '#theme' => 'login_page_template',
      '#data' => $form_array_data
    );
  }


  // callback for LoginsaveCallback().
  public function LoginsaveCallback() {
    global $base_url;
    $expert_data = $_POST;
    $tab_build['expert_lead_status'] = 'error';
    $checkmo = check_mobile_no($expert_data['plmobile']);
    $st = TRUE;
    if(empty($checkmo)) {
      $tab_build['expert_lead_status'] = 'error';
      $st = FALSE;
    }
      if ($st && !empty($expert_data['plmobile'])) {
        $send_out = send_otp_curl($expert_data['plmobile']);
        if ($send_out['type'] = 'success') {
          $tab_build['expert_lead_status'] = 'otp_require';
          \Drupal::logger('lead_sms_status')->notice('OTP sms sent to mobile ' . $expert_data['plmobile'].' with request_id '.$send_out['request_id']);
        }
      }
    return new JsonResponse($tab_build);
    exit;
  }

  // callback for loginotp().
  public function LoginsavesendCallback() {
    global $base_url;
    $expert_data = $_POST;
    $tab_build['base_url'] = $base_url;
    $tab_build['expert_lead_status'] = 'error';
    if (!empty($expert_data['plmobile']) && !empty($expert_data['plotp'])) {
      $verify_out = verifyotp($expert_data['plmobile'], $expert_data['plotp']);
      if ($verify_out['type'] == 'success' && $verify_out['message'] == 'OTP verified success') {
        $uid = _check_user_id_with_phone($expert_data['plmobile']);
        if (!empty($uid)) {
          $user = User::load($uid);
          $pname = ($user->field_parent_name->value)?$user->field_parent_name->value:'';
          $pmobile = ($user->field_mobile_no->value)?$user->field_mobile_no->value:'';
          $terms = ($user->field_user_whatsup_communication->value)?$user->field_user_whatsup_communication->value:0;
          $prachild = $user->field_pchild_name->getValue();
          $field_child_name = [];

          if (isset($_SESSION['schoolid']) && $_SESSION['schoolid']) {
          $neculasid = getneculas_id($_SESSION['schoolid']);
          $neculasids = implode(',', $neculasid);
          $school = explode(',', $neculasid);
          } else {
           $neculasid = [];
           $neculasids = '';
           $school = [];
          }
          $sgrade = [];
          foreach ($prachild as $key => $element ) {
            $educationdetails = \Drupal\paragraphs\Entity\Paragraph::load($element['target_id']);
            $field_child_name = $educationdetails->field_child_name->getValue()[0]['value'];
            $field_grade = $educationdetails->field_pgrade->getValue()[0]['value'];
            $sgrade[] = getgrades($educationdetails->field_pgrade->getValue()[0]['value']);
            $leadid = $pmobile.$field_grade;
            leadacquire($pname, $pmobile, $field_child_name, $field_grade, $terms, 'enquirylogindata', $leadid, $neculasids, $uid);
          }
          //$lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $neculasid, 'ArrE2' => $neculasid, 'ArrE3' => $sgrade);
          //lead_api($lead_request);

          for ($index = 0; $index < sizeof($neculasid); $index++) {
            $nuc_id = [$neculasid[$index]];
            $lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $nuc_id, 'ArrE2' => $nuc_id, 'ArrE3' => $sgrade);
            lead_api($lead_request);
          }


          user_login_finalize($user);
          if (isset($_SESSION['schoolid']) && $_SESSION['schoolid']) {
            //unset($_SESSION['schoolid']);
          }
          //moengage tracking
          $field  = array(
            'unique_user_id'   => $expert_data['plmobile'],
            'first_name' => '',
            'username' => '',
            'last_name' => '',
            'email' => '',
            'track_type' => 'User-login',
            'mobile'=> $expert_data['plmobile']
          );
          moengage_track_create1($field);
        }
        $tab_build['expert_lead_status'] = 'success';
      } else if ($verify_out['type'] == 'error' && $verify_out['message'] == 'OTP not match')  {
        $tab_build['expert_lead_status'] = 'otp_invalid';
      }
      else {
        $tab_build['expert_lead_status'] = 'error';
      }
    }
    return new JsonResponse($tab_build);
    exit;
  }

    // callback for ResendOtp().
    public function ResendOtp() {
      global $base_url;
      $otp_data = $_POST;
      $tab_build['resend_otp_status'] = 'error';
      if (!empty($otp_data['plmobile'])) {
        $verify_out = resendotp($otp_data['plmobile']);
        if ($verify_out['type'] == 'success' && $verify_out['message'] == 'retry send successfully') {
          $tab_build['resend_otp_status'] = 'success';
          \Drupal::logger('lead_sms_status')->notice('Resend OTP sms sent to mobile ' . $otp_data['plmobile']);
        } else {
          $tab_build['resend_otp_status'] = 'successnot';
          \Drupal::logger('lead_sms_status')->notice('unable to Resend OTP via sms to ' . $otp_data['plmobile']);
        }
      }
      return new JsonResponse($tab_build);
      exit;
    }

    // callback for Loginotp().
  public function Loginotp() {
    global $base_url;
    $expert_data = $_POST;
    $tab_build['otp_verify_status'] = 'error';
    $checkmo = check_mobile_no($expert_data['plmobile']);
    $st = TRUE;
    if(empty($checkmo)) {
      $tab_build['otp_verify_status'] = 'error';
      $st = FALSE;
    }
    if ($st && !empty($expert_data['plmobile'])) {
      $send_out = send_otp_curl($expert_data['plmobile']);
      if ($send_out['type'] = 'success') {
        // Generate Lead
        generate_lead_befor_login($expert_data['plmobile']);
        $tab_build['otp_verify_status'] = 'otp_require';
        \Drupal::logger('lead_sms_status')->notice('OTP sms sent to mobile ' . $expert_data['plmobile'].' with request_id '.$send_out['request_id']);
      }
    }
    return new JsonResponse($tab_build);
    exit;
  }

}
