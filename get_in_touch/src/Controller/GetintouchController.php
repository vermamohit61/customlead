<?php

/**
 * @file
 * Contains \Drupal\get_in_touch\Controller\GetintouchController.
 */

namespace Drupal\get_in_touch\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\node\Entity\Node;

class GetintouchController extends ControllerBase {

    // callback for ExpertSaveCallback().
    public function ExpertSaveCallback() {
    global $base_url;
    $expert_data = $_POST;
    $tab_build['expert_lead_status'] = 'error';
    if (!empty($expert_data)) {
          $node = Node::create(['type' => 'get_in_touch']);
          $node->set('title', $expert_data['exname']);
          $node->set('field_last_name', $expert_data['exlname']);
          $node->set('field_email_id', $expert_data['exmail']);
          $node->set('field_mobile_no', $expert_data['exmobile']);
          $node->set('field_message', $expert_data['exmessage']);
          $node->set('field_whatsapp_communication', $expert_data['terms']);
          $node->set('uid', 1);
          $node->langcode = 'en';
          $node->status = 1;
          $node->enforceIsNew();
          $node->created = REQUEST_TIME;
          $node->changed = REQUEST_TIME;
          $node->save();
          $lead_request = array('pname' => $expert_data['exname'], 'pmobile' => $expert_data['exmobile'], 'Email' => $expert_data['exmail'], 'ArrE1' => array(), 'ArrE2' => array(), 'ArrE3' => array());
          lead_api($lead_request);
          $tab_build['expert_lead_status'] = 'success';
        //moengage tracking
      $field  = array(
        'unique_user_id'   => $expert_data['exmobile'],
        'first_name' => $expert_data['exname'],
        'username' => '',
        'last_name' => $expert_data['exlname'],
        'email' => $expert_data['exmail'],
        'track_type' => 'Subscribe',
        'mobile'=> $expert_data['exmobile']
      );
      moengage_track_create($field);

    }
    return new JsonResponse($tab_build);
    exit;
  }

}
