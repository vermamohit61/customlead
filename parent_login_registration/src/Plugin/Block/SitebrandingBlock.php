<?php

namespace Drupal\parent_login_registration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a 'SitebrandingBlock' block.
 *
 * @Block(
 *  id = "sitebranding_block",
 *  admin_label = @Translation("sitebranding block"),
 * )
 */
class SitebrandingBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $roles = \Drupal::currentUser()->getRoles();
    $url = Url::fromRoute('<none>');
    if (in_array('marketing_admin', $roles)) {
      $url = Url::fromRoute('view.marketing_page_list.page_1');      
    }
    if (in_array('content_admin', $roles)) {
      $url = Url::fromRoute('view.banners.page_1');     
    }
    if (in_array('site_admin', $roles)) {
      $url = Url::fromRoute('view.user_admin_people.page_2');     
    }   
    $site = '<a href="'.$url->toString().'" rel="homes" class="site-logo">
    <img src="/themes/custom/leadschool/logo.svg" alt="Home">
    </a>';     
    return array(
      '#markup' => $site,
    );  
  }
}
