<?php
/**
 * @file
 * Contains \Drupal\school_management\Plugin\Block\IPtoLocDropdownBlock.
 */

namespace Drupal\school_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'state dropdown' block.
 *
 * @Block(
 *   id = "ip_state_list_block",
 *   admin_label = @Translation("State List block"),
 *   category = @Translation("Custom State List")
 * )
 */
class IPtoLocDropdownBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {

        $form = \Drupal::formBuilder()->getForm('Drupal\school_management\Form\IPtoLocDropdownForm');

        return $form;
    }
      /**
   * {@inheritdoc}
  */
  public function getCacheMaxAge() {
    return 0;
  }
}
