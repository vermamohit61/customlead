<?php

namespace Drupal\get_in_touch\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'GetintouchBlock' block.
 *
 * @Block(
 *  id = "getintouch_block",
 *  admin_label = @Translation("Getintouch block"),
 * )
 */
 class GetintouchBlock extends BlockBase {

  /**
  * {@inheritdoc}
  */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\get_in_touch\Form\GetintouchForm');
    return $form;
  }

}
