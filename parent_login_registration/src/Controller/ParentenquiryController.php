<?php

/**
* @file
* Contains \Drupal\parent_login_registration\Controller\ParentenquiryController.
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
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\path_alias\Entity\PathAlias;

class ParentenquiryController extends ControllerBase {
  // callback for EnquiryCallback().
  // controller for enquiry form
  function EnquiryCallback(){
    global $base_url;
    $expert_data = $_POST;
    //$expert_data['pmobile'] = 8384981812;
    //$expert_data['userid'] = 42;
    //$expert_data['schoolid'] = 76;
    //$expert_data['cname'] = '317,670';

    $tab_build['expert_lead_status'] = 'error';
    $checkmo = check_mobile_no($expert_data['pmobile']);
    if (!empty($expert_data)) {
      $utm_source = isset($expert_data['utm_source'])?$expert_data['utm_source']:'';
      $utm_medium = isset($expert_data['utm_medium'])?$expert_data['utm_medium']:'';
      $utm_campaign = isset($expert_data['utm_campaign'])?$expert_data['utm_campaign']:'';
      $utm_term = isset($expert_data['utm_term'])?$expert_data['utm_term']:'';
      $utm_content = isset($expert_data['utm_content'])?$expert_data['utm_content']:'';
      $gclid = isset($expert_data['gclid'])?$expert_data['gclid']:'';
      $fbclid = isset($expert_data['fbclid'])?$expert_data['fbclid']:'';
      if (!empty($expert_data['userid'])){
        $user = User::load($expert_data['userid']);
        $pname = ($user->field_parent_name->value)?$user->field_parent_name->value:'';
        $pmobile = ($user->field_mobile_no->value)?$user->field_mobile_no->value:'';
        $terms = ($user->field_user_whatsup_communication->value)?$user->field_user_whatsup_communication->value:0;
        $neculasid = getneculas_id($expert_data['schoolid']);
        //$neculasid = [1130775,1130776];
        $neculasids = implode(',', $neculasid);
        $prachild = explode(',', $expert_data['cname']);
        $sgrade = [];
        foreach ($prachild as $key => $element ) {
          $educationdetails = \Drupal\paragraphs\Entity\Paragraph::load($element);
          $field_child_name = $educationdetails->field_child_name->getValue()[0]['value'];
          $field_grade = $educationdetails->field_pgrade->getValue()[0]['value'];
          $sgrade[] = getgrades($educationdetails->field_pgrade->getValue()[0]['value']);
          $leadid = $pmobile.$field_grade;
          // insert data in database table
          leadacquire($pname, $pmobile, $field_child_name, $field_grade, $terms, 'enquirydata', $leadid, $neculasids, $expert_data['userid']);
        }
        //$lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $neculasid, 'ArrE2' => $neculasid, 'ArrE3' => $sgrade, 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_campaign' => $utm_campaign, 'utm_content' => $utm_content, 'gclid' => $gclid, 'fbclid' => $fbclid);
          // insert data in crm
        for($index = 0 ; $index < sizeof($neculasid) ; $index++ ){
          $nuc_id = [$neculasid[$index]];
          $lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $nuc_id, 'ArrE2' => $nuc_id, 'ArrE3' => $sgrade, 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_campaign' => $utm_campaign, 'utm_content' => $utm_content, 'gclid' => $gclid, 'fbclid' => $fbclid);
          lead_api($lead_request);
        }
        $tab_build['expert_lead_status'] = 'success';
      } else {
        $st = TRUE;
        if(!empty($checkmo)) {
          $tab_build['expert_lead_status'] = 'error';
          $st = FALSE;
        }
        if ($st == TRUE) {
          $verify_out = verifyotp($expert_data['pmobile'], $expert_data['potp']);
        if ($verify_out['type'] == 'success' && $verify_out['message'] == 'OTP verified success') {
          $neculasid = getneculas_id($expert_data['schoolid']);
          $neculasids = implode(',', $neculasid);
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
          user_login_finalize($user);
          $leadid = $expert_data['pmobile'].$expert_data['grade'];
          leadacquire($expert_data['pname'], $expert_data['pmobile'], $expert_data['cname'], $expert_data['grade'], $expert_data['terms'], 'enquirydata', $leadid, $neculasids, $newid);
          //$lead_request = array('pname' => $expert_data['pname'], 'pmobile' => $expert_data['pmobile'], 'ArrE1' => $neculasid, 'ArrE2' => $neculasid, 'ArrE3' => array(getgrades($expert_data['grade'])), 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_campaign' => $utm_campaign, 'utm_content' => $utm_content, 'gclid' => $gclid, 'fbclid' => $fbclid);
          //lead_api($lead_request);
          for ($index = 0; $index < sizeof($neculasid); $index++) {
            $nuc_id = [$neculasid[$index]];
            $lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $nuc_id, 'ArrE2' => $nuc_id, 'ArrE3' => $sgrade, 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_campaign' => $utm_campaign, 'utm_content' => $utm_content, 'gclid' => $gclid, 'fbclid' => $fbclid);
            lead_api($lead_request);
          }

          $path_alias = PathAlias::create([
            'path' => '/user/' . $newid . '/edit',
            'alias' => '/'.$generatedusername,
          ]);
          $path_alias->save();
          $tab_build['expert_lead_status'] = 'success';
        } else if ($verify_out['type'] == 'error' && $verify_out['message'] == 'OTP not match')  {
          $tab_build['expert_lead_status'] = 'otp_invalid';
        }
        }
      }

    }
    return new JsonResponse($tab_build);
    exit;
  }

  // callback for SinglechildenquiryCallback().
  // controller for enquiry form
  function SinglechildenquiryCallback(){
    global $base_url;
    $expert_data = $_POST;
    $tab_build['expert_lead_status'] = 'error';
    if (!empty($expert_data)) {
      $userid = \Drupal::currentUser()->id();
      $user = User::load($userid);
      $pname = ($user->field_parent_name->value)?$user->field_parent_name->value:'';
      $pmobile = ($user->field_mobile_no->value)?$user->field_mobile_no->value:'';
      $terms = ($user->field_user_whatsup_communication->value)?$user->field_user_whatsup_communication->value:0;
      $school_names = getSchoolName($expert_data['schoolid']);
      $neculasid = getneculas_id($expert_data['schoolid']);
      $neculasids = implode(',', $neculasid);
      $prachild = $user->field_pchild_name->getValue();
      $sgrade = [];
      foreach ($prachild as $key => $element ) {
        $educationdetails = \Drupal\paragraphs\Entity\Paragraph::load($element['target_id']);
        $field_child_name = $educationdetails->field_child_name->getValue()[0]['value'];
        $field_grade = $educationdetails->field_pgrade->getValue()[0]['value'];
        $sgrade[] = getgrades($educationdetails->field_pgrade->getValue()[0]['value']);
        $leadid = $pmobile.$field_grade;
        leadacquire($pname, $pmobile, $field_child_name, $field_grade, $terms, 'enquirydata', $leadid, $neculasids, $userid);
      }
      //$lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $neculasid, 'ArrE2' => $neculasid, 'ArrE3' => $sgrade);
          // insert data in crm
      //lead_api($lead_request);
      for ($index = 0; $index < sizeof($neculasid); $index++) {
        $nuc_id = [$neculasid[$index]];
        $lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $nuc_id, 'ArrE2' => $nuc_id, 'ArrE3' => $sgrade, 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_campaign' => $utm_campaign, 'utm_content' => $utm_content, 'gclid' => $gclid, 'fbclid' => $fbclid);
        lead_api($lead_request);
      }

      $tab_build['expert_lead_status'] = 'success';
      $tab_build['school_names'] = $school_names;

    }
    return new JsonResponse($tab_build);
    exit;
  }

  // callback for MultplechildenquiryCallback().
  // controller for enquiry form
  function MultplechildenquiryCallback(){
    global $base_url;
    $expert_data = $_POST;
    $tab_build['expert_lead_status'] = 'error';
    if (!empty($expert_data)) {
      $userid = \Drupal::currentUser()->id();
      $user = User::load($userid);
      $pname = ($user->field_parent_name->value)?$user->field_parent_name->value:'';
      $pmobile = ($user->field_mobile_no->value)?$user->field_mobile_no->value:'';
      $terms = ($user->field_user_whatsup_communication->value)?$user->field_user_whatsup_communication->value:0;
      $school_names = getSchoolName($expert_data['schoolid']);
      $neculasid = getneculas_id($expert_data['schoolid']);
      $neculasids = implode(',', $neculasid);
      $prachild = explode(',', $expert_data['childid']);
      $sgrade = [];
      foreach ($prachild as $key => $element ) {
        $educationdetails = \Drupal\paragraphs\Entity\Paragraph::load($element);
        $field_child_name = $educationdetails->field_child_name->getValue()[0]['value'];
        $field_grade = $educationdetails->field_pgrade->getValue()[0]['value'];
        $sgrade[] = getgrades($educationdetails->field_pgrade->getValue()[0]['value']);
        $leadid = $pmobile.$field_grade;
        leadacquire($pname, $pmobile, $field_child_name, $field_grade, $terms, 'enquirydata', $leadid, $neculasids, $userid);
      }
      //$lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $neculasid, 'ArrE2' => $neculasid, 'ArrE3' => $sgrade);
          // insert data in crm
      //lead_api($lead_request);
      for ($index = 0; $index < sizeof($neculasid); $index++) {
        $nuc_id = [$neculasid[$index]];
        $lead_request = array('pname' => $pname, 'pmobile' => $pmobile, 'ArrE1' => $nuc_id, 'ArrE2' => $nuc_id, 'ArrE3' => $sgrade, 'utm_source' => $utm_source, 'utm_medium' => $utm_medium, 'utm_campaign' => $utm_campaign, 'utm_content' => $utm_content, 'gclid' => $gclid, 'fbclid' => $fbclid);
        lead_api($lead_request);
      }

      $tab_build['expert_lead_status'] = 'success';
      $tab_build['school_names'] = $school_names;
    }
    return new JsonResponse($tab_build);
    exit;
  }

  //callback SinglenonloginenquiryCallback
  // set school id in session
  function SinglenonloginenquiryCallback(){
    $expert_data = $_POST;
    if(isset($_SESSION['schoolid'])){
      unset($_SESSION['schoolid']);
      $_SESSION['schoolid'] = $expert_data['schoolid'];
      $tab_build['schoolid'] = $_SESSION['schoolid'];
    }
    else{
      $_SESSION['schoolid'] = $expert_data['schoolid'];
      $tab_build['schoolid'] = $_SESSION['schoolid'];
    }
    
    return new JsonResponse($tab_build);
    exit;
  }
}
