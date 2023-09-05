<?php

namespace Drupal\common_leadschool\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Class ThemechangeThemeNegotiator.
 */
class ThemechangeThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $userRolesArray = \Drupal::currentUser()->getRoles();
    if (in_array("content_admin", $userRolesArray) || in_array("marketing_admin", $userRolesArray) || in_array("site_admin", $userRolesArray))
    {
        return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return 'adminimal_theme';
  }

}
