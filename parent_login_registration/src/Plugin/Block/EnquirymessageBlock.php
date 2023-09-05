<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'EnquirymessageBlock' block.
 *
 * @Block(
 *  id = "enquirymessage_block",
 *  admin_label = @Translation("Enquirymessage block"),
 * )
 */
class EnquirymessageBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_path = \Drupal::service('path.current')->getPath();
    $user = \Drupal::currentUser()->id();
    $str_data = array(
      'uid' => $user,
      'path' => $current_path,
    );
   
    return array(
      '#theme' => 'enquirymessage_block_template',
      '#data' => $str_data
    );
  }
  
 

}
