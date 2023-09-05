<?php

/**
 * @file
 * Contains \Drupal\school_management\Plugin\Block\MarketingPageMgtSearchFormBlock.
 */

namespace Drupal\school_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'marketing page' block.
 *
 * @Block(
 *   id = "marketing_page_form",
 *   admin_label = @Translation("Marketing page block"),
 *   category = @Translation("Marketing page block")
 * )
 */
class MarketingPageMgtSearchFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\school_management\Form\MarketingPageForm');

    return $form;
  }

}
