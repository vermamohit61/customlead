<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ParentenquiryBlock' block.
 *
 * @Block(
 *  id = "parentenquiry_block",
 *  admin_label = @Translation("Parent Enquiry block"),
 * )
 */
 class ParentenquiryBlock extends BlockBase {

  /**
  * {@inheritdoc}
  */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\parent_login_registration\Form\ParentenquiryForm');
    $form_array_data = array(
      'parent_enquiry_form' => $form
    );
    return array(
      '#theme' => 'parent_enquiryform_template',
      '#data' => $form_array_data
    );
  }

}
