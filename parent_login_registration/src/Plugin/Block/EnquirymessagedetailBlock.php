<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'EnquirymessagedetailBlock' block.
 *
 * @Block(
 *  id = "enquirymessagedetail_block",
 *  admin_label = @Translation("Enquirymessagedetail block"),
 * )
 */
class EnquirymessagedetailBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = \Drupal::currentUser()->id();
    $current_path = \Drupal::service('path.current')->getPath();
    $str_data = array(
      'uid' => $user,
      'path' => $current_path,
    );
    return array(
      '#theme' => 'enquirymessagedetail_block_template',
      '#data' => $str_data
    );

  }

}