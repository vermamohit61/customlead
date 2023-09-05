<?php

/**
 * @file
 * Contains \Drupal\school_management\Plugin\Block\RecentSearchKeyWordsBlock.
 */

namespace Drupal\school_management\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'custom recent search keywords' block.
 *
 * @Block(
 *   id = "recent_search_keywords_block",
 *   admin_label = @Translation("Recent Search Keywords Block"),
 *   category = @Translation("Recent Search Keywords Block")
 * )
 */
class RecentSearchKeyWordsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'recent_search_keywords_block_template',
      '#data' => show_search_suggestion(),
      '#cache' => [
        'contexts' => ['url.query_args:search',
        'url.query_args:city',
        'url.query_args:state',
        'url.query_args:search',
        'url.query_args:board',
        'url.query_args:grade',
        'url.query_args:pincode',
        'url.query_args:facilty',

      ],
      ],
    );
  }

}
