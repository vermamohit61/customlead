<?php
/**
 * @file
 * Contains \Drupal\moengage_track\Controller\SchoolsfilterController.
 */
namespace Drupal\moengage_track\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Connection;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;

Class SchoolsfilterController extends ControllerBase {
  // not found filter url stored in moengage track table
  public function schoolsSearchFilterTrack($filter_url) {
   
   $tab_build['expert_lead_status'] = 'error';
  if (!empty($filter_url)) {
    $current_uri = \Drupal::request()->getRequestUri();
    $explode_current_uri = explode("/",$current_uri);
    $user_id = \Drupal::currentUser()->id();
    $moeuser = User::load($user_id);
    $parent_name = ($moeuser->field_parent_name->value)?$moeuser->field_parent_name->value:'';
    $puser_email = ($moeuser->getEmail())?$moeuser->getEmail():'';
    $parent_mobile = ($moeuser->field_mobile_no->value)?$moeuser->field_mobile_no->value:'';
    $uid = ($user_id)?$user_id:0;    
    $field  = array(
      'unique_user_id'   => $uid,
      'first_name' => $parent_name,
      'username' => $puser_email,
      'last_name' => '',
      'email' => $puser_email,
      'track_type' => 'no-result-found-filter-url',
      'filter_url' => $explode_current_uri[2],
      'mobile'=> $parent_mobile,
      'publish_date' => date('Y-m-d h:m:s'),
      'result_found' => 1,
      'uid' => $uid
    );
    $query = \Drupal::database();
    $query ->insert('moengage_track')->fields($field)->execute();
     
    if (isset($query)) {
      $tab_build['expert_lead_status'] = 'success';
    } else {
      $tab_build['expert_lead_status'] = 'error';
    }
  }
  return new JsonResponse($tab_build);
    exit;
 }
}