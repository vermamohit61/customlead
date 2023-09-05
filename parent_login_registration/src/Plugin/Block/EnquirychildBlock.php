<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Provides a 'EnquirychildBlock' block.
 *
 * @Block(
 *  id = "enquirychild_block",
 *  admin_label = @Translation("Enquirychild block"),
 * )
 */
class EnquirychildBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $userid = \Drupal::currentUser()->id();
    $cdata = $this->getmultplechild($userid);
    $str_data = array(
      'cdata' => $cdata,
    );
    return array(
      '#theme' => 'enquirychild_block_template',
      '#data' => $str_data
    );
  }

  // get child from pragraph
  function getmultplechild($userid) {
    $user = User::load($userid);
    $prachild = $user->field_pchild_name->getValue();
    $field_child_name = array();
    foreach ($prachild as $key => $element ) {
      $educationdetails = Paragraph::load($element['target_id']);
      $field_child_name[$element['target_id']] = isset($educationdetails->field_child_name->getValue()[0]['value'])?$educationdetails->field_child_name->getValue()[0]['value']:NULL;
    }
    return $field_child_name;
  }

}
