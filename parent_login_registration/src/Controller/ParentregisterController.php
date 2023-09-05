<?php

/**
 * @file
 * Contains \Drupal\parent_login_registration\Controller\ParentregisterController.
 */

namespace Drupal\parent_login_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\path_alias\Entity\PathAlias;

class ParentregisterController extends ControllerBase {
    // callback for RegisterCallback().
    public function RegisterCallback() {
    global $base_url;
    $expert_data = $_POST;
    $tab_build['base_url'] = $base_url;
    $tab_build['expert_lead_status'] = 'error';
    $checkmo = check_mobile_no($expert_data['pmobile']);
    if (!empty($expert_data)) {
      $st = TRUE;
      if(!empty($checkmo)) {
        $tab_build['expert_lead_status'] = 'error';
        $st = FALSE;
      }
      if ($st == TRUE) {
        // Set session variables
        $_SESSION["newly_registered"] = "yes";

        $verify_out = verifyotp($expert_data['pmobile'], $expert_data['potp']);
        if ($verify_out['type'] == 'success' && $verify_out['message'] == 'OTP verified success') {
        if (isset($_SESSION['schoolid']) && $_SESSION['schoolid']) {
          $neculasid = getneculas_id($_SESSION['schoolid']);
          $neculasids = implode(',', $neculasid);
          } else {
           $neculasid = [];
           $neculasids = '';
          }
        $paragraph = Paragraph::create([
          'type' => 'child_name',
          'field_child_name' => $expert_data['cname'],
          'field_pgrade' => $expert_data['grade'],
        ]);
        $paragraph->save();
        $generatedusername = 'lsm'.uniqid();
        $pass = password_generate(10);
        $user = User::create();
        $user->setPassword($pass);
        $user->enforceIsNew();
        $user->setEmail($expert_data['pmobile'].'@leadschool.com');
        $user->setUsername($generatedusername);
        $user->set('field_parent_name', $expert_data['pname']);
        $user->set('field_mobile_no', $expert_data['pmobile']);
        $user->set("field_child_name", $expert_data['cname']);
        $user->set("field_grade_applying_for", $expert_data['grade']);
        $user->set("field_user_whatsup_communication", $expert_data['terms']);
        $user->field_pchild_name->appendItem($paragraph);
        $user->addRole('parent');
        //Optional settings
        $user->activate();
        //Save user account
        $user->save();
        $newid = $user->id();
        //$lead_request = array('pname' => $expert_data['pname'], 'pmobile' => $expert_data['pmobile'], 'ArrE1' => $neculasid, 'ArrE2' => $neculasid, 'ArrE3' => array(getgrades($expert_data['grade'])));
        //lead_api($lead_request);
        for ($index = 0; $index < sizeof($neculasid); $index++) {
          $nuc_id = [$neculasid[$index]];
          $lead_request = array('pname' => $expert_data['pname'], 'pmobile' => $expert_data['pmobile'], 'ArrE1' => $nuc_id, 'ArrE2' => $nuc_id, 'ArrE3' => array(getgrades($expert_data['grade'])));
          lead_api($lead_request);
        }

        user_login_finalize($user);
        if (isset($_SESSION['schoolid']) && $_SESSION['schoolid']) {
            unset($_SESSION['schoolid']);
        }
        $leadid = $expert_data['pmobile'].$expert_data['grade'];
        leadacquire($expert_data['pname'], $expert_data['pmobile'], $expert_data['cname'], $expert_data['grade'], $expert_data['terms'], 'registerenquiry', $leadid, $neculasids, $newid);
        $path_alias = PathAlias::create([
          'path' => '/user/' . $newid . '/edit',
          'alias' => '/'.$generatedusername,
        ]);
        $path_alias->save();
        $tab_build['expert_lead_status'] = 'success';
        //moengage tracking
        $unique_user_id = $expert_data['pmobile'].''.$expert_data['grade'];
        $field  = array(
          'unique_user_id'   => $unique_user_id,
          'first_name' => $expert_data['pname'],
          'username' => $generatedusername,
          'last_name' => '',
          'email' => '',
          'track_type' => 'Profile-Creation',
          'mobile'=> $expert_data['pmobile']
        );
        moengage_track_create1($field);
        $query = \Drupal::database();
        $query ->insert('moengage_track')
          ->fields($field)
          ->execute();
        } else if ($verify_out['type'] == 'error' && $verify_out['message'] == 'OTP not match')  {
          $tab_build['expert_lead_status'] = 'otp_invalid';
        }
      }
    }
    return new JsonResponse($tab_build);
    exit;
  }

  // callback for RegisterformCallback().
  function RegisterformCallback(){
    $userid = \Drupal::currentUser()->id();
    if(\Drupal::currentUser()->isAuthenticated()){
      $path = '/user/'.$userid.'/edit';
      $response = new RedirectResponse($path);
      $response->send();
      exit;
    }

    //$queryurl = isset($_GET['dest'])?$_GET['dest']:'';
    $old_url = \Drupal::request()->getRequestUri();
    $new_url = explode('dest',$old_url);
    if(isset($new_url[1])){
      $queryurl = substr($new_url[1],1);
    }
    else{
      $queryurl = '';
    }

    $form = \Drupal::formBuilder()->getForm('Drupal\parent_login_registration\Form\ParentregisterForm');
    $form_array_data = array(
      'registration_form' => $form,
      'queryurl' => $queryurl
    );
    
    return array(
      '#theme' => 'registration_page_template',
      '#data' => $form_array_data,
      '#cache'=>['max-age'=>0]
    );
  }

  // signup otp
  function SignupOtp() {
    global $base_url;
    $expert_data = $_POST;
    $tab_build['otp_verify_status'] = 'error';
    $checkmo = check_mobile_no($expert_data['mobileval']);
    $st = TRUE;
    if(!empty($checkmo)) {
      $tab_build['otp_verify_status'] = 'error';
      $st = FALSE;
    }
    if ($st && !empty($expert_data['mobileval'])) {
      $send_out = send_otp_curl($expert_data['mobileval']);
      if ($send_out['type'] = 'success') {
        $tab_build['otp_verify_status'] = 'success';
        \Drupal::logger('lead_sms_status')->notice('OTP sms sent to mobile ' . $expert_data['plmobile'].' with request_id '.$send_out['request_id']);
      }
    }
    return new JsonResponse($tab_build);
    exit;
  }

  // callback for ResendOtpregister().
  public function ResendOtpregister() {
    global $base_url;
    $otp_data = $_POST;
    $tab_build['resend_otp_status'] = 'error';
    if (!empty($otp_data['mobileval'])) {
      $verify_out = resendotp($otp_data['mobileval']);
      if ($verify_out['type'] == 'success' && $verify_out['message'] == 'retry send successfully') {
        $tab_build['resend_otp_status'] = 'success';
        \Drupal::logger('lead_sms_status')->notice('Resend OTP sms sent to mobile ' . $otp_data['mobileval']);
      } else {
        $tab_build['resend_otp_status'] = 'successnot';
        \Drupal::logger('lead_sms_status')->notice('unable to Resend OTP via sms to ' . $otp_data['mobileval']);
      }
    }
    return new JsonResponse($tab_build);
    exit;
  }

  // redirect register call
  function Rediectregister(){
    $user = \Drupal::currentUser()->id();
    if($_GET['dest'] == 'undefined'){
       $_GET['dest'] = '/find-a-school/schools';
    }
    $message = '<div class="thanks-pop-wrapper"><div class="thanks-inner">
    <a class="close-btn" href="javascript:">&#10006;</a>
    <h3>Thank you for registering.</h3>
    <p>Our counsellor will get in touch with you shortly </p>
    <strong>For registering more kids </strong>
    <a href="/user/'.$user.'/edit?dest='.$_GET['dest'].'" class="click-btn">CLICK HERE</a></div>
    </div>';
    $url = Url::fromUri('internal:/find-a-school/schools');
    if($_GET['dest'] != 'undefined'){
      $url = Url::fromUri('internal:' . $_GET['dest']);
    }
    \Drupal::messenger()->addStatus(['#markup' => $message]);
    $response = new RedirectResponse($url->toString());
    $response->send();
    return $response;
  }

  // redirect login call
  function Loginrediect() {
    $user = \Drupal::currentUser()->id();
    if($_GET['dest'] == 'undefined'){
       $_GET['dest'] = '/find-a-school/schools';
    }
    $message = '<div class="thanks-pop-wrapper">
    <div class="thanks-inner">
           <a class="close-btn" href="javascript:">&#10006;</a>
           <p>Congrats ! Now You can look for schools for all your children</p>
           <strong>For registering more kids</strong>
            <a href="/user/'.$user.'/edit?dest='.$_GET['dest'].'" class="click-btn">CLICK HERE</a>

    </div>
    </div>';
    $url = Url::fromUri('internal:/find-a-school/schools');
    if($_GET['dest'] != 'undefined'){
      $url = Url::fromUri('internal:' . $_GET['dest']);
    }
    \Drupal::messenger()->addStatus(['#markup' => $message]);
    $response = new RedirectResponse($url->toString());
    $response->send();
    return $response;
  }

  function sessionMessageDestroy(){
    if(isset($_SESSION['newly_registered'])){
      unset($_SESSION['newly_registered']);
      $result = ['result'=>'Session message destroyed successfully.'];
      return new JsonResponse($result);
    }
    else{
      $result = ['result'=>'No Values'];
      return new JsonResponse($result);
    }
  }
}
