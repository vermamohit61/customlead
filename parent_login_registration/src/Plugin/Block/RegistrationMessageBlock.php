<?php

namespace Drupal\parent_login_registration\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'EnquirymessageBlock' block.
 *
 * @Block(
 *  id = "registration_message_block",
 *  admin_label = @Translation("Registration Message block"),
 * )
 */

class RegistrationMessageBlock extends BlockBase{
    /**
   * {@inheritdoc}
   */
  public function build() {
    $current_path = \Drupal::service('path.current')->getPath();

    if(isset($_SESSION['newly_registered'])){
      $newly_registered = $_SESSION['newly_registered'];
    }
    else{
      $newly_registered = '0';
    }

    $data = array(
      'newly_registered' => $newly_registered,
    );

    return array(
      '#theme' => 'registration_message_block_template',
      '#data'=> $data
    );
  }

  /**
   * Disable the block cache
   */
  public function getCacheMaxAge() {
    return 0;
  }
}