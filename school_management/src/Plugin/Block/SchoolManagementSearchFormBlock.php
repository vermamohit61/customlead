<?php

/**
 * @file
 * Contains \Drupal\school_management\Plugin\Block\SchoolManagementSearchFormBlock.
 */

namespace Drupal\school_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'custom search' block.
 *
 * @Block(
 *   id = "school_search_form",
 *   admin_label = @Translation("School search block"),
 *   category = @Translation("School search block")
 * )
 */
class SchoolManagementSearchFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\school_management\Form\SchoolSearchForm');

    return $form;
  }
    /**
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
